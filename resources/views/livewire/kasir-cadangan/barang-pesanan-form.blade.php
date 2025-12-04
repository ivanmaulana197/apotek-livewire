@script
    <script>
        Alpine.data('barangPesananForm', () => ({
            data_barang: $wire.entangle('data_barang'),
            id_barang: $wire.entangle('id_barang'),
            jumlah: $wire.entangle('jumlah'),
            id_satuan: $wire.entangle('id_satuan'),
            harga_jual: $wire.entangle('harga_jual'),
            harga_satuan_utama: $wire.entangle('harga_satuan_utama'),
            sub_total: $wire.entangle('sub_total'),
            id_promo_selected: $wire.entangle('id_promo_selected'),
            id_list_promo: $wire.entangle('id_list_promo'),
            persen_resep: $wire.entangle('persen_resep'),
            jumlah_dalam_satuan_utama: $wire.entangle('jumlah_dalam_satuan_utama'),
            total_jumlah_dalam_satuan_utama_by_barang: $wire.entangle('total_jumlah_dalam_satuan_utama_by_barang'),
            list_total_jumlah_dalam_satuan_utama: $wire.entangle('list_total_jumlah_dalam_satuan_utama'),
            mode_kasir: '{{ $pesanan_kasir->mode_kasir }}',
            getAllSatuan() {
                return [{
                        id_satuan: this.data_barang.id_satuan_utama,
                        satuan: this.data_barang.satuan_utama.nama,
                        rasio: 1
                    },
                    ...this.data_barang.opsi_satuan.map(item => ({
                        id_satuan: item.id,
                        satuan: item.nama,
                        rasio: item.pivot.rasio_satuan_utama / item.pivot.rasio_satuan_opsi
                    }))
                ];
            },
            calculateJumlahDalamSatuanUtama(jumlah, id_satuan = null) {
                id_satuan = id_satuan ?? this.data_barang.id_satuan_utama;
                let item = this.getAllSatuan().find(s => s.id_satuan == id_satuan);
                return (item?.rasio ?? 1) * jumlah;
            },
            getTotalJumlahDalamSatuanUtama(id_barang) {
                return this.list_total_jumlah_dalam_satuan_utama[`${id_barang}`] ?? 0;
            },
            hargaJual(jumlah_dalam_satuan_utama = null, id_satuan = null) {
                id_satuan = id_satuan ?? this.data_barang.id_satuan_utama;

                if (!jumlah_dalam_satuan_utama) {
                    jumlah_dalam_satuan_utama = this.calculateJumlahDalamSatuanUtama(1, id_satuan);
                }

                let opsi_harga = this.data_barang.opsi_harga
                    .map(item => ({
                        ...item,
                        jumlah_dalam_satuan_utama: this.calculateJumlahDalamSatuanUtama(item.jumlah_min, item.id_satuan)
                    }))
                    .concat([{
                        jumlah_dalam_satuan_utama: 1,
                        harga: this.data_barang.harga_jual_utama,
                        harga_satuan_utama: this.data_barang.harga_jual_utama,
                    }])
                    .sort((a, b) => a.jumlah_dalam_satuan_utama - b.jumlah_dalam_satuan_utama);

                let harga_terpilih = opsi_harga
                    .filter(i => i.jumlah_dalam_satuan_utama <= jumlah_dalam_satuan_utama)
                    .pop();

                let harga = this.calculateJumlahDalamSatuanUtama(
                    harga_terpilih?.harga_satuan_utama ?? this.data_barang.harga_jual_utama,
                    id_satuan
                );

                return [
                    harga,
                    harga_terpilih?.harga_satuan_utama ?? this.data_barang.harga_jual_utama
                ];
            },
            updateHarga() {
                let jumlah = formatStringToNumber(this.jumlah);
                // a. Ubah jumlah ke satuan utama
                let jumlah_dalam_su = this.calculateJumlahDalamSatuanUtama(jumlah, this.id_satuan);
                this.jumlah_dalam_satuan_utama = jumlah_dalam_su;
                // b. Total jumlah yang sudah ada + input
                let total_jumlah = this.getTotalJumlahDalamSatuanUtama(this.data_barang.id) + jumlah_dalam_su;
                this.total_jumlah_dalam_satuan_utama_by_barang = total_jumlah;
                // c. Hitung harga dasar
                let [harga, harga_utama] = this.hargaJual(total_jumlah, this.id_satuan);

                this.harga_jual = harga;
                this.harga_satuan_utama = harga_utama;
                this.sub_total = jumlah * this.harga_jual;
                // d. Mode RESEP
                if (this.mode_kasir === 'RESEP') {
                    this.harga_jual = this.hargaJualResep(this.harga_jual);
                    this.harga_satuan_utama = this.hargaJualResep(this.harga_satuan_utama);
                    this.sub_total = jumlah * this.harga_jual;
                }
            },
            hargaJualResep(harga) {
                return harga + (harga * this.persen_resep) / 100;
            },
            init() {
                $wire.on('selectedBarang', async ({
                    idBarang
                }) => { //from local event
                    if (!idBarang) {
                        this.resetForm();
                        return;
                    } else {
                        this.id_barang = idBarang;
                        await $wire.getDataBarang(idBarang).then((response) => {
                            if (response) {
                                this.data_barang = response;
                                this.id_satuan = this.data_barang.id_satuan_utama;
                                let list_satuan = this.getAllSatuan().map(item => ({
                                    nama: item.satuan,
                                    id: item.id_satuan,
                                }));
                                $dispatch('update_satuan', {
                                    data: list_satuan,
                                    selected: this.data_barang.id_satuan_utama,
                                });
                                this.updateHarga();
                            }
                        })
                    }
                });

                $wire.on('selectedPromoBarang', ({
                    idPromoBarang
                }) => {
                    this.id_promo_selected = idPromoBarang;
                });

                Livewire.on('updatedModeKasir', ({
                    value
                }) => {
                    this.mode_kasir = value;
                    if (this.data_barang) {
                        this.updateHarga();
                    }
                });

                Livewire.on('select-promo', ({
                    id_promo_barang,
                    id_barang,
                    type,
                }) => {
                    // if (type === 'list') return;
                    if (this.id_barang !== id_barang) return;
                    this.id_promo_selected = id_promo_barang;
                });

                Livewire.on('barang_pesanan_updated', ({
                    list_total_jumlah_dalam_satuan_utama
                }) => {
                    this.$nextTick(() => {
                        this.resetForm();
                        this.list_total_jumlah_dalam_satuan_utama = list_total_jumlah_dalam_satuan_utama;
                    });

                });
            },
            resetForm() {
                this.data_barang = null;
                this.id_barang = null;
                this.id_satuan = null;
                this.$nextTick(() => {
                    this.jumlah = '';
                })
                this.harga_jual = null;
                this.harga_satuan_utama = null;
                this.sub_total = null;
                this.jumlah_dalam_satuan_utama = null;
                this.total_jumlah_dalam_satuan_utama_by_barang = 0;
                $dispatch('update_satuan', {
                    data: [],
                    selected: null
                });
            },

            updateJumlah($el) {
                $el.value = formatToStringNumber($el.value);
                if (this.data_barang) {
                    this.updateHarga();
                }
            },
        }));
    </script>
