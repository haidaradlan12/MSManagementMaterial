<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Opname') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- ── Belum Di-Opname ── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 animate-pulse"></span>
                        <h3 class="font-bold text-gray-800">Belum Di-Opname</h3>
                    </div>
                    <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full">
                        {{ $notOpnamed->count() }} item
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-5 py-3">Nama Material</th>
                                <th class="px-5 py-3">Lokasi Penyimpanan</th>
                                <th class="px-5 py-3 text-center">Stok Sistem</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($notOpnamed as $row)
                                <tr class="hover:bg-red-50 transition-colors">
                                    <td class="px-5 py-3 font-semibold text-gray-900">{{ $row->material_name }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $row->location }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                            {{ $row->stock_qty > 10 ? 'bg-green-100 text-green-700' : ($row->stock_qty > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ number_format($row->stock_qty) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <a href="{{ route('stock-opnames.create') }}"
                                           class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-colors inline-block">
                                            + Opname Sekarang
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm font-medium">Semua item sudah pernah di-opname! 🎉</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Rekomendasi Di-Opname Ulang ── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-400 animate-pulse"></span>
                        <h3 class="font-bold text-gray-800">Rekomendasi Di-Opname Ulang</h3>
                        <span class="text-xs text-gray-400 flex-1 hidden md:inline">(opname terakhir surplus / missing atau stok berubah sejak opname)</span>
                    </div>
                    <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2.5 py-1 rounded-full">
                        {{ $recommendedOpname->count() }} item
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-5 py-3">Nama Material</th>
                                <th class="px-5 py-3">Lokasi Penyimpanan</th>
                                <th class="px-5 py-3 text-center">Stok Sistem</th>
                                <th class="px-5 py-3 text-center">Status Terakhir</th>
                                <th class="px-5 py-3 text-center">Tgl Opname Terakhir</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recommendedOpname as $row)
                                <tr class="hover:bg-orange-50 transition-colors">
                                    <td class="px-5 py-3 font-semibold text-gray-900">{{ $row->material_name }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $row->location }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                            {{ $row->stock_qty > 10 ? 'bg-green-100 text-green-700' : ($row->stock_qty > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ number_format($row->stock_qty) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if($row->last_status === 'surplus')
                                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full mb-1 inline-block">
                                                ↑ Surplus (+{{ $row->last_diff }})
                                            </span>
                                        @elseif($row->last_status === 'missing')
                                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full mb-1 inline-block">
                                                ↓ Missing ({{ $row->last_diff }})
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full mb-1 inline-block">
                                                ✓ Matched
                                            </span>
                                        @endif

                                        @if($row->stock_qty != $row->actual_qty)
                                            <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full inline-block mt-1">
                                                (Opname Lagi)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($row->last_opname_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <a href="{{ route('stock-opnames.create') }}"
                                           class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition-colors inline-block">
                                            + Opname Ulang
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm font-medium">Semua opname terakhir sudah sesuai! ✅</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Opname History ── --}}

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-800">Opname History</h3>
                        <a href="{{ route('stock-opnames.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg transition-colors">
                            + Record Opname
                        </a>
                    </div>
                    {{-- Filter tanggal --}}
                    <form action="{{ route('stock-opnames.index') }}" method="GET"
                          class="flex flex-col sm:flex-row items-end gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
                        <div class="flex flex-col gap-1 w-full sm:w-auto">
                            <label class="text-xs font-semibold text-gray-600">Dari Tanggal</label>
                            <input type="date" name="date_from"
                                   value="{{ request('date_from') }}"
                                   class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                        </div>
                        <div class="flex flex-col gap-1 w-full sm:w-auto">
                            <label class="text-xs font-semibold text-gray-600">Sampai Tanggal</label>
                            <input type="date" name="date_to"
                                   value="{{ request('date_to') }}"
                                   class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                        </div>
                        <button type="submit"
                                class="flex items-center gap-2 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                            </svg>
                            Filter
                        </button>
                        @if(request()->hasAny(['date_from', 'date_to']))
                            <a href="{{ route('stock-opnames.index') }}"
                               class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>


                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-5 py-3">Tanggal</th>
                                <th class="px-5 py-3">Material</th>
                                <th class="px-5 py-3">Lokasi</th>
                                <th class="px-5 py-3 text-center">Sistem</th>
                                <th class="px-5 py-3 text-center">Aktual</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($stockOpnames as $opname)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <div class="text-xs text-gray-700 font-medium">{{ \Carbon\Carbon::parse($opname->opname_date)->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-5 py-3 font-medium text-gray-900">{{ $opname->material_label }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $opname->location ?? '—' }}</td>
                                    <td class="px-5 py-3 text-center">{{ $opname->system_quantity }}</td>
                                    <td class="px-5 py-3 text-center">{{ $opname->actual_quantity }}</td>
                                    <td class="px-5 py-3 text-center">
                                        @if($opname->status == 'matched')
                                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">✓ Matched</span>
                                        @elseif($opname->status == 'surplus')
                                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">↑ Surplus (+{{ $opname->difference }})</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">↓ Missing ({{ $opname->difference }})</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('stock-opnames.edit', $opname) }}" class="text-blue-600 hover:text-blue-900 text-xs font-medium mr-3">Edit</a>
                                        <form action="{{ route('stock-opnames.destroy', $opname) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium" onclick="return confirm('Hapus data opname ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $stockOpnames->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
