<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Barang – SO Material</title>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tab-btn.active  { background: #2563eb; color: #fff; }
        .tab-orng.active { background: #f97316; color: #fff; }
        .glass { backdrop-filter: blur(16px); background: rgba(255,255,255,.88); }
        .ts-wrapper .ts-control  { border-radius:.75rem!important; border-color:#e5e7eb!important; padding:.5rem .75rem!important; }
        .ts-wrapper.focus .ts-control { border-color:#3b82f6!important; box-shadow:0 0 0 2px rgba(59,130,246,.25)!important; }
        .ts-dropdown { border-radius:.75rem!important; border-color:#e5e7eb!important; box-shadow:0 4px 16px rgba(0,0,0,.10)!important; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">

@php
    // Pre-encode for JS – avoids Blade @json() comma-split bug
    $receiveStocksJson = json_encode(
        $materials->map(fn ($m) => [
            'id'    => $m->id,
            'label' => ($m->material_code ? '[' . $m->material_code . '] ' : '') . $m->material_name,
        ])->values()
    );

    $takeStocksJson = json_encode(
        $stocks->values()->map(fn ($s, $i) => [
            'idx'          => $i,
            'material_id'  => $s->material_id,
            'material_name'=> $s->material_name,
            'location'     => $s->location,
            'stock_qty'    => (int) $s->stock_qty,
            'label'        => $s->material_name . ' — ' . $s->location . ' (Stok: ' . number_format((int) $s->stock_qty) . ')',
        ])
    );
@endphp

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-gray-800 leading-tight">Portal Barang</h1>
                    <p class="text-xs text-gray-500">SO Material Management</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="text-xs text-blue-600 hover:underline font-medium">Login Admin →</a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8">

        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 shadow-sm">
                <svg class="w-5 h-5 mt-0.5 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Tabs -->
        <div class="flex gap-2 mb-6 bg-gray-100 p-1 rounded-xl">
            <button id="tab-receive" onclick="switchTab('receive')"
                    class="tab-btn active flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Terima Barang
            </button>
            <button id="tab-take" onclick="switchTab('take')"
                    class="tab-btn tab-orng flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold text-gray-600 transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Ambil Barang
            </button>
        </div>

        <!-- ═══ FORM TERIMA BARANG ═══ -->
        <div id="form-receive">
            <div class="glass rounded-2xl shadow-lg border border-white/60 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-1">Form Penerimaan Barang</h2>
                <p class="text-sm text-gray-500 mb-6">Isi data di bawah ini. Pengajuan akan divalidasi oleh admin.</p>

                <form action="{{ route('transactions.public.store') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="type" value="receive">

                    <!-- Nama Barang – manual only -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Barang <span class="text-red-500">*</span></label>
                        <input type="text" name="material_name_manual" id="receive_material_name"
                               placeholder="Ketik nama barang yang diterima..."
                               value="{{ old('material_name_manual') }}" required
                               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                        @error('material_name_manual') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah Barang</label>
                            <input type="number" name="quantity" min="1" required value="{{ old('quantity') }}" placeholder="0"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                            @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Penempatan</label>
                            <input type="text" name="location" required value="{{ old('location') }}" placeholder="Gudang A, Rak 2..."
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                            @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Penerima</label>
                        <input type="text" name="person_name" required value="{{ old('person_name') }}" placeholder="Nama lengkap penerima..."
                               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                        @error('person_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold py-3 rounded-xl transition-all duration-150 flex items-center justify-center gap-2 shadow-md shadow-blue-200">
                        Kirim Pengajuan Penerimaan
                    </button>
                </form>
            </div>
        </div>

        <!-- ═══ FORM AMBIL BARANG ═══ -->
        <div id="form-take" class="hidden">
            <div class="glass rounded-2xl shadow-lg border border-white/60 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-1">Form Pengambilan Barang</h2>
                <p class="text-sm text-gray-500 mb-6">Pilih barang yang tersedia. Lokasi otomatis terisi. Pengajuan divalidasi admin.</p>

                @if($stocks->isEmpty())
                    <div class="rounded-xl bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800 mb-4">
                        ⚠️ Belum ada stok barang tersedia. Pengajuan penerimaan barang harus disetujui admin terlebih dahulu.
                    </div>
                @endif

                <form action="{{ route('transactions.public.store') }}" method="POST" id="take-form" novalidate>
                    @csrf
                    <input type="hidden" name="type" value="take">
                    <!-- Populated by JS -->
                    <input type="hidden" id="take_material_id"   name="material_id">
                    <input type="hidden" id="take_material_name" name="material_name_manual">
                    <input type="hidden" id="take_location_val"  name="location">

                    <!-- Dropdown barang+lokasi -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nama Barang &amp; Lokasi
                        </label>
                        <select id="take_stock_select" class="take-select w-full">
                            <option value="">— Cari barang yang tersedia —</option>
                            @foreach($stocks as $i => $s)
                                <option value="{{ $i }}"
                                        data-material-id="{{ $s->material_id }}"
                                        data-material-name="{{ $s->material_name }}"
                                        data-location="{{ $s->location }}"
                                        data-qty="{{ (int) $s->stock_qty }}">
                                    {{ $s->material_name }} — {{ $s->location }}
                                    (Stok: {{ number_format((int) $s->stock_qty) }})
                                </option>
                            @endforeach
                        </select>
                        @error('material_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Lokasi (readonly) -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Pengambilan</label>
                        <div class="relative">
                            <input type="text" id="take_location_display" readonly
                                   placeholder="Otomatis dari pilihan barang..."
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pr-8 text-sm bg-gray-50 text-gray-500 outline-none cursor-default">
                            <svg class="w-4 h-4 text-gray-300 absolute right-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Info stok -->
                    <div id="take_stock_info" class="hidden mb-4">
                        <div class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-3 py-1.5 text-xs font-medium">
                            Stok tersedia: <span id="take_stock_qty" class="font-bold ml-0.5"></span> unit
                        </div>
                    </div>

                    <!-- Jumlah -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah Barang</label>
                        <input type="number" name="quantity" min="1" id="take_qty" required placeholder="0"
                               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                        @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kegunaan -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kegunaan <span class="text-orange-500">*</span></label>
                        <input type="text" name="purpose" required placeholder="Digunakan untuk..."
                               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                        @error('purpose') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nama Pengambil -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Pengambil</label>
                        <input type="text" name="person_name" required placeholder="Nama lengkap pengambil..."
                               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                        @error('person_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" id="take_submit_btn"
                            class="w-full bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-semibold py-3 rounded-xl transition-all duration-150 flex items-center justify-center gap-2 shadow-md shadow-orange-200">
                        Kirim Pengajuan Pengambilan
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-8">
            Pengajuan Anda akan diproses oleh admin. Hubungi petugas jika ada pertanyaan.
        </p>
    </main>

    <script>
        /* ─── Tab switch ─────────────────────────────────────────── */
        function switchTab(tab) {
            const isReceive = (tab === 'receive');
            document.getElementById('form-receive').classList.toggle('hidden', !isReceive);
            document.getElementById('form-take').classList.toggle('hidden', isReceive);
            document.getElementById('tab-receive').classList.toggle('active', isReceive);
            document.getElementById('tab-receive').classList.toggle('text-gray-600', !isReceive);
            document.getElementById('tab-take').classList.toggle('active', !isReceive);
            document.getElementById('tab-take').classList.toggle('text-gray-600', isReceive);
        }



        /* ─── Take – stock data (safe: pre-encoded in PHP) ───────── */
        const takeStocks = {!! $takeStocksJson !!};

        function fillTakeFields(idx) {
            if (idx === '' || idx === null || idx === undefined) {
                document.getElementById('take_material_id').value    = '';
                document.getElementById('take_material_name').value  = '';
                document.getElementById('take_location_val').value   = '';
                document.getElementById('take_location_display').value = '';
                document.getElementById('take_stock_info').classList.add('hidden');
                document.getElementById('take_qty').removeAttribute('max');
                return;
            }
            const row = takeStocks[idx];
            if (!row) return;

            /* material identifier – use ID when available, fall back to name */
            document.getElementById('take_material_id').value   = row.material_id !== null ? row.material_id : '';
            document.getElementById('take_material_name').value = row.material_id !== null ? '' : row.material_name;

            document.getElementById('take_location_val').value      = row.location;
            document.getElementById('take_location_display').value  = row.location;
            document.getElementById('take_stock_qty').textContent   = row.stock_qty;
            document.getElementById('take_stock_info').classList.remove('hidden');
            document.getElementById('take_qty').setAttribute('max', row.stock_qty);
        }

        /* Initialize Tom Select on take dropdown */
        (function () {
            const ts = new TomSelect('#take_stock_select', {
                allowEmptyOption: true,
                placeholder: 'Cari nama barang & lokasi...',
                maxOptions: 300,
            });
            ts.on('change', function (val) { fillTakeFields(val === '' ? null : parseInt(val, 10)); });
        })();

        /* Guard: prevent submit if no stock selected */
        document.getElementById('take-form').addEventListener('submit', function (e) {
            const mid  = document.getElementById('take_material_id').value;
            const mname = document.getElementById('take_material_name').value;
            if (!mid && !mname) {
                e.preventDefault();
                alert('Silakan pilih barang terlebih dahulu.');
            }
        });

        /* ─── Restore tab on validation error ────────────────────── */
        @if(old('type') === 'take')
            switchTab('take');
        @endif
    </script>
</body>
</html>
