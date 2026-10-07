<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Stock Opname') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('stock-opnames.update', $stockOpname) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="material_id">Material</label>
                            <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="material_id" name="material_id" required onchange="updateSystemQty(this)">
                                <option value="">Select Material</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}" data-qty="{{ $material->stock_quantity }}" {{ old('material_id', $stockOpname->material_id) == $material->id ? 'selected' : '' }}>
                                        {{ $material->material_code }} - {{ $material->material_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('material_id') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="system_quantity">System Quantity</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100" id="system_quantity" name="system_quantity" type="number" required readonly value="{{ old('system_quantity', $stockOpname->system_quantity) }}">
                            @error('system_quantity') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="actual_quantity">Actual Quantity</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="actual_quantity" name="actual_quantity" type="number" required min="0" value="{{ old('actual_quantity', $stockOpname->actual_quantity) }}">
                            @error('actual_quantity') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="opname_date">Opname Date</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="opname_date" name="opname_date" type="date" required value="{{ old('opname_date', $stockOpname->opname_date) }}">
                            @error('opname_date') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">Notes</label>
                            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="notes" name="notes">{{ old('notes', $stockOpname->notes) }}</textarea>
                            @error('notes') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Update
                            </button>
                            <a class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800" href="{{ route('stock-opnames.index') }}">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function updateSystemQty(select) {
            var selectedOption = select.options[select.selectedIndex];
            var qty = selectedOption.getAttribute('data-qty');
            if(qty !== null) {
                document.getElementById('system_quantity').value = qty;
            } else {
                document.getElementById('system_quantity').value = '';
            }
        }
    </script>
</x-app-layout>
