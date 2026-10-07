<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stok Material') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Info bar -->
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Stok dihitung dari transaksi <strong>Terima</strong> &amp; <strong>Ambil</strong> yang telah disetujui admin.
                </p>
            </div>

            @if (session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Stock Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h3 class="font-bold text-gray-800">Daftar Stok Material per Lokasi</h3>
                        <!-- Filter input -->
                        <div class="relative w-full sm:w-72">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                            </svg>
                            <input type="text" id="material-filter"
                                   placeholder="Filter nama material..."
                                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                    </div>
                    <div class="mt-1">
                        <span class="text-xs text-gray-400" id="row-count">{{ $stocks->count() }} entri lokasi aktif</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600" id="stock-table">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-5 py-3">Nama Material</th>
                                <th class="px-5 py-3 text-center">Stok QTY</th>
                                <th class="px-5 py-3">Lokasi Penyimpanan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="stock-body">
                            @forelse($stocks as $row)
                                <tr class="stock-row hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 font-semibold text-gray-900 material-name-cell">
                                        {{ $row->material_name }}
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-bold
                                            {{ $row->stock_qty > 10 ? 'bg-green-100 text-green-700' : ($row->stock_qty > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ number_format($row->stock_qty) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 text-gray-700">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $row->location }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-row">
                                    <td colspan="3" class="px-5 py-10 text-center">
                                        <div class="flex flex-col items-center gap-2 text-gray-400">
                                            <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="text-sm">Belum ada stok tersedia.</p>
                                            <p class="text-xs">Stok akan muncul setelah transaksi Terima Barang disetujui admin.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- No filter results -->
                    <div id="no-filter-result" class="hidden px-5 py-8 text-center text-sm text-gray-400">
                        Tidak ada material yang cocok dengan filter "<span id="filter-keyword"></span>".
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        (function () {
            const filterInput   = document.getElementById('material-filter');
            const rows          = document.querySelectorAll('#stock-body .stock-row');
            const rowCount      = document.getElementById('row-count');
            const noResult      = document.getElementById('no-filter-result');
            const filterKeyword = document.getElementById('filter-keyword');
            const total         = rows.length;

            filterInput.addEventListener('input', function () {
                const q       = this.value.trim().toLowerCase();
                let   visible = 0;

                rows.forEach(function (row) {
                    const name = row.querySelector('.material-name-cell').textContent.trim().toLowerCase();
                    const show = !q || name.includes(q);
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                rowCount.textContent = visible + ' entri lokasi aktif';

                if (q && visible === 0) {
                    filterKeyword.textContent = filterInput.value.trim();
                    noResult.classList.remove('hidden');
                } else {
                    noResult.classList.add('hidden');
                }
            });
        })();
    </script>
</x-app-layout>
