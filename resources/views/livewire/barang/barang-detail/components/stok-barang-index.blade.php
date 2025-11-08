@php
    use App\Helpers\FormatHelper;
@endphp

@script
    <script>
        Alpine.data('stokBarangIndex', () => ({
            refs: [],
            temp: [],
            isEditStok: $wire.entangle('isEditStok'),
            stok_sistem_barang: $wire.entangle('stok_sistem_barang'),
            stok_barangs: $wire.entangle('stok_barangs'),
            nama_satuan_utama: $wire.entangle('nama_satuan_utama'),
            all_satuan: $wire.entangle('all_satuan'),
            total_stok_batch_barang: $wire.entangle('total_stok_batch_barang'),
            formattedStok(stok) {
                let hasil = [];
                const EPS = 1e-9; // toleransi error kecil
                const fmod = (a, b) => a - Math.floor(a / b) * b;
                this.all_satuan.forEach((item) => {
                    const rasio = parseFloat(item.rasio);
                    if (rasio <= 0) return;

                    const jumlah = Math.floor((stok / rasio) + EPS);
                    if (jumlah > 0) {
                        hasil.push(`${jumlah} ${item.satuan}`);
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

<div class="card" x-data="stokBarangIndex">
    <div class="card-body">
        <div class="card-title">
            List Stok Barang {{ $nama_barang }}
        </div>

        <div class="">
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
                                <span x-text="item.jumlah + ' ' + nama_satuan_utama +''"></span>
                            </div>
                            <div x-show="item.isEdit">
                                <x-form.input x-model="item.jumlah" input-group='right' @keyup.enter="updateStokBarang(index)" x-init="refs[`jumlah_${index}`] = $el"
                                              label-input-group="{{ $nama_satuan_utama }}" onkeyup="formatForInputNumber(this)" placeholder="Masukan Jumlah"
                                              error-name="stok_barangs.${index}.jumlah" />
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
                                <x-form.input x-model="item.harga_beli" input-group='left' @keyup.enter="updateStokBarang(index)" x-init="refs[`harga_beli_${index}`] = $el"
                                              label-input-group="Rp" onkeyup="formatForInputNumber(this)" placeholder="Masukan Harga Beli"
                                              error-name="stok_barangs.${index}.harga_beli" />
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
                    <x-table.td colspan="2" class="text-center" x-init="temp_stok_sistem_barang = stok_sistem_barang = formatNumberToString(stok_sistem_barang)">
                        <form wire:submit.prevent="updateStokSistemBarang">
                            <span>Stok Sistem : </span>
                            <span x-show="!isEditStok" x-text="stok_sistem_barang + ' ' + nama_satuan_utama"></span>
                            <div x-show="isEditStok">
                                <x-form.input x-model="stok_sistem_barang" input-group='right' x-ref="inputs" label-input-group="{{ $nama_satuan_utama }}"
                                              onkeyup="formatForInputNumber(this)" placeholder="Masukan Jumlah" error-name="stok_sistem_barang" />
                            </div>
                            <p class="font-semibold italic" x-text="formattedStok(formatStringToNumber(stok_sistem_barang))"></->
                    </x-table.td>
                    <x-table.td colspan="2">
                        <span>Selisih : </span>
                        <span
                              x-text="formatNumberToString(total_stok_batch_barang - formatStringToNumber(stok_sistem_barang)) + ' ' + nama_satuan_utama"></span>
                        <p class="font-semibold italic" x-text="formattedStok(total_stok_batch_barang - formatStringToNumber(stok_sistem_barang))"></p>
                    </x-table.td>
                    <x-table.td class="text-center">
                        <x-table.action-button x-show="!isEditStok" tooltip="Edit" color="warning" @click="$focus.focus($refs.inputs); isEditStok = true;">
                            <span class="iconify tabler--edit-circle text-lg"></span>
                        </x-table.action-button>
                        <x-table.action-button x-show="isEditStok" tooltip="Batalkan" color="secondary"
                                               @click="isEditStok = false; stok_sistem_barang = temp_stok_sistem_barang;">
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
            <livewire:barang.barang-detail.components.stok-barang-form :id_barang="$id_barang" :$all_satuan :$nama_satuan_utama />
        </x-modal>

    </div>
</div>
