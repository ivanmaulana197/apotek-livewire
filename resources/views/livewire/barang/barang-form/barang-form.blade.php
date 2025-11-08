<x-slot:title>{{ $form->isEdit ? 'Edit' : 'Tambah' }} Data Barang</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="{{ $form->isEdit ? 'Edit' : 'Tambah' }} Data Barang">
        <x-slot:button>
            <a class="btn btn-xs bg-slate-500 hover:bg-slate-700" href="{{ route('data.barang') }}" wire:navigate>
                <span class="iconify famicons--caret-back"></span>
                Kembali
            </a>
        </x-slot:button>
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
            <x-breadcrumb.before name="Data Barang" href="{{ route('data.barang') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="card" x-data="formBarang">
        <div class="card-body">
            <form wire:submit.prevent='{{ $form->isEdit ? 'update' : 'save' }}'>
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-6">
                        <x-form.input label="Kode Barang" x-model='kode' error-name="form.kode" />
                        <x-badge message="Dikosongkan jika tidak ada kode barang" />
                        <x-form.input x-model='nama' error-name="form.nama" label="Nama" />
                        <x-form.input x-model='stok' error-name="form.stok" label="Stok" @keyup="formatForInputNumber($el)" input-group="right">
                            <x-slot:labelInputGroup>
                                <span x-text="nama_satuan_utama ?? '-'"></span>
                            </x-slot:labelInputGroup>
                        </x-form.input>
                        <x-form.input x-model='stok_min' error-name="form.stok_min" label="Stok Minimal" @keyup="formatForInputNumber($el)" input-group="right">
                            <x-slot:labelInputGroup>
                                <span x-text="nama_satuan_utama ?? '-'"></span>
                            </x-slot:labelInputGroup>
                        </x-form.input>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <x-form.select-alpine label="Kategori Obat" name="kategori" :data-option="$this->kategories" option-value="id" option-label="nama" :
                                              modal-name="kategori" :add-value=true multiple x-model="id_kategories" error-name="form.id_kategories" />
                        <x-form.select-alpine label="Rak" name="rak" :data-option="$this->raks" option-value="id" option-label="nama" modal-name="rak"
                                              :add-value=true multiple x-model="id_raks" error-name="form.id_raks" />
                        <x-form.input label="Keterangan" x-model='keterangan' error-name="keterangan" type="textarea" />
                    </div>

                    {{-- satuan barang --}}
                    @include('livewire.barang.barang-form.satuan-barang-form')

                    {{-- harga barang --}}
                    @include('livewire.barang.barang-form.harga-barang-form')

                    <div class="col-span-12 gap-2">
                        <button type="submit" class="btn bg-primary hover:bg-primaryemphasis">
                            {{ $form->isEdit ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('data.barang') }}" class="text-black bg-gray-200 border btn hover:bg-gray-400" wire:navigate>
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div>
            <x-modal name="rak">
                @livewire('data-master.rak.rak-form', ['isModal' => true])
            </x-modal>
            <x-modal name="satuan">
                @livewire('data-master.satuan.satuan-form', ['isModal' => true])
            </x-modal>
            <x-modal name="kategori">
                @livewire('data-master.kategori.kategori-form', ['isModal' => true])
            </x-modal>
        </div>
    </div>

</div>

