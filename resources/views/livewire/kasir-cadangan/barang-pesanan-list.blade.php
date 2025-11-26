@script
    <script>
        Alpine.data('barangPesananList', () => ({
            refs: [],
            temp: {},
            persen_resep: $wire.entangle('persen_resep'),
            mode_kasir: $wire.entangle('mode_kasir'),
            isEditBarang: {},
            barang_pesanan: $wire.entangle('barang_pesanan_kasir'),
            ceklistDeleteBarang: $wire.entangle('ceklistDeleteBarang'),
            list_total_jumlah_dalam_satuan_utama: $wire.entangle('list_total_jumlah_dalam_satuan_utama'),
            barang_pesanan_kasir: $wire.entangle('barang_pesanan_kasir'),
            promo_pesanan_kasir: $wire.entangle('promo_pesanan_kasir'),
            jumlah_barang_input: $wire.entangle('jumlah_barang_input'),
            jumlah_barang_input_dalam_satuan_utama: $wire.entangle('jumlah_barang_input_dalam_satuan_utama'),
            total_jumlah_dalam_satuan_utama_by_barang: $wire.entangle('total_jumlah_dalam_satuan_utama_by_barang'),
            promo: {},
            id_member: $wire.entangle('id_member'),
            getTotalJumlahDalamSatuanUtama(id_barang) {
                return this.list_total_jumlah_dalam_satuan_utama[`${id_barang}`] ?? 0;
            },
            setPromo() {
                this.promo_pesanan_kasir.forEach((promo_pesanan_kasir) => {
                    const idBarang = promo_pesanan_kasir.promo_barang?.id_barang;
                    if (!idBarang) return;
                    this.promo[idBarang] = promo_pesanan_kasir;
                });
            },
            combinedPromoAndBarangPesanan() {
                this.barang_pesanan.sort((a, b) => {
                    // Convert updated_at to Date
                    let dateA = new Date(a.updated_at);
                    let dateB = new Date(b.updated_at);

                    // Urutkan updated_at DESC (yang terbaru duluan)
                    if (dateA < dateB) return -1;
                    if (dateA > dateB) return 1;

                    // Jika updated_at sama → urutkan berdasarkan id_barang ASC
                    if (a.id_barang < b.id_barang) return -1;
                    if (a.id_barang > b.id_barang) return 1;

                    return 0;
                });
                this.barang_pesanan.forEach((item, index) => {
                    const next = this.barang_pesanan[index + 1];
                    // Default tanpa promo
                    item.promo_barang = null;
                    this.jumlah_barang_input = {
                        ...this.jumlah_barang_input,
                        [item.id]: item.jumlah,
                    }
                    this.isEditBarang = {
                        ...this.isEditBarang,
                        [item.id]: false,
                    }
                    // Jika item terakhir untuk id_barang
                    if (!next || next.id_barang !== item.id_barang) {
                        item.promo_barang = this.promo[item.id_barang] ?? null;
                    }
                });
                console.log(this.barang_pesanan);
            },
            editPesananBarang(id_barang_pesanan) {
                this.isEditBarang[id_barang_pesanan] = true;
                this.temp[id_barang_pesanan] = {
                    ...this.barang_pesanan.filter((item) => item.id === id_barang_pesanan)[0]
                };
                this.$nextTick(() => {
                    this.refs[`jumlah_barang_${id_barang_pesanan}`]?.focus();
                });
            },
            cancelUpdatePesananBarang(index) {
                let id_barang_pesanan = this.barang_pesanan[index].id;
                this.isEditBarang[id_barang_pesanan] = false;
                this.jumlah_barang_input[id_barang_pesanan] = this.temp[id_barang_pesanan]['jumlah'];
                this.barang_pesanan[index] = this.temp[id_barang_pesanan];
                this.barang_pesanan = [...this.barang_pesanan];
            },
            async updatePesananBarang(id_barang_pesanan) {
                await $wire.updatePesananBarang(id_barang_pesanan)
                    .then(($response) => {
                        if ($response?.status === 'success') {
                            this.isEditBarang[id_barang_pesanan] = false;
                        } else {
                            this.isEditBarang[id_barang_pesanan] = true;
                        }
                    })
                return;
            },

            getAllSatuan(data_barang) {
                return [{
                        id_satuan: data_barang.id_satuan_utama,
                        satuan: data_barang.satuan_utama.nama,
                        rasio: 1
                    },
                    ...data_barang.opsi_satuan.map(item => ({
                        id_satuan: item.id,
                        satuan: item.nama,
                        rasio: item.pivot.rasio_satuan_utama / item.pivot.rasio_satuan_opsi
                    }))
                ];
            },
            calculateJumlahDalamSatuanUtama(data_barang, jumlah, id_satuan = null) {
                id_satuan = id_satuan ?? data_barang.id_satuan_utama;
                let item = this.getAllSatuan(data_barang).find(s => s.id_satuan == id_satuan);
                return (item?.rasio ?? 1) * jumlah;
            },
            getTotalJumlahDalamSatuanUtama(id_barang) {
                return this.list_total_jumlah_dalam_satuan_utama[`${id_barang}`] ?? 0;
            },
            hargaJual(data_barang, jumlah_dalam_satuan_utama = null, id_satuan = null) {
                //data_barang
                // id_satuan = id_satuan ?? this.data_barang.id_satuan_utama;
                id_satuan = id_satuan ?? data_barang.id_satuan_utama;

                if (!jumlah_dalam_satuan_utama) {
                    jumlah_dalam_satuan_utama = this.calculateJumlahDalamSatuanUtama(data_barang, 1, id_satuan);
                }

                let opsi_harga = data_barang.opsi_harga
                    .map(item => ({
                        ...item,
                        jumlah_dalam_satuan_utama: this.calculateJumlahDalamSatuanUtama(data_barang, item.jumlah_min, item.id_satuan)
                    }))
                    .concat([{
                        jumlah_dalam_satuan_utama: 1,
                        harga: data_barang.harga_jual_utama,
                        harga_satuan_utama: data_barang.harga_jual_utama,
                    }])
                    .sort((a, b) => a.jumlah_dalam_satuan_utama - b.jumlah_dalam_satuan_utama);

                let harga_terpilih = opsi_harga
                    .filter(i => i.jumlah_dalam_satuan_utama <= jumlah_dalam_satuan_utama)
                    .pop();

                let harga = this.calculateJumlahDalamSatuanUtama(
                    data_barang,
                    harga_terpilih?.harga_satuan_utama ?? data_barang.harga_jual_utama,
                    id_satuan
                );

                return [
                    harga,
                    harga_terpilih?.harga_satuan_utama ?? data_barang.harga_jual_utama
                ];
            },
            updateHarga(id_barang_pesanan) {
                let barang_pesanan = this.barang_pesanan.filter((item) => item.id === id_barang_pesanan)[0];

                let jumlah = formatStringToNumber(this.jumlah_barang_input[id_barang_pesanan]);

                // a. Ubah jumlah ke satuan utama
                let jumlah_dalam_su = this.calculateJumlahDalamSatuanUtama(barang_pesanan.barang, jumlah, barang_pesanan.id_satuan);
                this.jumlah_barang_input_dalam_satuan_utama = {
                    ...this.jumlah_barang_input_dalam_satuan_utama,
                    [id_barang_pesanan]: jumlah_dalam_su
                };
                let total_jumlah_keseluruhan_before = this.getTotalJumlahDalamSatuanUtama(barang_pesanan.id_barang);
                let jumlah_temp_satuan_utama = this.calculateJumlahDalamSatuanUtama(barang_pesanan.barang, this.temp[id_barang_pesanan]['jumlah'],
                    barang_pesanan.id_satuan);


                // b. Total jumlah yang sudah ada + input
                let total_jumlah = total_jumlah_keseluruhan_before - jumlah_temp_satuan_utama + jumlah_dalam_su;
                //key nya berdasarkan id_barang ya
                this.total_jumlah_dalam_satuan_utama_by_barang[id_barang_pesanan] = {
                    ...this.total_jumlah_dalam_satuan_utama_by_barang,
                    [id_barang_pesanan]: total_jumlah
                };

                // c. Hitung harga dasar
                let [harga, harga_utama] = this.hargaJual(barang_pesanan.barang, total_jumlah, barang_pesanan.id_satuan);

                barang_pesanan.harga = harga;
                barang_pesanan.harga_satuan_utama = harga_utama;
                barang_pesanan.sub_total = jumlah * barang_pesanan.harga;
                // d. Mode RESEP
                if (this.mode_kasir === 'RESEP') {
                    barang_pesanan.harga = this.hargaJualResep(barang_pesanan.harga);
                    barang_pesanan.harga_satuan_utama = this.hargaJualResep(barang_pesanan.harga_satuan_utama);
                    barang_pesanan.sub_total = jumlah * barang_pesanan.harga;
                }
                this.barang_pesanan.filter((item) => item.id === id_barang_pesanan)[0] = barang_pesanan;
            },
            hargaJualResep(harga) {
                return harga + (harga * this.persen_resep) / 100;
            },
            init() {
                // $wire.updateValueAllBarangPesananKasir();
                this.setPromo();
                this.combinedPromoAndBarangPesanan();
                Livewire.on('barang_pesanan_updated', ({
                    list_total_jumlah_dalam_satuan_utama,
                    pesanan_kasir
                }) => {
                    this.$nextTick(() => {
                        this.id_member = pesanan_kasir.id_member;
                        this.list_total_jumlah_dalam_satuan_utama = list_total_jumlah_dalam_satuan_utama;
                        this.barang_pesanan = pesanan_kasir.barang_pesanan_kasir;
                        this.promo_pesanan_kasir = pesanan_kasir.promo_pesanan_kasir;
                        this.promo = {};

                        this.setPromo();
                        this.combinedPromoAndBarangPesanan();
                    })

                });

                Livewire.on('select-promo', ({
                    id_promo_barang,
                    id_barang,
                    type
                }) => {
                    if (type === 'form') return;
                    console.log(id_promo_barang, id_barang)
                    let total_jumlah_dalam_satuan_utama = this.getTotalJumlahDalamSatuanUtama(id_barang);
                    let total_harga_barang = this.barang_pesanan.filter(item => item.id_barang === id_barang)
                        .reduce((sum, item) => sum + item.sub_total, 0);
                    $wire.updatePromoBarang(id_promo_barang, id_barang, total_jumlah_dalam_satuan_utama, total_harga_barang);
                });



                Livewire.on('updatedModeKasir', ({
                    value
                }) => {
                    this.mode_kasir = value;
                    // if (this.data_barang) {
                    //     this.updateHarga();
                    // }
                });
            },
            updateJumlah($el, id_barang_pesanan) {
                $el.value = formatToStringNumber($el.value);
                this.updateHarga(id_barang_pesanan);
            },

            showListDiskonBarang(id_barang_pesanan) {
                let barang_select = this.barang_pesanan.filter((item) => item.id === id_barang_pesanan)[0];

                let promo_selected = this.promo_pesanan_kasir.filter((item) => item.promo_barang?.id_barang === barang_select.id_barang)[0]
                    ?.id_promo_barang ?? null;
                let id_promo_selected = barang_select.promo_barang?.id_promo_barang ?? null;
                let total_jumlah_dalam_satuan_utama = this.getTotalJumlahDalamSatuanUtama(barang_select.id_barang);
                $dispatch('toggle-modal-promo_barang', {
                    promo_barangModal: true
                });
                $dispatch('list-promo-barang', {
                    id_promo_barang_selected: promo_selected,
                    id_barang: barang_select.id_barang,
                    total_jumlah: total_jumlah_dalam_satuan_utama,
                    id_member: this.id_member,
                    type: 'list'
                });
            },

        }));
    </script>
