@script
    <script>
        Alpine.data('fakturBarangForm', () => ({
            faktur_barang: $wire.entangle('faktur_barang'),
            addFakturBarang() {
                this.faktur_barang.push({
                    id_barang: null,
                    jumlah: null,
                    id_satuan: null,
                    tgl_expired: null,
                    harga_beli: null,
                    isEdit: true,
                });
            },
            deleteSelectBarang(index) {
                this.faktur_barang[index].id_barang = null;
                this.faktur_barang[index].barang = null;
                this.faktur_barang[index].id_satuan = null;
                this.faktur_barang[index].satuan = null;
                $dispatch('update_satuan_barang_' + index, {
                    data: [],
                    selected: null
                });
            },
            editFakturBarang(index) {
                this.faktur_barang[index].isEdit = true;
                // $wire.editFakturBarang(index);
                $dispatch('update_satuan_barang_' + index, {
                    data: this.faktur_barang[index]['barang']['all_satuan'],
                    selected: this.faktur_barang[index]['id_satuan']
                });
            },
            async removeFakturBarang(index) {
                if (this.faktur_barang[index].id) {
                    let res = await $wire.removeFakturBarang(index);
                    if (res.success) {
                        this.faktur_barang.splice(index, 1);
                    }
                } else {
                    this.faktur_barang.splice(index, 1);
                }
            }

        }));
    </script>
@endscript

@php
    use App\Helpers\FormatHelper;
@endphp

<div x-data="fakturBarangForm">

    <div class="grid grid-cols-12 gap-x-2">
        <div class="col-span-12 mb-2">
            <x-table nama="faktur-barang" height="auto">
                <x-table.thead>
                    <x-table.th label="NO." class="min-w-5" />
                    <x-table.th label="Nama Barang" class="min-w-64"  />
                    <x-table.th label="Jumlah" class="min-w-60"  />
                    {{-- <x-table.th label="Stok Real" /> --}}
                    {{-- <x-table.th label="H.Jual" /> --}}
                    {{-- <x-table.th label="H.Grosir" /> --}}
                    <x-table.th label="Tgl Expired" class="min-w-48"  />
                    <x-table.th label="Harga Beli" class="min-w-48" />
                    <x-table.th label="Aksi" class="min-w-28"    />
                </x-table.thead>
                <x-table.tbody>
                    <template x-for="(item, index) in faktur_barang" :key="index" wire:key='table-faktur-barang'>
                        <x-table.tr>
                                <x-table.td class="text-center" x-text="index + 1"></x-table.td>
                                <x-table.td>
                                    <h6 x-show="!item.isEdit" x-text="item.barang?.nama"></h6>
                                    <div x-show="item.isEdit" class="flex join">
                                        <div @click="$dispatch('toggle-modal-select_barang', { select_barangModal: true, id_barang: item.id_barang, index: index })"
                                             x-text="item.barang?.nama ? item.barang?.nama+' ['+item.barang?.stok+']' : 'Select Barang ...'"
                                             class="join-item flex items-center ps-5 pe-2 text-xs rounded-l-lg border border-gray-400 dark:border-gray-700 cursor-pointer w-full text-black dark:text-white text-start">
                                        </div>
                                        <button x-show="item.id_barang" @click="deleteSelectBarang(index)" type="button"
                                                class="btn join-item rounded-r-full dark:bg-lighterror dark:text-error hover:bg-erroremphasis dark:hover:bg-error dark:hover:text-white">
                                            <i class="ti ti-trash-filled text-lg leading-tight font-medium"></i>
                                            Hapus
                                        </button>
                                        <button x-show="!item.id_barang" type="button"
                                                class="btn join-item rounded-r-full dark:bg-lightprimary dark:text-primary hover:bg-primaryemphasis dark:hover:bg-primary dark:hover:text-white">
                                            <i class="ti ti-search text-lg leading-tight font-medium"></i>
                                            Search
                                        </button>
                                    </div>
                                    <div x-show="item.isEdit">
                                        <x-error-bag errorName="`faktur_barang.${index}.id_barang`" />
                                    </div>

                                </x-table.td>
                                <x-table.td class="text-center">
                                    <h6 x-show="!item.isEdit" x-text="item.jumlah+' / '+item.satuan?.nama"></h6>
                                    <div x-show="item.isEdit" class="grid grid-cols-12 join border border-bordergray dark:border-darkborder">
                                        <div class="col-span-6">
                                            <x-form.input @keyup.enter=" $wire.createOrUpdateFakturBarang(index)" onkeyup="formatForInputNumber(this)" placeholder="Masukan Jumlah" x-model='item.jumlah'
                                                          class="join-item" error-name="faktur_barang.${index}.jumlah" x-ref="input_jumlah" />
                                        </div>
                                        <div class="col-span-6">
                                            <x-form.select-alpine ::name="'satuan_barang_' + index" :emptyOption="false" :dataOption="[]" optionValue="id_satuan"
                                                                  x-model='item.id_satuan' error-name="faktur_barang.${index}.id_satuan" optionLabel="satuan"
                                                                  class="join-item" />
                                        </div>
                                    </div>
                                </x-table.td>
                                <x-table.td class="text-center">
                                    <h6 x-show="!item.isEdit" x-text="item.tgl_expired"></h6>
                                    <div x-show="item.isEdit">
                                        <x-alpine.form.input-date name="tgl_expired" ::id="'tgl_expired' + index" :singleDatePicker=true x-model="item.tgl_expired"
                                                                  errorName="faktur_barang.${index}.tgl_expired" :ranges=false />
                                    </div>
                                </x-table.td>
                                <x-table.td class="text-center">
                                    <h6 x-show="!item.isEdit" x-text="item.harga_beli"></h6>
                                    <div x-show="item.isEdit">
                                        <x-form.input onkeyup="formatForInputNumber(this)" placeholder="Masukan Harga Beli" x-model='item.harga_beli'
                                                      error-name="faktur_barang.${index}.harga_beli" x-ref="input_harga_beli" />
                                    </div>
                                </x-table.td>
                                <x-table.td class="text-center">
                                    <x-table.action-button x-show="!item.isEdit" tooltip="Edit" color="warning" @click="editFakturBarang(index)">
                                        <i class="ti ti-edit"></i>
                                    </x-table.action-button>
                                    <x-table.action-button x-show="item.isEdit" tooltip="Simpan" color="success"
                                                           @click=" $wire.createOrUpdateFakturBarang(index)">
                                        <i class="ti ti-check"></i>
                                    </x-table.action-button>
                                    <x-table.action-button tooltip="Hapus" color="error" @click="removeFakturBarang(index)">
                                        <i class="ti ti-trash-x-filled"></i>
                                    </x-table.action-button>
                                </x-table.td>

                            </x-table.tr>
                    </template>
                    <tr>
                        <td colspan="2" class="p-2">
                            <button @click="addFakturBarang" type="button" class="btn btn-sm btn-primary">Tambah Barang <i class="ti ti-plus"></i></button>
                        </td>
                    </tr>
                </x-table.tbody>
            </x-table>

            <livewire:faktur.select-barang />
        </div>

    </div>
</div>
