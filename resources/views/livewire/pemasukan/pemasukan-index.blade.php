<x-slot:title>Kasir</x-slot:title>

<div class="grid grid-cols-12 gap-3">
    <div class="col-span-12 md:col-span-8">
        <div class="mb-3 card">
            <div class="card-body">
                <div class="card-title">
                    <div class="grid w-full grid-cols-12 gap-3">
                        <div class="col-span-6 lg:col-span-4 flex items-center">
                            <h5 class="text-lg font-bold">Data Kasir</h5>
                        </div>
                        <div class="col-span-6 lg:col-span-2">
                            <div class="flex items-center justify-end">
                                <button class="btn btn-sm bg-success hover:bg-successemphasis" wire:click='tambahPesananKasir'>+ New</button>
                                <livewire:kasir.pesanan-kasir.pesanan-kasir-list :pesanan_kasir_selected='$pesanan_kasir->id' />
                            </div>
                        </div>
                        <div class="col-span-6 lg:col-span-3">
                            <x-form.select name="mode_kasir" :direction=false :empty-option=false wire:model.live='mode_kasir'>
                                <x-slot:custom-option>
                                    <option value="UMUM" {{ $mode_kasir == 'UMUM' ? 'selected' : '' }}>KASIR UMUM</option>
                                    <option value="RESEP" {{ $mode_kasir == 'RESEP' ? 'selected' : '' }}>KASIR RESEP</option>
                                </x-slot:custom-option>
                            </x-form.select>
                        </div>
                        <div class="col-span-6 lg:col-span-3 ">
                            <x-form.select name="mode_shift" :direction=false :empty-option=false wire:model.live='mode_shift' error-name="mode_shift">
                                <x-slot:custom-option>
                                    <option value="PAGI" {{ $mode_shift == 'PAGI' ? 'selected' : '' }}>PAGI</option>
                                    <option value="MALAM" {{ $mode_shift == 'MALAM' ? 'selected' : '' }}>MALAM</option>
                                </x-slot:custom-option>
                            </x-form.select>
                        </div>

                    </div>
                </div>
                {{-- @include('livewire.pemasukan.barang-pesanan-pemasukan-form') --}}
                <livewire:pemasukan.pemasukan-barang-pesanan-form :$pesanan_kasir :persen_resep="$setting->persentase_jual_resep" />
                {{-- <livewire:kasir.barang-pesanan-kasir.barang-pesanan-kasir-form :$pesanan_kasir :persen_resep="$setting->persentase_jual_resep" /> --}}

            </div>
        </div>
        {{-- <livewire:kasir.barang-pesanan-kasir.barang-pesanan-kasir-list :$pesanan_kasir :persen_resep="$setting->persentase_jual_resep" /> --}}
    </div>
    <div class="col-span-12 md:col-span-4">
        {{-- <livewire:kasir.kasir-pembayaran-form :$pesanan_kasir> --}}

    </div>

    {{-- Komponen struk (bisa modal atau div yang akan diprint) --}}
    {{-- @livewire('kasir.components.struk-pembayaran') --}}
</div>