@endscript

<div class="card" x-data="barangPesananList">
    <div class="card-body p-2">
        <div>
            <button class="btn btn-sm bg-error hover:bg-erroremphasis w-fit" wire:click='deleteAllPesananBarang'>Hapus Semua</button>
            {{-- @if (!empty($ceklistDeleteBarang)) --}}
            <template x-if="ceklistDeleteBarang.length > 0">
                <button class="btn btn-sm bg-error hover:bg-erroremphasis w-fit" wire:click='deleteWithSelectedItem'>Hapus yang Dipilih</button>
            </template>
            @error('barang_pesanan.*')
                <p class="text-red-500 text-sm">* {{ $message }}</p>
            @enderror
        </div>
        <x-table nama="barang-pesanan-kasir" height=" min-h-96  w-auto">
            <x-table.thead>
                <x-table.th label="#" />
                <x-table.th label="No" />
                <x-table.th label="Nama Barang" />
                <x-table.th label="Harga" />
                <x-table.th label="Jumlah" />
                <x-table.th label="Total" />
                <x-table.th label="Aksi" />
            </x-table.thead>

            <template x-if="barang_pesanan.length > 0">
                <template x-for="(item, index) in barang_pesanan" :key="index">
                    <x-table.tbody ::class="((index === barang_pesanan.length - 1) ? 'h-full' : '')">
                        <x-table.tr ::class="(item.promo_barang ? '' : ((index === barang_pesanan.length - 1) ? 'h-full' : ''))">
                            {{-- ' ' + (errors_barang_pesanan[item.id]?.status ? errors_barang_pesanan[item.id]?.bg_color : '')"> --}}
                            <x-table.td class="text-center">
                                <input id="hs-table-checkbox-1" x-model="ceklistDeleteBarang" :value="item.id" type="checkbox"
                                       class="border-gray-400 w-4 h-4 rounded text-primary focus:ring-primary dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-gray-800">
                                <label for="hs-table-checkbox-1" class="sr-only">Checkbox</label>
                            </x-table.td>
                            <x-table.td class="text-center" x-text="index+1"></x-table.td>

                            <x-table.td class="font-bold"
                                        x-text="`${item.barang.nama} [${formatNumberToString(item.barang.stok)} / ${item.barang.satuan_utama.nama}]`">
                            </x-table.td>
                            <x-table.td class="min-w-24">
                                <template x-if="item.id_satuan != item.barang.id_satuan_utama">
                                    <div class="">
                                        <p class="text-sm" x-text="`Rp. ${formatNumberToString(item.harga)} / ${item.satuan.nama}`">
                                        </p>
                                        &asymp;
                                        <span class="text-xs text-gray-500 dark:text-gray-400"
                                              x-text="`Rp. ${formatNumberToString(item.harga_satuan_utama)} / ${item.barang.satuan_utama.nama}`">
                                        </span>
                                    </div>
                                </template>
                                <template x-if="item.id_satuan == item.barang.id_satuan_utama">
                                    <div class="inline-flex text-sm">
                                        <h6 x-text="`Rp. ${formatNumberToString(item.harga_satuan_utama)} / ${item.barang.satuan_utama.nama}`"></h6>
                                    </div>
                                </template>
                            </x-table.td>
                            <x-table.td class="min-w-44">
                                <div>
                                    <form x-show="isEditBarang[`${item.id}`]" @submit.prevent="updatePesananBarang(item.id)">
                                        <x-form.input x-effect="refs[`jumlah_barang_${item.id}`] = $el" ::value="item.jumlah" x-model="jumlah_barang_input[item.id]"
                                                      @keyup="updateJumlah($el, item.id)" errorName="jumlah_barang_input.${item.id}"
                                                      placeholder="Masukan Jumlah" inputGroup="right">
                                            <x-slot:labelInputGroup>
                                                <span x-text="'/'+item.satuan.nama"></span>
                                            </x-slot:labelInputGroup>
                                        </x-form.input>
                                    </form>
                                    <h6 x-show="!isEditBarang[item.id]" class="text-center" wire:ignore x-text="`${item.jumlah} / ${item.satuan.nama}`"></h6>
                                </div>
                            </x-table.td>
                            <x-table.td class="text-right min-w-24" x-text="`Rp. ${formatNumberToString(item.sub_total)}`"></x-table.td>
                            <x-table.td class="text-center flex items-center justify-center gap-x-1 min-w-24">

                                @include('livewire.kasir-cadangan.components.badge-tooltip-info-barang')

                                <x-table.action-button tooltip="List Diskon Barang" color="primary" x-show="!isEditBarang[item.id]"
                                                       @click="showListDiskonBarang(item.id)">
                                    <span class="text-xs">Promo</span>
                                </x-table.action-button>
                                <x-table.action-button tooltip="Simpan" color="success" x-show="isEditBarang[item.id]" @click="updatePesananBarang(item.id)">
                                    <span class="iconify tabler--check text-lg"></span>
                                </x-table.action-button>
                                <x-table.action-button tooltip="Simpan" color="secondary" x-show="isEditBarang[item.id]"
                                                       @click="cancelUpdatePesananBarang(index)">
                                    <span class="iconify tabler--x text-lg"></span>
                                </x-table.action-button>

                                <x-table.action-button tooltip="Edit" color="warning" x-show="!isEditBarang[item.id]" @click="editPesananBarang(item.id)">
                                    <span class="iconify tabler--edit-circle text-lg"></span>
                                </x-table.action-button>

                                <x-table.action-button tooltip="Hapus" color="error" x-show="!isEditBarang[item.id]"
                                                       wire:click="$dispatch('alert-confirm', { id: item.id, method: 'delete-barang-pesanan-kasir' });">
                                    <span class="iconify tabler--trash-x-filled text-lg"></span>
                                </x-table.action-button>
                            </x-table.td>
                        </x-table.tr>

                        <template x-if="item.promo_barang">
                            <x-table.tr-empty ::class="'ps-4 text-start align-top' + (index === (barang_pesanan.length - 1) ? ' h-full' : '')" colspan="7">
                                <x-slot:content>
                                    **Promo:** <span x-text="item.promo_barang.deskripsi"></span>
                                </x-slot:content>
                            </x-table.tr-empty>
                        </template>

                    </x-table.tbody>
                </template>
            </template>
            <template x-if="barang_pesanan.length === 0">
                <x-table.tr-empty colspan="7" />
            </template>

        </x-table>

        {{-- <x-modal name="promo_barang" size="6xl">
            <livewire:kasir-cadangan.promo.promo-barang-list />
        </x-modal> --}}
    </div>
</div>

{{--
            <x-table.tbody>
                    @forelse ($barang_pesanan_kasir as $index => $barang_pesanan)
                        <x-table.tr wire:key='{{ $index }}'
                                    class="{{ !empty($barang_pesanan['promo_barang']) ? '' : ($loop->last ? 'h-full' : '') }} {{ $errors->has('barang_pesanan.' . $barang_pesanan['id']) ? 'bg-red-400/80' : '' }}">
                            <x-table.td class="text-center">
                                <input id="hs-table-checkbox-1" wire:model.live="ceklistDeleteBarang" value="{{ $barang_pesanan['id'] }}" type="checkbox"
                                       class="border-gray-400 w-4 h-4 rounded text-primary focus:ring-primary dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-gray-800">
                                <label for="hs-table-checkbox-1" class="sr-only">Checkbox</label>
                            </x-table.td>
                            <x-table.td class="text-center">{{ $index + 1 }}</x-table.td>
                            <x-table.td class="font-bold">{{ $barang_pesanan['barang']['nama'] }}
                                [@decimal($barang_pesanan['barang']['stok']) / {{ $barang_pesanan['barang']['satuan_utama']['nama'] }}]</x-table.td>
                            <x-table.td class="min-w-24">
                                @if ($barang_pesanan['id_satuan'] != $barang_pesanan['barang']['id_satuan_utama'])
                                    <div class="text-sm">
                                        Rp. @nominal($barang_pesanan['harga']) / {{ $barang_pesanan['satuan']['nama'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        &asymp; Rp. @nominal($barang_pesanan['harga_satuan_utama'])
                                        / {{ $barang_pesanan['barang']['satuan_utama']['nama'] }}
                                    </div>
                                @else
                                    <div class="inline-flex text-sm">
                                        <h6>Rp. @nominal($barang_pesanan['harga_satuan_utama'])
                                            / {{ $barang_pesanan['barang']['satuan_utama']['nama'] }}</h6>
                                    </div>
                                @endif
                            </x-table.td>
                            <x-table.td class="min-w-44">
                                <div @focus-edit-jumlah-{{ $barang_pesanan['id'] }}.window="$focus.focus($refs.edit_jumlah_{{ $barang_pesanan['id'] }});">
                                    <form x-show="isEditBarang['{{ $barang_pesanan['id'] }}']"
                                          @submit.prevent="updatePesananBarang('{{ $barang_pesanan['id'] }}')">
                                        <x-form.input x-ref="edit_jumlah_{{ $barang_pesanan['id'] }}" value="{{ $barang_pesanan['jumlah'] }}"
                                                      wire:model="jumlah_barang_pesanan.{{ $barang_pesanan['id'] }}" onkeyup="formatForInputNumber(this)"
                                                      errorName="jumlah_barang_pesanan.{{ $barang_pesanan['id'] }}" placeholder="Masukan Jumlah"
                                                      labelInputGroup="/{{ $barang_pesanan['satuan']['nama'] }}" inputGroup="right" />
                                    </form>
                                    <h6 x-show="!isEditBarang['{{ $barang_pesanan['id'] }}']" class="text-center">{{ $barang_pesanan['jumlah'] }} /
                                        {{ $barang_pesanan['satuan']['nama'] }}</h6>
                                </div>
                            </x-table.td>
                            <x-table.td class="text-center min-w-24">
                                Rp. @nominal($barang_pesanan['sub_total'])
                            </x-table.td>
                            <x-table.td class="text-center flex items-center justify-center gap-x-1 min-w-24">
                                <x-table.action-button tooltip="List Diskon Barang" color="info" x-show="!isEditBarang['{{ $barang_pesanan['id'] }}']"
                                                       wire:click="showListDiskonBarang('{{ $barang_pesanan['id'] }}')">
                                    <span class="text-xs">Diskon</span>
                                </x-table.action-button>
                                <x-table.action-button tooltip="Simpan" color="success" x-show="isEditBarang['{{ $barang_pesanan['id'] }}']"
                                                       @click="updatePesananBarang('{{ $barang_pesanan['id'] }}')">
                                    <span class="iconify tabler--check text-lg"></span>
                                </x-table.action-button>

                                <x-table.action-button tooltip="Edit" color="warning" x-show="!isEditBarang['{{ $barang_pesanan['id'] }}']"
                                                       @click="editPesananBarang('{{ $barang_pesanan['id'] }}')">
                                    <span class="iconify tabler--edit-circle text-lg"></span>
                                </x-table.action-button>

                                <x-table.action-button tooltip="Hapus" color="error"
                                                       wire:click="$dispatch('alert-confirm', { id: '{{ $barang_pesanan['id'] }}', method: 'delete-barang-pesanan-kasir' });">
                                    <span class="iconify tabler--trash-x-filled text-lg"></span>
                                </x-table.action-button>
                            </x-table.td>

                        </x-table.tr>
                        @if ($barang_pesanan['promo_barang'])
                            <x-table.tr-empty class="ps-4 text-start align-top" colspan="8">
                                <x-slot:content>
                                    **Promo:** {{ $barang_pesanan['promo_barang']['deskripsi'] }}
                                </x-slot:content>
                            </x-table.tr-empty>
                            @if ($loop->last)
                                <x-table.tr-empty class="h-full" colspan="8" content="" />
                            @endif
                        @endif
                    @empty
                        <x-table.tr-empty colspan="8" />
                    @endforelse
            </x-table.tbody>
            --}}