@endscript

<div class="" x-data="barangPesananForm">
    <form wire:submit.prevent='addBarangPesananKasir'>
        <div class="grid grid-cols-12 gap-2">
            <div class="col-span-12 md:col-span-6 lg:col-span-4">
                <livewire:kasir.components.search-barang />
            </div>
            <div class="col-span-12 md:col-span-6 lg:col-span-4">
                <div class="grid grid-cols-2 join " @focus-input-jumlah.window="$focus.focus($refs.input_jumlah)">
                    <x-form.input @keyup="updateJumlah($el)" placeholder="Masukan Jumlah" x-model="jumlah" class="join-item" error-name="jumlah"
                                  x-ref="input_jumlah" />
                    <x-form.select-alpine @change="updateHarga" placeholder="Pilih Satuan" :emptyOption="false" :dataOption="[]" error-name="id_satuan"
                                          name="satuan" :emptyOption=false x-model='id_satuan' class="join-item" />
                </div>
            </div>
            <div class="col-span-12 md:col-span-6 lg:col-span-4">
                <div class="grid grid-cols-3 md:grid-cols-1 lg:grid-cols-3 gap-2">
                    @include('livewire.kasir-cadangan.components.badge-tooltip-info-satuan')
                    @include('livewire.kasir-cadangan.components.badge-tooltip-info-grosir')
                    <button class="btn btn-sm" type="button"
                            @click="
                        $dispatch('toggle-modal-promo_barang', {promo_barangModal: true});
                        $dispatch('list-promo-barang', {
                            id_promo_barang_selected: id_promo_selected,
                            id_barang: data_barang?.id ?? null,
                            total_jumlah: total_jumlah_dalam_satuan_utama_by_barang,
                            id_member: '{{ $pesanan_kasir->id_member }}',
                            type: 'form'
                        });

                    ">List
                        Promo</button>
                </div>
            </div>

            <div class="col-span-12 md:col-span-6 lg:col-span-12">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-2">
                    <div
                         class="flex justify-between py-1 px-3 rounded-lg border border-bordergray bg-gray-100 text-gray-500 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-700">
                        <span>Stok :</span>
                        <span x-text="formatNumberToString(data_barang?.stok ?? 0) + ' / ' + (data_barang?.satuan_utama?.nama ?? '')"> </span>
                    </div>
                    <div
                         class="flex justify-between py-1 px-3 rounded-lg border border-bordergray bg-gray-100 text-gray-500  dark:text-gray-400  dark:bg-gray-700 dark:border-gray-700">
                        <span>Harga:</span>
                        <span class="font-bold" x-text="'Rp. '+formatNumberToString(harga_jual ?? 0)"> </span>
                    </div>
                    <div
                         class="flex justify-between py-1 px-3 rounded-lg border border-bordergray bg-gray-100 text-gray-500  dark:text-gray-400  dark:bg-gray-700 dark:border-gray-700">
                        <span>Sub Total:</span>
                        <span class="font-bold" x-text="'Rp. '+formatNumberToString(sub_total ?? 0)"> </span>
                    </div>
                </div>
            </div>
            <div class="col-span-12">
                <button type="submit" class="btn btn-sm bg-primary w-full ">Tambah +</button>
            </div>
        </div>
    </form>

    <x-modal name="promo_barang" size="6xl">
        <livewire:kasir-cadangan.promo.promo-barang-list />
    </x-modal>
</div>
