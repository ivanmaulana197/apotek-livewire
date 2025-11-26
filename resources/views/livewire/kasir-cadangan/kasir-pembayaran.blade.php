@php
    use App\Helpers\GenerateHelper;
@endphp

@script
    <script>
        Alpine.data('kasirPembayaran', () => ({
            'setting': $wire.entangle('setting'),

            'mode_kasir': $wire.entangle('mode_kasir'),
            'sub_total': $wire.entangle('sub_total'),
            'diskon': $wire.entangle('diskon'),
            'total_bayar': $wire.entangle('total_bayar'),
            'id_member': $wire.entangle('id_member'),
            'promo_pesanan_kasir': $wire.entangle('promo_pesanan_kasir'),
            'bayar': $wire.entangle('bayar'),
            'kembalian': $wire.entangle('kembalian'),
            init() {
                this.kembalian = formatNumberToString(0 - this.total_bayar);
                Livewire.on('barang_pesanan_updated', ({
                    list_total_jumlah_dalam_satuan_utama,
                    pesanan_kasir
                }) => {
                    this.$nextTick(() => {
                        this.promo_pesanan_kasir = pesanan_kasir.promo_pesanan_kasir;
                        this.mode_kasir = pesanan_kasir.mode_kasir;
                        this.id_member = pesanan_kasir.id_member;
                        this.sub_total = Math.ceil(pesanan_kasir.total_harga);
                        this.diskon = Math.ceil(pesanan_kasir.promo_pesanan_kasir.reduce((sum, item) => sum + Number(item
                            .nominal_promo), 0));
                        this.total_bayar = Math.ceil(this.pembulatanTotalHarga(this.sub_total - this.diskon, false));
                        this.hitungKembalian(formatStringToNumber(this.bayar));
                    })

                });

                Livewire.on('select-promo_transaksi', ({
                    id_promo_transaksi
                }) => {
                    $wire.updatePromoTransaksi(id_promo_transaksi);
                });
            },

            showPromoTransaksiModal() {
                $dispatch('toggle-modal-promo_transaksi', {
                    promo_transaksiModal: true
                });
                let promo_selected = this.promo_pesanan_kasir.filter((item) => item.id_promo_transaksi != null)[0];
                let total_belanja = this.sub_total;
                let total_diskon = this.promo_pesanan_kasir.filter((item) => item.id_promo_transaksi == null).reduce(
                    (sum, item) => sum + item.nominal_promo, 0
                );
                total_belanja = total_belanja - total_diskon;
                console.log(promo_selected?.id_promo_transaksi)
                console.log(total_belanja)
                $dispatch('list-promo-transaksi', {
                    id_promo_transaksi_selected: promo_selected?.id_promo_transaksi ?? null,
                    id_member: this.id_member,
                    total_belanja: total_belanja,
                });
            },
            pembulatanTotalHarga(number, format = true) {
                let pembulatan = 0;
                if (this.mode_kasir == 'UMUM') {
                    pembulatan = this.setting.pembulatan_total;
                } else if (this.mode_kasir == 'RESEP') {
                    pembulatan = this.setting.pembulatan_resep;
                }

                let temp = Math.ceil(number / pembulatan) * pembulatan;
                return format ? formatNumberToString(temp) : temp;
            },
            hitungKembalian(value) {
                this.bayar = formatToStringNumber(value);

                this.kembalian = Math.ceil(formatStringToNumber(this.bayar) - this.total_bayar);
                this.kembalian = formatNumberToString(this.kembalian);

            },
        }))
    </script>
@endscript

<div class="card" x-data="kasirPembayaran">
    <form @submit="submit">
        <div class="card-body p-3 ">
            <div class="card-title bg-warning py-2 px-1 rounded-lg justify-center ">
                <span class="text-4xl text-white font-mono" x-text="'Rp. '+formatNumberToString(total_bayar)">
                    {{-- Rp. {{ FormatHelper::formatNumberToString($total_bayar) }} --}}
                </span>
            </div>
            <div class="flex flex-col gap-x-3">
                <div class="flex justify-between py-1 lg:text-lg px-3 rounded-lg border border-bordergray bg-gray-100  dark:bg-gray-700 dark:border-gray-700">
                    No Transaksi : <span class="text-black dark:text-white font-semibold">
                        {{ GenerateHelper::generateTransaksiId() }}
                    </span>
                </div>
                <x-form.select name="member" label="Pilih Member" :dataOption="$members" optionValue="id" optionLabel="nama" addValue="true" :item-selected="$id_member"
                               wire:model.live='id_member' error-name="id_member" />

                <template x-if="mode_kasir == 'RESEP'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3">
                        <x-form.input label="Nama Pasien" class="text-xs bg-gray-200 dark:bg-gray-800" wire:model.live='nama_pasien' errorName="nama_pasien"
                                      onkeyup="formatToUpperCase(this)" />

                        <x-form.input label="Alamat Pasien" class="text-xs bg-gray-200 dark:bg-gray-800" wire:model.live='alamat_pasien'
                                      errorName="alamat_pasien" onkeyup="formatToUpperCase(this)" />

                        <x-form.input label="No. HP Pasien" class="text-xs bg-gray-200 dark:bg-gray-800" wire:model.live='no_hp_pasien'
                                      errorName="no_hp_pasien" />

                        <x-form.select name="dokter" label="Pilih Dokter" :dataOption="$dokters" optionValue="id" optionLabel="nama" :addValue=true
                                       :itemSelected="$dokter_pasien" wire:model.live='dokter_pasien' errorName="dokter_pasien" />
                    </div>
                </template>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-3">
                <x-form.text-display x-text="formatNumberToString(sub_total)" label="SUB TOTAL" labelInputGroup="Rp" inputGroup="left"
                                     class="bg-gray-200 dark:bg-gray-800" />
                <x-form.text-display :addValue=true x-text="formatNumberToString(diskon)" label="POTONGAN" labelInputGroup="Rp" inputGroup="left"
                                     class="bg-gray-200 dark:bg-gray-800">
                    <x-slot:custom-button>
                        <button class="btn p-1.5 btn-sm text-lg" type="button" @click="showPromoTransaksiModal">
                            <span class="iconify iconamoon--discount"></span>
                        </button>
                    </x-slot:custom-button>
                </x-form.text-display>
                <x-form.text-display x-text="formatNumberToString(total_bayar - (sub_total - diskon))" label="PEMBULATAN" labelInputGroup="Rp" inputGroup="left"
                                     class="bg-gray-200 dark:bg-gray-800" />
                <x-form.text-display x-text="formatNumberToString(total_bayar)" label="TOTAL BAYAR" labelInputGroup="Rp" inputGroup="left"
                                     class="bg-gray-200 dark:bg-gray-800" />
                <x-form.input id="bayar" x-model="bayar" @keyup="hitungKembalian($el.value)" label="BAYAR" placeholder="Nominal Bayar" labelInputGroup="Rp"
                              inputGroup="left" />
                <x-form.input x-model="kembalian" label="KEMBALI" labelInputGroup="Rp" inputGroup="left" readonly class="bg-gray-200 dark:bg-gray-800"
                              placeholder="Total Kembalian" />
            </div>
            <x-form.select name="metode_pembayaran" label="METODE PEMBAYARAN" :dataOption="$metode_pembayarans" optionValue="id" optionLabel="nama" addValue="true"
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
    <x-modal name="promo_transaksi" size="6xl">
        <livewire:kasir-cadangan.promo.promo-transaksi-list />
    </x-modal>
</div>
