<x-slot:title>Detail Barang Stok</x-slot:title>

<div>
    <x-breadcrumb name="Detail Stok Data Barang {{ $barang['nama'] }}">
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

    @script
        <script>
            Alpine.data('stokBarangIndex', () => ({
                refs: [],
                temp: [],
                barang: $wire.entangle('barang'),
                isEditStok: $wire.entangle('isEditStok'),
                // stok_sistem_barang: $wire.entangle('barang.stok'),
                stok_barangs: $wire.entangle('stok_barangs'),
                // nama_satuan_utama: $wire.entangle('barang.satuan_utama.nama'),
                // all_satuan: $wire.entangle('barang.all_satuan'),
                total_stok_batch_barang: $wire.entangle('total_stok_batch_barang'),
                formattedStok(stok) {
                    let hasil = [];
                    const EPS = 1e-9; // toleransi error kecil
                    const fmod = (a, b) => a - Math.floor(a / b) * b;
                    let all_satuan = [...this.barang.all_satuan].sort((a, b) => b.rasio - a.rasio)
                    all_satuan.forEach((item) => {
                        const rasio = parseFloat(item.rasio);
                        if (rasio <= 0) return;

                        const jumlah = Math.floor((stok / rasio) + EPS);
                        if (jumlah > 0) {
                            hasil.push(`${formatNumberToString(jumlah)} ${item.satuan}`);
                        }

                        stok = fmod(stok, rasio);
                    })
                    // kalau sisa stok masih lebih besar dari 0.0001 (karena float)
                    // if (stok > EPS) {
                    //     hasil.push(`${stok.toFixed(4)} (sisa)`);
                    // }
                    return hasil.join(',  ');
                },

                editStokBarang(index) {
                    this.stok_barangs[index].isEdit = true;
                    this.$nextTick(() => {
                        this.refs['jumlah_' + index]?.focus();
                    });
                    this.temp[index] = {
                        'jumlah': this.stok_barangs[index].jumlah,
                        'tgl_expired': this.stok_barangs[index].tgl_expired,
                        'harga_beli': this.stok_barangs[index].harga_beli
                    }
                },
                cancelStokBarang(index) {
                    // $wire.resetValidation([`stok_barangs.${index}.jumlah`, `stok_barangs.${index}.tgl_expired`, `stok_barangs.${index}.harga_beli`]);
                    $wire.cancelStokBarang(index);
                    this.stok_barangs[index].jumlah = this.temp[index].jumlah;
                    this.stok_barangs[index].tgl_expired = this.temp[index].tgl_expired;
                    this.stok_barangs[index].harga_beli = this.temp[index].harga_beli;
                    this.stok_barangs[index].isEdit = false;

                },
                updateStokBarang(index) {
                    $wire.updateStokBarang(index);
                    // this.stok_barangs[index].isEdit = true;
                }
            }))
        </script>
    @endscript

    <div x-data="stokBarangIndex">
        @php
            $id_barang = $barang['id'];
        @endphp
        <div class="card mb-2">
            <div class="card-body p-2">
                <div class="flex flex-wrap gap-2">
                    <a wire:navigate href="{{ route('data.barang.detail', $id_barang) }}"
                       class="btn btn-sm bg-dark text-blue-400 hover:text-blue-500 dark:text-blue-600 hover:bg-gray-700 dark:bg-gray-200  dark:hover:bg-gray-300 ">
                        <span class="iconify solar--eye-bold text-lg"></span> Detail
                        Barang</a>
                    <a wire:navigate href="{{ route('data.barang.edit', $id_barang) }}"
                       class="btn btn-sm bg-dark text-yellow-500 hover:text-yellow-600 dark:text-orange-400 hover:dark:text-orange-300 hover:bg-gray-700 dark:bg-gray-200 dark:hover:bg-gray-300"><span
                              class="iconify tabler--edit-circle text-lg"></span> Edit Barang</a>
                    <a wire:navigate href="{{ route('data.barang.detail.faktur', $id_barang) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-300">Detail
                        Faktur Barang</a>
                    <a wire:navigate href="{{ route('data.barang.detail.stok', $id_barang) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-300">Detail
                        Stok Barang</a>
                    <a wire:navigate href="{{ route('data.barang.detail.expired', $id_barang) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-300">Detail
                        Expired Barang</a>
                    <a wire:navigate href="{{ route('data.barang.detail.transaksi', $id_barang) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-300">Detail Transaksi
                        Barang</a>
                </div>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-body">
                <div class="card-title">Info Barang {{ $barang['nama'] }}</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <x-span-grid label="Stok">
                        <x-slot:data>
                            {{-- @decimal($barang->stok) {{ $barang->satuan_utama->nama }} --}}
                            <p x-text="formatNumberToString(barang.stok) + ' / ' + barang.satuan_utama.nama"></p>
                            <p class="font-semibold italic" x-text="formattedStok(barang.stok)"></p>
                            <p class="text-xs" x-text="'Min: ' + formatNumberToString(barang.stok_min) + ' ' + barang.satuan_utama.nama"></p>
                        </x-slot:data>
                    </x-span-grid>
                    <x-span-grid label="Satuan">
                        <x-slot:data>
                            <span class="font-normal">Utama:</span>
                            <span class="font-semibold text-dark dark:text-white" x-text="barang.satuan_utama.nama"></span>
                            <template x-for="opsi_satuan in barang.opsi_satuan" :key="opsi_satuan.id">
                                <p class="text-xs">
                                    - <span x-text="opsi_satuan.pivot.rasio_satuan_opsi"></span> <span x-text="opsi_satuan.nama"></span> =
                                    <span x-text="opsi_satuan.pivot.rasio_satuan_utama"></span> <span x-text="barang.satuan_utama.nama"></span>
                                </p>
                            </template>

                        </x-slot:data>
                    </x-span-grid>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="card-title" x-text="'List Stok Barang : '+barang.nama"></div>

                <div>
                    <button class="btn btn-sm" @click="$dispatch('toggle-modal-edit_stok_barang', {edit_stok_barangModal: true})">Tambah Stok +</button>
                </div>

                <x-table nama="stok_barang" height="h-full">
                    <x-table.thead>
                        <x-table.th label="No" />
                        <x-table.th label="Jumlah Stok" />
                        <x-table.th label="Tanggal Expired" />
                        <x-table.th label="Harga Beli" />
                        <x-table.th label='Aksi' class="flex justify-center" />
                    </x-table.thead>
                    <x-table.tbody>
                        <template x-for="(item, index) in stok_barangs" :key="index" wire:key='table-faktur-barang'>
                            <x-table.tr x-init="">
                                <x-table.td class="text-center" x-text="index + 1" />
                                <x-table.td>
                                    <div x-show="!item.isEdit">
                                        <span x-text="item.jumlah + ' ' + barang.satuan_utama.nama +''"></span>
                                    </div>
                                    <div x-show="item.isEdit">
                                        <x-form.input x-model="item.jumlah" input-group='right' @keyup.enter="updateStokBarang(index)" x-init="refs[`jumlah_${index}`] = $el"
                                                      label-input-group="{{ $barang['satuan_utama']['nama'] }}" onkeyup="formatForInputNumber(this)"
                                                      placeholder="Masukan Jumlah" error-name="stok_barangs.${index}.jumlah" />
                                    </div>
                                    <span class="italic font-bold" x-text="'= '+formattedStok(formatStringToNumber(item.jumlah))"></span>
                                </x-table.td>
                                <x-table.td>
                                    <div x-show="item.isEdit">
                                        <x-alpine.form.input-date minDate='{{ now()->toDateString() }}' ::nama="'tgl_expired_' + index" :singleDatePicker=true
                                                                  x-model="item.tgl_expired" errorName="stok_barangs.${index}.tgl_expired" :ranges=false />
                                    </div>
                                    <span x-show="!item.isEdit" x-text="item.tgl_expired"></span>
                                </x-table.td>
                                <x-table.td>
                                    <div x-show="item.isEdit">
                                        <x-form.input x-model="item.harga_beli" input-group='left' @keyup.enter="updateStokBarang(index)"
                                                      x-init="refs[`harga_beli_${index}`] = $el" label-input-group="Rp" onkeyup="formatForInputNumber(this)"
                                                      placeholder="Masukan Harga Beli" error-name="stok_barangs.${index}.harga_beli" />
                                    </div>
                                    <span x-show="!item.isEdit" x-text="item.harga_beli ? 'Rp. '+item.harga_beli : '-'"></span>
                                </x-table.td>
                                <x-table.td class="text-center">
                                    <x-table.action-button x-show="!item.isEdit" tooltip="Edit" color="warning" @click="editStokBarang(index)">
                                        <span class="iconify tabler--edit-circle text-lg"></span>
                                    </x-table.action-button>
                                    <x-table.action-button x-show="item.isEdit" tooltip="Batalkan" color="secondary" @click="cancelStokBarang(index)">
                                        <span class="iconify mdi--close text-lg"></span>
                                    </x-table.action-button>
                                    <x-table.action-button x-show="item.isEdit" tooltip="Simpan" color="success" @click="updateStokBarang(index)">
                                        <span class="iconify tabler--check text-lg"></span>
                                    </x-table.action-button>

                                    <x-table.action-button x-show="!item.isEdit" tooltip="Hapus" color="error"
                                                           @click="$wire.dispatch('alert-confirm', { id: item.id, method: 'delete-stok-barang' });">
                                        <span class="iconify tabler--trash-x-filled text-lg"></span>
                                    </x-table.action-button>

                                </x-table.td>

                            </x-table.tr>
                        </template>
                        <x-table.tr>
                            <x-table.td colspan="2" class="text-center" x-init="temp_stok_sistem_barang = barang.stok = formatNumberToString(barang.stok)">
                                <form wire:submit.prevent="updateStokSistemBarang">
                                    <span>Stok Sistem : </span>
                                    <span x-show="!isEditStok" x-text="barang.stok + ' ' + barang.satuan_utama.nama"></span>
                                    <div x-show="isEditStok">
                                        <x-form.input x-model="barang.stok" input-group='right' x-ref="inputs"
                                                      label-input-group="{{ $barang['satuan_utama']['nama'] }}" onkeyup="formatForInputNumber(this)"
                                                      placeholder="Masukan Jumlah" error-name="barang.stok" />
                                    </div>
                                    <p class="font-semibold italic" x-text="formattedStok(formatStringToNumber(barang.stok))"></->
                            </x-table.td>
                            <x-table.td colspan="2">
                                <span>Selisih : </span>
                                <span
                                      x-text="formatNumberToString(total_stok_batch_barang - formatStringToNumber(barang.stok)) + ' ' + barang.satuan_utama.nama"></span>
                                <p class="font-semibold italic" x-text="formattedStok(total_stok_batch_barang - formatStringToNumber(barang.stok))">
                                </p>
                            </x-table.td>
                            <x-table.td class="text-center">
                                <x-table.action-button x-show="!isEditStok" tooltip="Edit" color="warning"
                                                       @click="$focus.focus($refs.inputs); isEditStok = true;">
                                    <span class="iconify tabler--edit-circle text-lg"></span>
                                </x-table.action-button>
                                <x-table.action-button x-show="isEditStok" tooltip="Batalkan" color="secondary"
                                                       @click="isEditStok = false; barang.stok = temp_stok_sistem_barang;">
                                    <span class="iconify mdi--close text-lg"></span>
                                </x-table.action-button>
                                <x-table.action-button x-show="isEditStok" tooltip="Simpan" type="submit" color="success">
                                    <span class="iconify tabler--check text-lg"></span>
                                </x-table.action-button>
                                </form>
                            </x-table.td>
                        </x-table.tr>

                    </x-table.tbody>

                </x-table>

                <x-modal name="edit_stok_barang">
                    <livewire:barang.barang-detail.components.stok-barang-form :id_barang="$barang['id']" :all_satuan="$barang['all_satuan']" :nama_satuan_utama="$barang['satuan_utama']['nama']" />
                </x-modal>

            </div>
        </div>
    </div>

</div>
