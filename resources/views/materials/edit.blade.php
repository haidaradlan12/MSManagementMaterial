<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Material') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('materials.update', $material) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="material_code">Material Code</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="material_code" name="material_code" type="text" required value="{{ old('material_code', $material->material_code) }}">
                            @error('material_code') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="material_name">Material Name</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="material_name" name="material_name" type="text" required value="{{ old('material_name', $material->material_name) }}">
                            @error('material_name') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="stock_quantity">Stock Quantity</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="stock_quantity" name="stock_quantity" type="number" required min="0" value="{{ old('stock_quantity', $material->stock_quantity) }}">
                            @error('stock_quantity') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="unit">Unit</label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="unit" name="unit" type="text" required value="{{ old('unit', $material->unit) }}">
                            @error('unit') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="remarks">Remarks</label>
                            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="remarks" name="remarks">{{ old('remarks', $material->remarks) }}</textarea>
                            @error('remarks') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                                Update
                            </button>
                            <a class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800" href="{{ route('materials.index') }}">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
