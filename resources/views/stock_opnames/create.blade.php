<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Record Stock Opname') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if($stocks->isEmpty())
                        <div class="rounded-xl bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800 mb-6">
                            ⚠️ Belum ada stok tersedia untuk di-opname. Pastikan transaksi Penerimaan Barang telah disetujui admin terlebih dahulu.
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                            <p class="text-sm font-semibold text-red-700 mb-1">Terjadi kesalahan:</p>
                            <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        // Pre-encode ALL computed stocks for JS
                        $stocksEncoded = json_encode(
                            $stocks->values()->map(fn ($s, $i) => [
                                'idx'                  => $i,
                                'material_id'          => $s->material_id,
                                'material_name_manual' => $s->material_id ? null : $s->material_name,
                                'location'             => $s->location,
                                'stock_qty'            => (int) $s->stock_qty,
                                'label'                => $s->material_name,
                            ])
                        );
                    @endphp

                    <form action="{{ route('stock-opnames.store') }}" method="POST" id="opname-form">
                        @csrf

                        <!-- Material + Location picker -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="stock_pick">
                                Material &amp; Lokasi Penempatan <span class="text-red-500">*</span>
                            </label>
                            <select id="stock_pick"
                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">— Pilih Material &amp; Lokasi —</option>
                                @foreach($stocks as $i => $s)
                                    <option value="{{ $i }}"
                                            {{ old('material_id') == $s->material_id ? 'selected' : '' }}>
                                        {{ $s->material_name }} — {{ $s->location }}
                                        (Stok: {{ number_format((int) $s->stock_qty) }})
                                    </option>
                                @endforeach
                            </select>
                            <p id="pick_error" class="text-red-500 text-xs italic mt-1 hidden">Pilih material terlebih dahulu.</p>
                            @error('material_id') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror

                            {{-- Hidden fields populated by JS --}}
                            <input type="hidden" id="material_id"          name="material_id"          value="{{ old('material_id') }}">
                            <input type="hidden" id="material_name_manual" name="material_name_manual" value="{{ old('material_name_manual') }}">
                            <input type="hidden" id="location"             name="location"             value="{{ old('location') }}">
                        </div>

                        <!-- Location display (readonly) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Penempatan</label>
                            <input id="location_display" type="text" readonly
                                   value="{{ old('location') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-50 leading-tight"
                                   placeholder="Otomatis dari pilihan material">
                        </div>

                        <!-- System Quantity – hidden, submitted via hidden field -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Jumlah Sistem (Stok Tercatat)
                            </label>
                            <input type="text" id="system_quantity_display" readonly
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-100 leading-tight"
                                   placeholder="Terisi otomatis dari pilihan material"
                                   value="{{ old('system_quantity') }}">
                            {{-- Actual submitted field --}}
                            <input type="hidden" id="system_quantity" name="system_quantity" value="{{ old('system_quantity', 0) }}">
                            @error('system_quantity') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Actual Quantity -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="actual_quantity">
                                Jumlah Aktual (Hasil Hitung Fisik) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="actual_quantity" name="actual_quantity" min="0" required
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400"
                                   value="{{ old('actual_quantity') }}">
                            @error('actual_quantity') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Opname Date -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="opname_date">
                                Tanggal Opname <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="opname_date" name="opname_date" required
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400"
                                   value="{{ old('opname_date', date('Y-m-d')) }}">
                            @error('opname_date') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">Catatan</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none transition-colors">
                                Simpan Opname
                            </button>
                            <a class="font-bold text-sm text-blue-600 hover:text-blue-800" href="{{ route('stock-opnames.index') }}">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const stocks = {!! $stocksEncoded !!};

        const pickEl   = document.getElementById('stock_pick');
        const pickErr  = document.getElementById('pick_error');

        function applySelection(idx) {
            if (idx === '' || idx === null) {
                document.getElementById('material_id').value             = '';
                document.getElementById('material_name_manual').value    = '';
                document.getElementById('location').value                = '';
                document.getElementById('location_display').value        = '';
                document.getElementById('system_quantity').value         = 0;
                document.getElementById('system_quantity_display').value = '';
                return;
            }
            const row = stocks[idx];
            if (!row) return;

            // material_id for linked materials, material_name_manual for manual entries
            document.getElementById('material_id').value             = row.material_id !== null ? row.material_id : '';
            document.getElementById('material_name_manual').value    = row.material_id !== null ? '' : (row.material_name_manual || '');
            document.getElementById('location').value                = row.location;
            document.getElementById('location_display').value        = row.location;
            document.getElementById('system_quantity').value         = row.stock_qty;
            document.getElementById('system_quantity_display').value = row.stock_qty;
        }

        pickEl.addEventListener('change', function () {
            applySelection(this.value !== '' ? parseInt(this.value, 10) : '');
            pickErr.classList.add('hidden');
        });

        /* Guard: must pick a material before submit (either by ID or manual name) */
        document.getElementById('opname-form').addEventListener('submit', function (e) {
            const mid   = document.getElementById('material_id').value;
            const mname = document.getElementById('material_name_manual').value;
            if (!mid && !mname) {
                e.preventDefault();
                pickErr.classList.remove('hidden');
                pickEl.focus();
            }
        });

        /* Restore selection on validation error */
        @if(old('material_id'))
            const oldId = "{{ old('material_id') }}";
            stocks.forEach(function (row, idx) {
                if (String(row.material_id) === oldId) {
                    pickEl.value = idx;
                    applySelection(idx);
                }
            });
        @endif
    </script>
</x-app-layout>