@script
    <script>
        Alpine.data("formBarang", () => ({
            isEdit: $wire.entangle("form.isEdit"),
            kode: $wire.entangle("form.kode"),
            nama: $wire.entangle("form.nama"),
            stok: $wire.entangle("form.stok"),
            stok_min: $wire.entangle("form.stok_min"),
            id_kategories: $wire.entangle("form.id_kategories"),
            id_raks: $wire.entangle("form.id_raks"),
            keterangan: $wire.entangle("form.keterangan"),
            id_satuan_utama: $wire.entangle("form.id_satuan_utama"),
            nama_satuan_utama: $wire.entangle("form.nama_satuan_utama"),
            all_satuan: $wire.entangle("satuans"),
            satuan_tersedia: [],
            satuan_terpilih: [],
            opsi_satuan: $wire.entangle("form.opsi_satuan"),
            harga_beli_default: $wire.entangle("form.harga_beli_default"),
            mode_jual_utama: $wire.entangle('form.mode_jual_utama'),
            persen_jual_utama: $wire.entangle("form.persen_jual_utama"),
            harga_jual_utama: $wire.entangle("form.harga_jual_utama"),
            opsi_harga: $wire.entangle("form.opsi_harga"),

            getDipilih() {
                return [this.id_satuan_utama, ...this.opsi_satuan.map((o) => o.id_satuan)];
            },
            refreshOpsiSatuanAvailable() {
                const dipilih = this.getDipilih();

                // Untuk satuan utama
                this.satuan_tersedia["utama"] = this.all_satuan.filter((opt) =>
                    !dipilih.includes(opt.id) || opt.id === this.id_satuan_utama
                );

                // Untuk setiap opsi_satuan
                this.opsi_satuan.forEach((opsi, index) => {
                    this.satuan_tersedia[index] = this.all_satuan.filter(
                        (opt) => !dipilih.includes(opt.id) || opt.id === opsi.id_satuan
                    );
                });
                $dispatch("update_satuan", {
                    data: this.satuan_tersedia["utama"],
                    selected: this.id_satuan_utama,
                });

                this.opsi_satuan.forEach((opsi, index) => {
                    $dispatch("update_satuan_opsi_" + index, {
                        data: this.satuan_tersedia[index],
                        selected: opsi.id_satuan,
                    });
                });
            },
            opsiSatuanSelected() {
                const dipilih = this.getDipilih();
                this.satuan_terpilih = this.all_satuan.filter((opt) =>
                    dipilih.includes(opt.id)
                );
                this.$nextTick(() => {
                    this.opsi_harga.forEach(async (opsi, index) => {
                        if (!opsi.id_satuan) {
                            opsi.id_satuan = this.id_satuan_utama;
                        }
                        let cek = this.satuan_terpilih.some(
                            (item) => item.id === opsi.id_satuan
                        );
                        //cek apakah id satuan yang dipilih ada dalam satuan terpilih
                        //jika ada maka lanjut, jika tidak maka opsi.id_satuan = id_satuan_utama
                        if (!cek) {
                            opsi.id_satuan = this.id_satuan_utama;
                        }
                        opsi.nama_satuan = await $wire.fetchNamaSatuan(opsi.id_satuan);
                        if (opsi.mode == 'PERSEN') {
                            this.updatePersenJual(index);
                        } else if (opsi.mode == 'HARGA') {
                            this.updateHargaJual(index);
                        }
                        $dispatch("update_opsi_harga_" + index, {
                            data: this.satuan_terpilih,
                            selected: opsi.id_satuan,
                        });

                    });
                });
            },
            async satuanUtamaChange($el) {
                if (!$el.value) {
                    this.refreshAllSatuan();
                    this.nama_satuan_utama = null;
                } else {
                    this.nama_satuan_utama = await $wire.fetchNamaSatuan($el.value);
                }
                this.updatedSatuanOpsi();
            },
            updatedSatuanOpsi() {
                this.$nextTick(() => {
                    this.refreshOpsiSatuanAvailable();
                    this.opsiSatuanSelected();
                });
            },
            addOpsiSatuan() {
                this.opsi_satuan.push({
                    id_satuan: null,
                    rasio_satuan_utama: 1,
                    rasio_satuan_opsi: 1,
                });
                this.updatedSatuanOpsi();
            },
            removeOpsiSatuan(index) {
                this.opsi_satuan.splice(index, 1);
                this.updatedSatuanOpsi();
            },
            refreshAllSatuan() {
                this.id_satuan_utama = null;
                this.opsi_satuan = [];
                this.satuan_tersedia = [];
                this.satuan_terpilih = [];
            },
            jumlahDalamSatuanUtama(jumlah, id_satuan) {
                if (id_satuan == this.id_satuan_utama) {
                    return jumlah
                } else {
                    let satuan = this.opsi_satuan.find(i => i.id_satuan == id_satuan);
                    return satuan ? jumlah * (formatStringToNumber(satuan.rasio_satuan_utama) / formatStringToNumber(satuan.rasio_satuan_opsi)) :
                        $jumlah;
                }
            },
            updatedOpsiHarga() {
                this.opsi_harga.forEach((item, index) => {
                    if (item.mode == 'PERSEN') {
                        this.updatePersenJual(index);
                    } else if (item.mode == 'HARGA') {
                        this.updateHargaJual(index);
                    }
                })
            },
            addOpsiHarga() {
                this.opsi_harga.push({
                    jumlah_min: null,
                    id_satuan: this.id_satuan_utama,
                    nama_satuan: this.nama_satuan_utama,
                    mode: 'PERSEN',
                    persen: 0,
                    harga: this.harga_beli_default,
                    harga_satuan_utama: this.harga_beli_default,
                    // _utama //sudah berformat IDR
                });
                this.updatedSatuanOpsi();
            },
            removeOpsiHarga(index) {
                this.opsi_harga.splice(index, 1);
                this.updatedSatuanOpsi();
            },
            updateHargaBeli($el) {
                $el.value = formatToStringNumber($el.value);
                if (this.mode_jual_utama == 'PERSEN') {
                    this.updatePersenJual('utama');
                } else if (this.mode_jual_utama == 'HARGA') {
                    this.updateHargaJual('utama');
                }

                this.updatedOpsiHarga();
            },
            changeRasioSatuanOpsi($el) {
                $el.value = formatToStringNumber($el.value);
                this.updatedOpsiHarga();
            },
            changeRasioSatuanUtama($el) {
                $el.value = formatToStringNumber($el.value);
                this.updatedOpsiHarga();
            },
            async changeSatuanOpsiHarga(index) {
                this.updatedOpsiHarga();
                this.opsi_harga[index]['nama_satuan'] = await $wire.fetchNamaSatuan(this.opsi_harga[index]['id_satuan']);

            },
            updateHargaJual(index = 'utama') {
                let harga_beli_default = formatStringToNumber(this.harga_beli_default);
                let persen, harga;
                if (index == 'utama') {
                    harga = formatStringToNumber(this.harga_jual_utama == '' ? '0' : this
                        .harga_jual_utama);
                    persen = ((harga - harga_beli_default) / harga_beli_default) * 100;
                    this.persen_jual_utama = formatNumberToString(persen)
                    this.harga_jual_utama = formatToStringNumber(this.harga_jual_utama == '' ? '0' : this
                        .harga_jual_utama);
                } else {
                    // rasio = this.opsi_harga[index]['id_satuan']
                    harga_beli_default = harga_beli_default * this.jumlahDalamSatuanUtama(1, this.opsi_harga[index]['id_satuan']);
                    harga = formatStringToNumber(this.opsi_harga[index]['harga'] == '' ?
                        '0' : this.opsi_harga[index]['harga']);
                    persen = ((harga - harga_beli_default) / harga_beli_default) * 100;
                    this.opsi_harga[index]['persen'] = formatNumberToString(persen)
                    this.opsi_harga[index]['harga'] = formatToStringNumber(this.opsi_harga[index]['harga'] == '' ? '0' : this.opsi_harga[index][
                        'harga'
                    ])
                    this.opsi_harga[index]['harga_satuan_utama'] = formatNumberToString(harga / this.jumlahDalamSatuanUtama(1, this.opsi_harga[index][
                        'id_satuan'
                    ]));

                }

            },
            updatePersenJual(index = 'utama') {
                let harga_beli_default = formatStringToNumber(this.harga_beli_default);
                let persen, harga;
                if (index == 'utama') {
                    persen = formatStringToNumber(this.persen_jual_utama == '' ? '0' : this
                        .persen_jual_utama);
                    harga = harga_beli_default + (harga_beli_default * persen) / 100;
                    this.harga_jual_utama = formatNumberToString(harga)
                    this.persen_jual_utama = formatToStringNumber(this.persen_jual_utama == '' ? '0' : this
                        .persen_jual_utama);
                } else {
                    harga_beli_default = harga_beli_default * this.jumlahDalamSatuanUtama(1, this.opsi_harga[index]['id_satuan']);
                    persen = formatStringToNumber(this.opsi_harga[index]['persen'] ==
                        '' ? '0' : this.opsi_harga[index]['persen']);
                    harga = harga_beli_default + (harga_beli_default * persen) / 100;
                    this.opsi_harga[index]['harga'] = formatNumberToString(harga)
                    this.opsi_harga[index]['persen'] = formatToStringNumber(this.opsi_harga[index][
                        'persen'
                    ] == '' ? '0' : this.opsi_harga[index]['persen'])
                    this.opsi_harga[index]['harga_satuan_utama'] = formatNumberToString(harga / this.jumlahDalamSatuanUtama(1, this.opsi_harga[index][
                        'id_satuan'
                    ]));

                }
            },
            init() {
                this.updatedSatuanOpsi();
                Livewire.on('satuan-created', ({
                    satuan
                }) => {
                    this.all_satuan.push({
                        id: satuan.id,
                        nama: satuan.nama,
                    });

                    this.updatedSatuanOpsi();
                });
            }
        }));
    </script>
@endscript
