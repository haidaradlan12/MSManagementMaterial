<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Validasi Transaksi Barang') }}
        </h2>
    </x-slot>

    {{-- Modal Lihat Gambar --}}
    <div id="photo-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4" style="display:none">
        <div class="bg-gray-100 border border-gray-200 rounded-2xl shadow-2xl w-full p-4 mx-auto" style="max-width: 500px;">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-gray-700 text-sm">Foto Bukti Transaksi</h3>
                <button onclick="closePhotoModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            <div class="mt-2 text-center">
                <img id="photo-modal-img" src="" alt="Foto Bukti" class="w-full rounded-xl object-contain bg-gray-200 mx-auto" style="max-height: 65vh; width: auto;">
            </div>
        </div>
    </div>

    {{-- Modal Setujui dengan Divisi --}}
    <div id="approve-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm p-4" style="display:none">
        <div class="bg-green-50 border border-green-200 rounded-2xl shadow-2xl w-full p-5 mx-auto" style="max-width: 380px;">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-green-800 text-sm">Setujui Transaksi</h3>
                </div>
                <button onclick="closeApproveModal()" class="text-green-400 hover:text-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-green-700 mb-3">Pilih divisi yang bertanggung jawab.</p>
            <form id="approve-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-green-800 mb-1">Divisi <span class="text-red-500">*</span></label>
                    <select name="division" id="approve-division-select" required
                            class="w-full border border-green-300 bg-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:border-green-400 outline-none">
                        <option value="">— Pilih Divisi —</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div }}">{{ $div }}</option>
                        @endforeach
                    </select>
                    <p id="division-error" class="hidden text-red-500 text-xs mt-1">Divisi wajib dipilih.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="closeApproveModal()"
                            class="flex-1 px-3 py-2 border border-green-300 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition-colors">
                        ✓ Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

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

            <!-- ── Pending Transactions ── -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                        <h3 class="font-bold text-gray-800">Menunggu Validasi Admin</h3>
                    </div>
                    <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2.5 py-1 rounded-full">
                        {{ $pending->count() }} pengajuan
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-5 py-3">Jenis</th>
                                <th class="px-5 py-3">Nama Barang</th>
                                <th class="px-5 py-3">Jumlah</th>
                                <th class="px-5 py-3">Lokasi</th>
                                <th class="px-5 py-3">Nama</th>
                                <th class="px-5 py-3">Kegunaan</th>
                                <th class="px-5 py-3">Foto</th>
                                <th class="px-5 py-3">Tgl Pengajuan</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pending as $tx)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        @if($tx->type === 'receive')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                                ↑ Terima
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">
                                                ↓ Ambil
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 font-medium text-gray-900">{{ $tx->material_label }}</td>
                                    <td class="px-5 py-3">{{ number_format($tx->quantity) }}</td>
                                    <td class="px-5 py-3">{{ $tx->location }}</td>
                                    <td class="px-5 py-3">{{ $tx->person_name }}</td>
                                    <td class="px-5 py-3 {{ $tx->type === 'receive' ? 'text-gray-400' : '' }}">{{ $tx->type === 'receive' ? '-' : ($tx->purpose ?: '-') }}</td>
                                    <td class="px-5 py-3">
                                        @if($tx->photo_path)
                                            <button type="button"
                                                    onclick="openPhotoModal('{{ asset('storage_public/' . $tx->photo_path) }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold rounded-lg transition-colors">
                                                🖼 Lihat
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <div class="text-xs text-gray-700 font-medium">{{ $tx->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $tx->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button"
                                                    onclick="openApproveModal('{{ route('transactions.approve', $tx) }}')"
                                                    class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                                ✓ Setuju
                                            </button>
                                            <form action="{{ route('transactions.reject', $tx) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold rounded-lg transition-colors"
                                                        onclick="return confirm('Tolak pengajuan ini?')">
                                                    ✗ Tolak
                                                </button>
                                            </form>
                                            <form action="{{ route('transactions.destroy', $tx) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-colors"
                                                        onclick="return confirm('Hapus pengajuan ini selamanya?')">
                                                    🗑 Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-5 py-8 text-center text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Tidak ada pengajuan yang menunggu validasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── History ── -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-3">
                        <h3 class="font-bold text-gray-800">Riwayat Transaksi</h3>
                    </div>
                    <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        {{-- Filter Jenis --}}
                        <select name="type" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5">
                            <option value="">Semua Jenis</option>
                            <option value="receive" {{ request('type') === 'receive' ? 'selected' : '' }}>Terima</option>
                            <option value="take" {{ request('type') === 'take' ? 'selected' : '' }}>Ambil</option>
                        </select>

                        {{-- Filter Nama Barang --}}
                        <input type="text" name="material_name" value="{{ request('material_name') }}"
                               placeholder="Cari Nama Barang..."
                               class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5 w-full">

                        {{-- Filter Divisi --}}
                        <select name="division" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5">
                            <option value="">Semua Divisi</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div }}" {{ request('division') === $div ? 'selected' : '' }}>{{ $div }}</option>
                            @endforeach
                        </select>
                        {{-- Pilihan Jenis Tanggal --}}
                        <select name="date_type" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5" title="Jenis Tanggal">
                            <option value="created_at" {{ request('date_type', 'created_at') === 'created_at' ? 'selected' : '' }}>Tgl Pengajuan</option>
                            <option value="updated_at" {{ request('date_type') === 'updated_at' ? 'selected' : '' }}>Tgl Validasi</option>
                        </select>

                        {{-- Filter Tanggal Dari --}}
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5"
                               title="Dari Tanggal">

                        {{-- Filter Tanggal Sampai --}}
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5"
                               title="Sampai Tanggal">

                        {{-- Tombol --}}
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                Filter
                            </button>
                            @if(request()->hasAny(['type', 'material_name', 'division', 'date_from', 'date_to']))
                                <a href="{{ route('transactions.index') }}" class="flex-1 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg transition-colors text-center inline-flex items-center justify-center">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-5 py-3">Jenis</th>
                                <th class="px-5 py-3">Nama Barang</th>
                                <th class="px-5 py-3">Jumlah</th>
                                <th class="px-5 py-3">Lokasi</th>
                                <th class="px-5 py-3">Nama</th>
                                <th class="px-5 py-3">Kegunaan</th>
                                <th class="px-5 py-3">Divisi</th>
                                <th class="px-5 py-3">Foto</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Tgl Pengajuan</th>
                                <th class="px-5 py-3">Tgl Validasi</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($history as $tx)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        @if($tx->type === 'receive')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded-full">↑ Terima</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-orange-50 text-orange-600 text-xs font-medium rounded-full">↓ Ambil</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 font-medium text-gray-900">{{ $tx->material_label }}</td>
                                    <td class="px-5 py-3">{{ number_format($tx->quantity) }}</td>
                                    <td class="px-5 py-3">{{ $tx->location }}</td>
                                    <td class="px-5 py-3">{{ $tx->person_name }}</td>
                                    <td class="px-5 py-3">{{ $tx->type === 'receive' ? '-' : ($tx->purpose ?: '-') }}</td>
                                    {{-- Divisi --}}
                                    <td class="px-5 py-3">
                                        @if($tx->division)
                                            <span class="px-2 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-lg">{{ $tx->division }}</span>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                    {{-- Foto --}}
                                    <td class="px-5 py-3">
                                        @if($tx->photo_path)
                                            <button type="button"
                                                    onclick="openPhotoModal('{{ asset('storage_public/' . $tx->photo_path) }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold rounded-lg transition-colors">
                                                🖼 Lihat
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        @if($tx->status === 'approved')
                                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">✓ Disetujui</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">✗ Ditolak</span>
                                        @endif
                                    </td>
                                    {{-- Tanggal Pengajuan --}}
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <div class="text-xs text-gray-700 font-medium">{{ $tx->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $tx->created_at->format('H:i') }}</div>
                                    </td>
                                    {{-- Tanggal Validasi --}}
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        @if($tx->validated_at)
                                            <div class="text-xs text-gray-700 font-medium">{{ $tx->validated_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $tx->validated_at->format('H:i') }}</div>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <form action="{{ route('transactions.destroy', $tx) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-400 hover:text-red-600 text-xs font-medium transition-colors"
                                                    onclick="return confirm('Hapus riwayat ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-5 py-8 text-center text-gray-400">Belum ada riwayat transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($history->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        /* ── Modal Foto ─────────────────────────────────────────── */
        function openPhotoModal(url) {
            var modal = document.getElementById('photo-modal');
            document.getElementById('photo-modal-img').src = url;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closePhotoModal() {
            document.getElementById('photo-modal').style.display = 'none';
            document.getElementById('photo-modal-img').src = '';
            document.body.style.overflow = '';
        }
        document.getElementById('photo-modal').addEventListener('click', function (e) {
            if (e.target === this) closePhotoModal();
        });

        /* ── Modal Approve dengan Divisi ────────────────────────── */
        function openApproveModal(actionUrl) {
            document.getElementById('approve-form').action = actionUrl;
            document.getElementById('approve-division-select').value = '';
            document.getElementById('division-error').classList.add('hidden');
            document.getElementById('approve-modal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeApproveModal() {
            document.getElementById('approve-modal').style.display = 'none';
            document.body.style.overflow = '';
        }
        document.getElementById('approve-modal').addEventListener('click', function (e) {
            if (e.target === this) closeApproveModal();
        });
        document.getElementById('approve-form').addEventListener('submit', function (e) {
            const division = document.getElementById('approve-division-select').value;
            if (!division) {
                e.preventDefault();
                document.getElementById('division-error').classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
