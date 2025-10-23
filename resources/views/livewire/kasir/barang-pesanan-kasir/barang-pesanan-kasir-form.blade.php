@php
    use App\Helpers\FormatHelper;
    // use App\Helpers\GenerateHelper;
@endphp

@script
    <script>
        Alpine.data('barangPesananKasirForm', () => ({
            jumlah: $wire.entangle('jumlah'),
            id_satuan: $wire.entangle('id_satuan'),
        }))
    </script>
@endscript
<div class="">
    <form wire:submit='addBarangPesananKasir'>
        <div class="grid grid-cols-12 gap-2">
            <div class="col-span-12 md:col-span-6 lg:col-span-4">
                <livewire:kasir.components.search-barang />
            </div>
            <div class="col-span-12 md:col-span-6 lg:col-span-4">
                <div class="grid grid-cols-2 join " @focus-input-jumlah.window="$focus.focus($refs.input_jumlah)">
                    {{-- <x-form.label label="Input Jumlah" /> --}}
                    <x-form.input onkeyup="formatForInputNumber(this)" placeholder="Masukan Jumlah" wire:model.live="jumlah" class="join-item" error-name="jumlah"
                                  x-ref="input_jumlah" />
                    <x-form.select name="satuan" placeholder="Pilih Satuan" :emptyOption="false" :dataOption="[]" optionValue="id" wire:model.live='id_satuan'
                                   error-name="satuan" optionLabel="nama" class="join-item" />
                </div>
            </div>
            <div class="col-span-12 md:col-span-6 lg:col-span-4">
                <div class="grid grid-cols-3 md:grid-cols-1 lg:grid-cols-3 gap-2">
                    @include('livewire.kasir.barang-pesanan-kasir.components.badge-tooltip-info-satuan')
                    @include('livewire.kasir.barang-pesanan-kasir.components.badge-tooltip-info-grosir')
                    <button class="btn btn-sm" type="button" wire:click='showDiskon'>List Diskon</button>
                </div>
            </div>
            <div class="col-span-12 md:col-span-6 lg:col-span-12">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-2">
                    <div
                         class="flex justify-between py-1 px-3 rounded-lg border border-bordergray bg-gray-100 text-gray-500 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-700">
                        <span>Stok :</span>
                        <span>{{ FormatHelper::formatNumberToString($data_barang->stok ?? 0) }} /
                            {{ $data_barang->satuan_utama->nama ?? '' }}</span>
                    </div>
                    <div
                         class="flex justify-between py-1 px-3 rounded-lg border border-bordergray bg-gray-100 text-gray-500  dark:text-gray-400  dark:bg-gray-700 dark:border-gray-700">
                        <span>Harga:</span>
                        <span class="font-bold">Rp. {{ FormatHelper::formatNumberToString($harga_jual ?? 0) }}</span>
                    </div>
                    <div
                         class="flex justify-between py-1 px-3 rounded-lg border border-bordergray bg-gray-100 text-gray-500  dark:text-gray-400  dark:bg-gray-700 dark:border-gray-700">
                        <span>Sub Total:</span>
                        <span class="font-bold">Rp. {{ FormatHelper::formatNumberToString($subtotal ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-span-12">
                <button type="submit" class="btn btn-sm bg-primary w-full ">Tambah +</button>
            </div>
        </div>
    </form>
    <x-modal name="diskon" size="6xl">
        <livewire:kasir.diskon-list :id_pesanan_kasir="$pesanan_kasir->id" />
    </x-modal>
</div>
