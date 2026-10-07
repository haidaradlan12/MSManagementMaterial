<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Opname') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700">Opname History</h3>
                        <a href="{{ route('stock-opnames.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Record Opname
                        </a>
                    </div>
                    
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Material</th>
                                    <th class="px-4 py-3">System</th>
                                    <th class="px-4 py-3">Actual</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockOpnames as $opname)
                                    <tr class="border-b">
                                        <td class="px-4 py-3">{{ $opname->opname_date }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $opname->material_label }}</td>
                                        <td class="px-4 py-3">{{ $opname->system_quantity }}</td>
                                        <td class="px-4 py-3">{{ $opname->actual_quantity }}</td>
                                        <td class="px-4 py-3">
                                            @if($opname->status == 'matched')
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Matched</span>
                                            @elseif($opname->status == 'surplus')
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Surplus (+{{ $opname->difference }})</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Missing ({{ $opname->difference }})</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('stock-opnames.edit', $opname) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                            <form action="{{ route('stock-opnames.destroy', $opname) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $stockOpnames->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
