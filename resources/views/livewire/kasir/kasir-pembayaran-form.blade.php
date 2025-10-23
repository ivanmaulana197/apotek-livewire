@php
    use App\Helpers\FormatHelper;
    use App\Helpers\GenerateHelper;
@endphp

@script
    <script>
        Alpine.data('kasirPembayaranForm', () => ({
            total_bayar: $wire.entangle('total_bayar'),
            bayar: null,
            kembalian: null,
            hitungKembalian(value) {
                this.bayar = formatToStringNumber(value);
                if (this.bayar) {
                    this.kembalian = formatStringToNumber(this.bayar) - this.total_bayar;
                    this.kembalian = formatNumberToString(this.kembalian);
                } else {
                    this.kembalian = formatNumberToString(0 - this.total_bayar);
                }
            },
            submit(event) {
                event.preventDefault();
                if (this.total_bayar <= 0) {
                    Livewire.dispatch('alert', {
                        message: 'Silahkan Input Barang Terlebih Dahulu',
                        status: 'error'
                    });
                    Livewire.dispatch('focus-select-barang');
                    return;
                }

                if (!this.bayar || !this.kembalian) {
                    Livewire.dispatch('alert', {
                        message: 'Masukan Nominal Pembayaran',
                        status: 'error'
                    });
                    $('#bayar').focus();
                    return;
                }

                if (formatStringToNumber(this.kembalian) >= 0) {
                    $wire.$call('handlePembayaran', formatStringToNumber(this.bayar), formatStringToNumber(this.kembalian));
                } else {
                    Livewire.dispatch('alert', {
                        message: 'Kembalian tidak mencukupi',
                        status: 'error'
                    });
                    $('#bayar').focus();
                }

            },
            init() {
                $wire.on('total_bayar_update', ({
                    total_bayar
                }) => {
                    this.total_bayar = total_bayar;
                    this.hitungKembalian(this.bayar)
                })
                this.hitungKembalian(this.bayar)

                Livewire.on('struk-pembayaran', ({
                    id_transaksi
                }) => {
                    let url = '{{ route('cetak.struk-pembayaran', ':id_transaksi') }}';
                    url = url.replace(':id_transaksi', id_transaksi);
                    window.open(url, '_blank', 'width=700,height=600');
                })
            }
        }))
    </script>
@endscript

<div class="card" x-data="kasirPembayaranForm">
    <form @submit="submit">
        <div class="card-body p-3 ">
            <div class="card-title bg-warning py-2 px-1 rounded-lg justify-center ">
                <span class="text-4xl text-white font-mono">
                    Rp. {{ FormatHelper::formatNumberToString($total_bayar) }}
                </span>
            </div>
            <div class="flex flex-col gap-x-3">
                <div class="flex justify-between py-1 lg:text-lg px-3 rounded-lg border border-bordergray bg-gray-100  dark:bg-gray-700 dark:border-gray-700">
                    No Transaksi : <span class="text-black dark:text-white font-semibold">
                        {{ GenerateHelper::generateTransaksiId() }}</span>
                </div>
                <x-form.select name="member" label="Pilih Member" :dataOption="$this->members" optionValue="id" optionLabel="nama" addValue="true" :item-selected="$id_member"
                               wire:model.live='id_member' error-name="id_member" />
                @if ($pesanan_kasir->mode_kasir == 'RESEP')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3">
                        <x-form.input label="Nama Pasien" class="text-xs bg-gray-200 dark:bg-gray-800" wire:model.live='nama_pasien' errorName="nama_pasien"
                                      onkeyup="formatToUpperCase(this)" />

                        <x-form.input label="Alamat Pasien" class="text-xs bg-gray-200 dark:bg-gray-800" wire:model.live='alamat_pasien'
                                      errorName="alamat_pasien" onkeyup="formatToUpperCase(this)" />

                        <x-form.input label="No. HP Pasien" class="text-xs bg-gray-200 dark:bg-gray-800" wire:model.live='no_hp_pasien'
                                      errorName="no_hp_pasien" />

                        <x-form.select name="dokter" label="Pilih Dokter" :dataOption="$this->dokters" optionValue="id" optionLabel="nama" :addValue=true
                                       :itemSelected="$dokter_pasien" wire:model.live='dokter_pasien' errorName="dokter_pasien" />
                    </div>
                @endif
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-3">
                <x-form.text-display value="{{ FormatHelper::formatNumberToString($sub_total) }}" label="SUB TOTAL" labelInputGroup="Rp" inputGroup="left"
                                     class="bg-gray-200 dark:bg-gray-800" />
                <x-form.text-display :addValue=true value="{{ FormatHelper::formatNumberToString($diskon) }}" label="POTONGAN" labelInputGroup="Rp"
                                     inputGroup="left" class="bg-gray-200 dark:bg-gray-800">
                    <x-slot:custom-button>
                        <button class="btn p-1.5 btn-sm text-lg" type="button" wire:click='showDiskon'>
                            <span class="iconify iconamoon--discount"></span>
                        </button>
                    </x-slot:custom-button>
                </x-form.text-display>
                <x-form.text-display value="{{ FormatHelper::formatNumberToString($total_bayar - ($sub_total - $diskon)) }}" label="PEMBULATAN"
                                     labelInputGroup="Rp" inputGroup="left" class="bg-gray-200 dark:bg-gray-800" />
                <x-form.text-display value="{{ FormatHelper::formatNumberToString($total_bayar) }}" label="TOTAL BAYAR" labelInputGroup="Rp" inputGroup="left"
                                     class="bg-gray-200 dark:bg-gray-800" />
                <x-form.input id="bayar" x-model="bayar" @keyup="hitungKembalian($el.value)" label="BAYAR" placeholder="Nominal Bayar" labelInputGroup="Rp"
                              inputGroup="left" />
                <x-form.input x-model="kembalian" label="KEMBALI" labelInputGroup="Rp" inputGroup="left" readonly class="bg-gray-200 dark:bg-gray-800"
                              placeholder="Total Kembalian" />
            </div>
            <x-form.select name="metode_pembayaran" label="METODE PEMBAYARAN" :dataOption="$this->metode_pembayarans" optionValue="id" optionLabel="nama" addValue="true"
                           :emptyOption=false :item-selected="$id_metode_pembayaran" wire:model.live='id_metode_pembayaran' error-name="metode_pembayaran" />
            <button class="btn w-full my-2" type="submit">Simpan</button>

        </div>
    </form>
    <x-modal name="member" size="xl">
        @livewire('data-master.member.member-form', ['isModal' => true])
    </x-modal>
    <x-modal name="dokter" size="lg">
        @livewire('data-master.dokter.dokter-form', ['isModal' => true])
    </x-modal>
    <x-modal name="metode_pembayaran" size="lg">
        @livewire('data-master.metode-pembayaran.metode-pembayaran-form', ['isModal' => true])
    </x-modal>
    <x-modal name="diskon_transaksi" size="6xl">
        <livewire:kasir.diskon-transaksi-list />
    </x-modal>
</div>
