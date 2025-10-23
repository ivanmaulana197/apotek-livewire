<x-slot:title>Data Promo</x-slot:title>

<div>
    <x-breadcrumb name="{{ $isEdit ? 'Edit' : 'Tambah' }} Promo">
        <x-slot:button>
            <a class="btn btn-xs bg-secondary hover:bg-secondaryemphasis" href="{{ route('promo') }}" wire:navigate>
                <iconify-icon icon="famicons:caret-back"></iconify-icon>
                Kembali
            </a>
        </x-slot:button>
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
            <x-breadcrumb.before name="Data Promo" href="{{ route('promo') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-1 gap-3">
        <div class="card " x-data="{
            refs: {},
            isLoading: true,
            tipe: $wire.entangle('form.tipe'),
            tipe_transaksi: $wire.entangle('form.tipe_transaksi'),
            barangPromo: $wire.entangle('form.barang_promo'),
            start: $wire.entangle('form.start_date'),
            end: $wire.entangle('form.end_date'),
            diskon_persen: $wire.entangle('form.diskon_persen'),
            diskon_nominal: $wire.entangle('form.diskon_nominal'),
            isEdit: $wire.entangle('isEdit'),
            is_member: $wire.entangle('form.is_member'),
            profesi_member: $wire.entangle('form.profesi_member'),
            initCustomTomSelect(el) {
                const model = el.getAttribute('x-model'); // ambil nama x-model (misal: 'form.tipe')
                el.tomselect?.destroy();
                el.tomselect = new TomSelect(el, {
                    maxItems: 1,
                    loadThrottle: 10,
                    sortField: {
                        field: 'text',
                        direction: 'asc'
                    },
                    valueField: 'value',
                    labelField: 'text',
                    plugins: [
                        'remove_button'
                    ],
                    loadingClass: 'z-10',
                    load: function(query, callback) {
                        //retun callback kosong jika query kosong
                        if (query.length < 1) return callback();
                        // hit ke function searchBarang dan mendapatkan data
                        data = $wire.searchBarang(query);
                        //data membutuhkan promise
                        data.then(item => {
                            //mapping data ke option
                            option = item.map(item => ({
                                value: item.id,
                                text: item.nama + ' [' + item.stok + ']'
                            }));
                            callback(option);
                        })
                    },
                });
                const initialValue = Alpine.evaluate(el, model);
                if (initialValue) {
                    if (this.isEdit) {
                        this.setValueCustomTomSelect('#' + el.id, initialValue);
                    }
                    el.tomselect.setValue(initialValue);
                }
            },
            setValueCustomTomSelect(id, id_select) {
                const tom = $(id)[0].tomselect;
                if (id_select === '') {
                    // tom.addOption({ value: '', text: '-- Pilih --' });
                    tom.setValue('');
                    return;
                }
                // console.log(id_select);
                $wire.fetchSingleItem(id_select).then(item => {
                    item = item.data;
                    tom.addOption({
                        value: item.id,
                        text: item.nama + ' [' + item.stok + ']'
                    });

                    tom.setValue(item.id);
                });
            },
            changeBarangSelected(el, index, tipe) {
                if (!this.isLoading) {
                    if (tipe == 'barang_promo') {
                        this.barangPromo[index].id_satuan = '';
                    } else if (tipe == 'barang_bonus') {
                        this.barangPromo[index].id_satuan_bonus = '';
                    }
                }
                $wire.fetchSingleItem(el.value).then(item => {
                    item = item.list_satuan;
                    if (tipe == 'barang_promo') {
                        id = 'id_satuan_' + index;
                        if (this.barangPromo[index].id_satuan == '') {
                            this.barangPromo[index].id_satuan = item[0]['value'];
                        }
                        selected = this.barangPromo[index].id_satuan;
                        this.refs['syarat_qty_' + index]?.focus();

                    } else if (tipe == 'barang_bonus') {
                        id = 'id_satuan_bonus_' + index;
                        if (this.barangPromo[index].id_satuan_bonus == '') {
                            this.barangPromo[index].id_satuan_bonus = item[0]['value'];
                        }
                        selected = this.barangPromo[index].id_satuan_bonus;
                        this.refs['bonus_qty_' + index]?.focus();
                    }
                    $dispatch('update-all-options-tom-select', {
                        id: id,
                        data: item,
                        selected: selected
                    });
                    $nextTick(() => {

                        this.isLoading = false;
                    })
                }).catch(error => {
                    if (tipe == 'barang_promo') {
                        id = 'id_satuan_' + index;
                        this.barangPromo[index].id_satuan = '';
                    } else if (tipe == 'barang_bonus') {
                        id = 'id_satuan_bonus_' + index;
                        this.barangPromo[index].id_satuan_bonus = '';
                    }
                    $dispatch('update-all-options-tom-select', {
                        id: id,
                        data: [],
                        selected: ''
                    });
                    $nextTick(() => {
                        this.isLoading = false;
                    })
                })

            },
            hapusBarangPromo(index) {
                this.barangPromo.splice(index, 1);
                this.barangPromo.forEach((item, i) => {
                    this.isLoading = true;
                    this.setValueCustomTomSelect('#barang_promo_barang_' + i, item.id_barang);
                    if (item.tipe == 'BARANG BONUS' || item.id_barang_bonus) {
                        this.setValueCustomTomSelect('#barang_promo_barang_bonus_' + i, item.id_barang_bonus);
                    }
                    $dispatch('set-value-tom-select', { id: 'tipe_promo_barang_' + i, value: item.tipe });

                })
            },
            tambahBarangPromo() {
                this.barangPromo.push({
                    id_barang: '',
                    syarat_qty: 1,
                    tipe_qty: 'MINIMAL',
                    id_satuan: '',
                    tipe: 'BARANG BONUS',
                    id_barang_bonus: '',
                    id_satuan_bonus: '',
                    bonus_qty: 1,
                    diskon_persen: null,
                    diskon_nominal: null
                })
            },
            changeTipeBarangPromo(index) {
                this.barangPromo[index].id_barang_bonus = '';
                this.barangPromo[index].bonus_qty = 1;
                this.barangPromo[index].diskon_persen = null;
                this.barangPromo[index].diskon_nominal = null;
            },
            changeTipeTransaksi() {
                this.diskon_persen = null;
                this.diskon_nominal = null;
            },
            changeIsMember() {
                if (!this.is_member) {
                    this.profesi_member = [];
                }
            },
        }" x-init="">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}" class="space-y-4">
                <div class="card-body">
                    <div class="flex items-center justify-between flex-none border-b border-gray-200">
                        <h5 class="card-title">{{ $isEdit ? 'Edit' : 'Tambah' }} Promo</h5>
                    </div>
                    <div class="">
                        <x-form.input name="nama" label="Nama Promo" type="text" wire:model="form.nama" error-name="form.nama" />
                        <x-form.input name="deskripsi" label="Deskripsi" type="textarea" wire:model="form.deskripsi" />
                        <div class="mt-2">
                            <label for="is_aktif" class="font-semibold text-gray-800 label-text dark:text-neutral-200 me-3">
                                Aktif ?
                            </label>
                            <input id="is_aktif" type="checkbox" class="rounded me-2 form-check-input" wire:model="form.is_aktif">
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 sm:grid-cols-2">
                            <x-alpine.form.input-date label="Tanggal Mulai:" name="start_date" ::id="'start_date'" :singleDatePicker=true x-model="start"
                                                      errorName="form.start_date" :ranges=false></x-alpine.form.input-date>
                            <x-alpine.form.input-date label="Tanggal Berakhir:" name="end_date" ::id="'end_date'" :singleDatePicker=true x-model="end"
                                                      errorName="form.end_date" :ranges=false></x-alpine.form.input-date>
                        </div>

                        <div class="grid grid-cols-12 gap-3 mb-3">
                            <div class="col-span-12 md:col-span-4">
                                <x-form.select-alpine name="tipe" ::id="'tipe_promo'" dropdown-parent="" label="Tipe Promo" :empty-option=false x-model="tipe"
                                                      error-name="form.tipe">
                                    <x-slot:custom-option>
                                        <option value="BARANG">BARANG</option>
                                        <option value="TRANSAKSI">TRANSAKSI</option>
                                    </x-slot:custom-option>
                                </x-form.select-alpine>
                            </div>
                            <div class="col-span-12 sm:col-span-5 md:col-span-3">
                                <x-form.label label="Untuk Member ?" />
                                <input @change="changeIsMember" type="checkbox" class="rounded form-check-input" x-model="is_member" />
                                <x-badge message="Centang jika diskon untuk membership" />
                            </div>
                            <div x-show="is_member" class="col-span-12 sm:col-span-7 md:col-span-5">
                                <x-form.select-alpine label="Profesi" id="profesi" name="profesi" :data-option="$this->profesies" option-value="id" option-label="nama"
                                                      modal-name="profesi" add-value="true" multiple x-model="profesi_member"
                                                      error-name="form.profesi_member.*" />
                            </div>
                        </div>

                        <!-- PROMO BARANG -->
                        <div x-show="tipe === 'BARANG'" class="space-y-2">
                            <template x-for="(item, index) in barangPromo" :key="index">
                                <div class="grid grid-cols-1 gap-x-4 p-2 border rounded-md" x-init="$nextTick(() => { $wire.index = index })">
                                    <div class="grid grid-cols-1 gap-x-3 sm:grid-cols-2">
                                        <x-form.select-alpine @change="changeBarangSelected($el, index, 'barang_promo')" name="barang_promo" :x-init="'initCustomTomSelect($el)'"
                                                              :init=false custom-option="" ::id="'barang_promo_barang_' + index" name="barang_promo_barang" ::placeholder="'Barang Promo ke-' + (index + 1)"
                                                              label="Barang" x-model="item.id_barang" error-name="form.barang_promo.${index}.id_barang" />
                                        <div class="grid grid-cols-1 ">
                                            <x-form.label label="Syarat Qty" />
                                            <div class="grid grid-cols-12 join">
                                                <div class="col-span-12 md:col-span-4">
                                                    <x-form.select-alpine name="tipe_qty" @change="changeTipeBarangPromo(index)" dropdown-parent=""
                                                                          ::id="'tipe_qty_' + index" :empty-option=false x-model="item.tipe_qty"
                                                                          error-name="form.barang_promo.${index}.tipe_qty">
                                                        <x-slot:custom-option>
                                                            <option value="MINIMAL">MINIMAL</option>
                                                            <option value="KELIPATAN">KELIPATAN</option>
                                                        </x-slot:custom-option>
                                                    </x-form.select-alpine>
                                                </div>
                                                <div class="col-span-6 md:col-span-4">
                                                    <x-form.input @keyup="formatForInputNumber($el)" placeholder="Masukan Jumlah" x-init="$nextTick(() => refs[`syarat_qty_${index}`] = $el)"
                                                                  x-model='item.syarat_qty' class="join-item" error-name="form.barang_promo.${index}.syarat_qty" />
                                                </div>
                                                <div class="col-span-6 md:col-span-4">

                                                    <x-form.select-alpine ::id="'id_satuan_' + index" placeholder="Pilih Satuan" :emptyOption="false" :dataOption="[]"
                                                                          optionValue="id" x-model='item.id_satuan'
                                                                          error-name="form.barang_promo.${index}.id_satuan" class="join-item" />
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <x-form.input inputGroup="right" label="Syarat Qty" @keyup="formatForInputNumber($el)" ::name="'form.barang_promos.' + index + '.syarat_qty'"
                                                      x-model="item.syarat_qty">
                                            <x-slot:labelInputGroup>
                                                <span x-text="item.satuan_barang ? item.satuan_barang : '-'"></span>
                                            </x-slot:labelInputGroup>
                                        </x-form.input> --}}
                                    </div>
                                    <div class="grid grid-cols-12 gap-3">
                                        <div :class="item.tipe === 'BARANG BONUS' ? 'col-span-12 md:col-span-4' : 'col-span-12 sm:col-span-6'">
                                            <x-form.select-alpine name="tipe_barang_promo" @change="changeTipeBarangPromo(index)" dropdown-parent=""
                                                                  ::id="'tipe_promo_barang_' + index" label="Tipe Promo Barang" :empty-option=false x-model="item.tipe"
                                                                  error-name="form.barang_promo.${index}.tipe">
                                                <x-slot:custom-option>
                                                    <option value="BARANG BONUS">BARANG BONUS</option>
                                                    <option value="PERSEN">PERSEN</option>
                                                    <option value="HARGA">HARGA</option>
                                                </x-slot:custom-option>
                                            </x-form.select-alpine>
                                        </div>
                                        <div x-show="item.tipe === 'BARANG BONUS'" class="col-span-12 md:col-span-8">
                                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                                <x-form.select-alpine @change="changeBarangSelected($el, index, 'barang_bonus')" name="barang_bonus"
                                                                      :x-init="'initCustomTomSelect($el)'" :init=false custom-option="" ::id="'barang_promo_barang_bonus_' + index"
                                                                      name="barang_promo_barang_bonus" ::placeholder="'Barang bonus ke-' + (index + 1)" label="Barang Bonus"
                                                                      x-model="item.id_barang_bonus" error-name="form.barang_promo.${index}.id_barang_bonus">
                                                </x-form.select-alpine>

                                                <div class="grid grid-cols-1 ">
                                                    <x-form.label label="Bonus Qty" />
                                                    <div class="grid grid-cols-2 join">
                                                        <x-form.input @keyup="formatForInputNumber($el)" placeholder="Masukan Jumlah" x-init="$nextTick(() => refs[`bonus_qty_${index}`] = $el)"
                                                                      x-model='item.bonus_qty' class="join-item"
                                                                      error-name="form.barang_promo.${index}.bonus_qty" />

                                                        <x-form.select-alpine ::id="'id_satuan_bonus_' + index" placeholder="Pilih Satuan" :emptyOption="false"
                                                                              :dataOption="[]" optionValue="id" x-model='item.id_satuan_bonus'
                                                                              error-name="form.barang_promo.${index}.id_satuan_bonus" class="join-item" />
                                                    </div>
                                                </div>

                                                {{-- <x-form.input inputGroup="right" name="qty" label="Bonus Qty" @keyup="formatForInputNumber($el)"
                                                              ::name="'form.barang_promos.' + index + '.bonus_qty'" x-model="item.bonus_qty">
                                                    <x-slot:labelInputGroup>
                                                        <span x-text="item.satuan_barang_bonus ? item.satuan_barang_bonus : '-'"></span>
                                                    </x-slot:labelInputGroup>
                                                </x-form.input> --}}
                                            </div>
                                        </div>
                                        <div x-show="item.tipe === 'PERSEN'" class="col-span-12 sm:col-span-6">
                                            <x-form.input error-name="form.barang_promo.${index}.diskon_persen" name="persen" label="Diskon (%)" @keyup="formatForInputNumber($el)" ::name="'form.barang_promos.' + index + '.diskon_persen'"
                                                          x-model="item.diskon_persen" />
                                        </div>
                                        <div x-show="item.tipe === 'HARGA'" class="col-span-12 sm:col-span-6">
                                            <x-form.input error-name="form.barang_promo.${index}.diskon_nominal" label="Diskon Nominal" @keyup="formatForInputNumber($el)" ::name="'form.barang_promos.' + index + '.diskon_nominal'"
                                                          x-model="item.diskon_nominal" />
                                        </div>
                                    </div>

                                    <div class="text-right m-2">
                                        <button type="button" class="text-white bg-red-600 btn btn-sm hover:bg-red-700"
                                                @click="hapusBarangPromo(index)">Hapus</button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" class="btn btn-sm" @click="tambahBarangPromo">
                                + Tambah Barang Promo
                            </button>
                        </div>

                        <!-- PROMO TRANSAKSI -->
                        <div x-show="tipe === 'TRANSAKSI'" class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 lg:col-span-4">
                                <x-form.input name="minimal_belanja" label="Minimal Belanja" wire:model="form.minimal_belanja"
                                              @keyup="formatForInputNumber($el)" />
                            </div>
                            <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                                <x-form.select-alpine name="tipe_transaksi" @change="changeTipeTransaksi" dropdown-parent="" ::id="'tipe_transaksi'"
                                                      label="Tipe Promo Transaksi" :empty-option=false x-model="tipe_transaksi"
                                                      error-name="form.tipe_transaksi">
                                    <x-slot:custom-option>
                                        <option value="PERSEN">PERSEN</option>
                                        <option value="HARGA">HARGA</option>
                                    </x-slot:custom-option>
                                </x-form.select-alpine>
                            </div>
                            <div x-show="tipe_transaksi === 'PERSEN'" class="col-span-12 sm:col-span-6 lg:col-span-4">
                                <x-form.input name="diskon" label="Diskon (%)" x-model="diskon_persen" @keyup="formatForInputNumber($el)" />
                            </div>
                            <div x-show="tipe_transaksi === 'HARGA'" class="col-span-12 sm:col-span-6 lg:col-span-4">
                                <x-form.input name="nominal" label="Diskon Nominal" x-model="diskon_nominal" @keyup="formatForInputNumber($el)" />
                            </div>

                        </div>

                    </div>
                    <div class="flex justify-end flex-none gap-2 px-4 py-2 border-t ">

                        <a href="{{ route('promo') }}" wire:navigate class="text-white bg-red-600 btn hover:bg-red-700">Batal</a>

                        <button type="submit" class="btn ">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
                    </div>
                </div>
            </form>
            <div x-show="is_member">
                <x-modal name="profesi">
                    @livewire('data-master.profesi.profesi-form', ['isModal' => true])
                </x-modal>
            </div>
        </div>

    </div>
</div>
