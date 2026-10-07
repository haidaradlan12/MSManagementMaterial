<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Material Management MS MTBU') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-700">Total Material</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $totalMaterials ?? 0 }}</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-700">Total Stock Opname</h3>
                            <p class="text-3xl font-bold text-green-600">{{ $totalOpnames ?? 0 }}</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <a href="{{ route('transactions.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6 text-gray-900 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-700">Pending Transaksi</h3>
                            <p class="text-3xl font-bold text-yellow-500">{{ $totalPending ?? 0 }}</p>
                            <p class="text-xs text-yellow-600 mt-0.5">Klik untuk validasi →</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                    </div>
                </a>
            </div>

            @php
                $aggregatedStock = $stock->groupBy('material_name')->map(function ($items, $name) {
                    return (object) [
                        'material_name' => $name,
                        'stock_qty' => $items->sum('stock_qty')
                    ];
                })->sortBy('material_name')->values();

                $stockGreen = $aggregatedStock->filter(fn($s) => $s->stock_qty > 10);
                $stockYellow = $aggregatedStock->filter(fn($s) => $s->stock_qty <= 10 && $s->stock_qty >= 2);
                $stockRed = $aggregatedStock->filter(fn($s) => $s->stock_qty <= 1);
            @endphp

            <!-- Status Stock Tables -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Tabel Kiri: > 10 (Hijau) -->
                <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg border border-green-200">
                    <div class="p-4 border-b border-green-200 bg-green-100 flex justify-between items-center">
                        <h3 class="font-bold text-green-800">Stock > 10</h3>
                        <span class="text-green-800 text-xs font-bold bg-green-200 px-2 py-1 rounded-full">{{ $stockGreen->count() }} Item</span>
                    </div>
                    <div class="p-4 h-64 overflow-y-auto">
                        <ul class="space-y-2">
                            @forelse($stockGreen as $s)
                                <li class="flex justify-between items-center text-sm border-b border-green-100 pb-2 last:border-0 last:pb-0">
                                    <span class="text-green-900 font-medium">{{ $s->material_name }}</span>
                                    <span class="bg-green-200 text-green-800 py-0.5 px-2 rounded-full text-xs font-bold">{{ number_format($s->stock_qty) }}</span>
                                </li>
                            @empty
                                <li class="text-green-600 text-sm text-center py-4">Tidak ada material</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Tabel Tengah: 2 - 10 (Kuning) -->
                <div class="bg-yellow-50 overflow-hidden shadow-sm sm:rounded-lg border border-yellow-200">
                    <div class="p-4 border-b border-yellow-200 bg-yellow-100 flex justify-between items-center">
                        <h3 class="font-bold text-yellow-800">Stock 2 - 10</h3>
                        <span class="text-yellow-800 text-xs font-bold bg-yellow-200 px-2 py-1 rounded-full">{{ $stockYellow->count() }} Item</span>
                    </div>
                    <div class="p-4 h-64 overflow-y-auto">
                        <ul class="space-y-2">
                            @forelse($stockYellow as $s)
                                <li class="flex justify-between items-center text-sm border-b border-yellow-100 pb-2 last:border-0 last:pb-0">
                                    <span class="text-yellow-900 font-medium">{{ $s->material_name }}</span>
                                    <span class="bg-yellow-200 text-yellow-800 py-0.5 px-2 rounded-full text-xs font-bold">{{ number_format($s->stock_qty) }}</span>
                                </li>
                            @empty
                                <li class="text-yellow-600 text-sm text-center py-4">Tidak ada material</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Tabel Kanan: <= 1 (Merah) -->
                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg border border-red-200">
                    <div class="p-4 border-b border-red-200 bg-red-100 flex justify-between items-center">
                        <h3 class="font-bold text-red-800">Stock 0 - 1</h3>
                        <span class="text-red-800 text-xs font-bold bg-red-200 px-2 py-1 rounded-full">{{ $stockRed->count() }} Item</span>
                    </div>
                    <div class="p-4 h-64 overflow-y-auto">
                        <ul class="space-y-2">
                            @forelse($stockRed as $s)
                                <li class="flex justify-between items-center text-sm border-b border-red-100 pb-2 last:border-0 last:pb-0">
                                    <span class="text-red-900 font-medium">{{ $s->material_name }}</span>
                                    <span class="bg-red-200 text-red-800 py-0.5 px-2 rounded-full text-xs font-bold">{{ number_format($s->stock_qty) }}</span>
                                </li>
                            @empty
                                <li class="text-red-600 text-sm text-center py-4">Tidak ada material</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ── Overview Material System dan Actual ── --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Overview Material System dan Actual</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Qty Sistem dari stok aktual · Qty Aktual & Status dari opname terakhir per lokasi</p>
                    </div>
                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded-full">{{ $overviewMaterial->count() }} item</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-5 py-3">Nama Material</th>
                                <th class="px-5 py-3">Lokasi Penyimpanan</th>
                                <th class="px-5 py-3 text-center">System Qty</th>
                                <th class="px-5 py-3 text-center">Actual Qty</th>
                                <th class="px-5 py-3 text-center">Status Sebelum</th>
                                <th class="px-5 py-3 text-center">Status Sekarang</th>
                                <th class="px-5 py-3 text-center">Tgl Opname Terakhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($overviewMaterial as $row)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 font-semibold text-gray-900">{{ $row->material_name }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $row->location }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                            {{ $row->system_qty > 10 ? 'bg-green-100 text-green-700' : ($row->system_qty > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ number_format($row->system_qty) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if($row->actual_qty !== null)
                                            <span class="text-gray-800 font-medium">{{ number_format($row->actual_qty) }}</span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if($row->status === 'matched')
                                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">✓ Matched</span>
                                        @elseif($row->status === 'surplus')
                                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">↑ Surplus (+{{ $row->difference }})</span>
                                        @elseif($row->status === 'missing')
                                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">↓ Missing ({{ $row->difference }})</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-orange-100 text-orange-600 text-xs font-medium rounded-full">⚠ Perlu dilakukan stock opname</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if($row->actual_qty !== null && $row->system_qty != $row->actual_qty && in_array($row->status, ['matched', 'surplus', 'missing']))
                                            <span class="px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Jangan lupa opname lagi</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center text-xs text-gray-500">
                                        @if($row->last_opname_at)
                                            {{ \Carbon\Carbon::parse($row->last_opname_at)->format('d M Y') }}
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada data stok material.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Latest Stock Opname Activity ── --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Latest Stock Opname Activity</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Material</th>
                                    <th class="px-4 py-3">System Qty</th>
                                    <th class="px-4 py-3">Actual Qty</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestOpnames ?? [] as $opname)
                                    <tr class="border-b">
                                        <td class="px-4 py-3">{{ $opname->opname_date }}</td>
                                        <td class="px-4 py-3">{{ $opname->material_label }}</td>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-center text-gray-500">No recent activity found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
