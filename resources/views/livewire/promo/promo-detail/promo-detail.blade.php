<x-slot:title>Data Promo</x-slot:title>

@php
    use App\Helpers\FormatHelper;
@endphp
<div>
    <x-breadcrumb name="Detail Promo {{ $promo->nama }}">
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

        <div class="card">
            <div class="card-body">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 md:col-span-6">
                        <x-span-grid label="Nama Promo" :data="$promo->nama" />
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <x-span-grid label="Deskripsi" data="{{ $promo->deskripsi ?? '-' }}" textColor="text-gray-600 dark:text-gray-400" />
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <x-span-grid label="Status">
                            <x-slot:data>
                                @if ($promo->is_aktif == 1)
                                    <x-badge message="Aktif" status="success" icon="gg:check-r" />
                                @else
                                    <x-badge message="Tidak Aktif" status="warning" icon="gg:close-r" />
                                @endif
                            </x-slot:data>
                        </x-span-grid>
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <x-span-grid label="Tipe Promo" data="{{ $promo->tipe ?? '-' }}" />

                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <x-span-grid label="Tanggal Promo"
                                     data="{{ FormatHelper::formatDate($promo->start_date) }} - {{ FormatHelper::formatDate($promo->end_date) ?? '~' }}" />
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <x-span-grid label="Member?">
                            <x-slot:data>
                                @if ($promo->is_member == 1)
                                    <x-badge message="Member" status="warning" icon="ic:round-key" />
                                @else
                                    <x-badge message="Public" status="success" icon="ic:sharp-public" />
                                @endif
                            </x-slot:data>
                        </x-span-grid>
                    </div>
                    @if ($promo->is_member)
                        <div class="col-span-12">
                            <x-form.text-display label="List Profesi Member Promo" value="{{ $promo->profesi_list }}" />
                        </div>
                    @endif

                    @if ($promo->tipe == 'BARANG')
                        <div class="col-span-12">
                            <h3 class="text-lg font-semibold">Barang Promo</h3>
                        </div>
                        @foreach ($promo->promoBarang as $index => $promoBarang)
                            <div class="col-span-12">
                                <div class="p-2 border rounded-md flex">
                                    <!-- Nomor urut -->
                                    <div class="flex items-start mr-2">
                                        <div
                                             class="bg-primary-500 text-gray-900 dark:text-white w-7 h-7 flex items-center justify-center rounded-full text-sm font-bold">
                                            #{{ $index + 1 }}
                                        </div>
                                    </div>

                                    <!-- Konten data -->
                                    <div class="flex-1 grid grid-cols-12 gap-4">
                                        <div class="col-span-12 md:col-span-6">
                                            <x-form.text-display label="Barang" value="{{ $promoBarang->barang->nama }}" />
                                        </div>
                                        <div class="col-span-12 md:col-span-6">
                                            <x-form.text-display label="Syarat Qty"
                                                                 value="{{ $promoBarang->tipe_qty }} : {{ $promoBarang->syarat_qty }} / {{ $promoBarang->satuan->nama }}" />
                                        </div>

                                        <div class="{{ $promoBarang->tipe === 'BARANG BONUS' ? 'col-span-12 md:col-span-4' : 'col-span-12 sm:col-span-6' }}">
                                            <x-form.text-display label="Tipe Barang Promo" value="{{ $promoBarang->tipe }}" />
                                        </div>
                                        @if ($promoBarang->tipe === 'BARANG BONUS')
                                            <div class="col-span-12 md:col-span-8">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <x-form.text-display label="Barang Bonus" value="{{ $promoBarang->barang_bonus->nama }}" />
                                                    <x-form.text-display label="Qty Barang Bonus" value="{{ $promoBarang->bonus_qty }}" />
                                                </div>
                                            </div>
                                        @elseif ($promoBarang->tipe === 'PERSEN')
                                            <div class="col-span-12 sm:col-span-6">
                                                <x-form.text-display label="Persen Promo Barang" labelInputGroup="%" inputGroup="right"
                                                                     value="{{ FormatHelper::formatNumberToString($promoBarang->diskon_persen) }}" />
                                            </div>
                                        @elseif ($promoBarang->tipe === 'HARGA')
                                            <div class="col-span-12 sm:col-span-6">
                                                <x-form.text-display label="Nominal Promo Barang" labelInputGroup="Rp" inputGroup="left"
                                                                     value="{{ FormatHelper::formatNumberToString($promoBarang->diskon_nominal) }}" />
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @elseif ($promo->tipe == 'TRANSAKSI')
                        <div class="col-span-12">
                            <h3 class="text-lg font-semibold">Promo Transaksi</h3>
                        </div>
                        <div class="col-span-12 lg:col-span-4">
                            <x-form.text-display label="Minimal Belanja" labelInputGroup="Rp" inputGroup="left"
                                                 value="{{ FormatHelper::formatNumberToString($promo->promoTransaksi->minimal_belanja) }}" />
                        </div>
                        <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                            <x-form.text-display label="Tipe Promo Transaksi" value="{{ $promo->promoTransaksi->tipe_transaksi }}" />
                        </div>
                        @if ($promo->promoTransaksi->tipe_transaksi === 'PERSEN')
                            <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                                <x-form.text-display label="Persen Promo" labelInputGroup="%" inputGroup="right"
                                                     value="{{ FormatHelper::formatNumberToString($promo->promoTransaksi->diskon_persen) }}" />
                            </div>
                        @elseif ($promo->promoTransaksi->tipe_transaksi === 'HARGA')
                            <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                                <x-form.text-display label="Nominal Promo" labelInputGroup="Rp" inputGroup="left"
                                                     value="{{ FormatHelper::formatNumberToString($promo->promoTransaksi->diskon_nominal) }}" />
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
