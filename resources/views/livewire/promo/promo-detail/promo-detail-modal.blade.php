@php
    use App\Helpers\FormatHelper;
@endphp

<div class="card">
    <div class="card-body p-2 flex flex-col max-h-[80vh]">
        <div wire:loading.class='absolute inset-0 z-50 flex items-center justify-center bg-white/70 dark:bg-gray-900/70' wire:loading.remove.class='hidden'
             class="hidden">
            <div class="flex flex-col items-center gap-2">
                <svg class="w-8 h-8 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z">
                    </path>
                </svg>
                <span class="text-xl text-gray-600 dark:text-gray-300">Memuat data promo...</span>
            </div>
        </div>
        <div class="flex items-center justify-between flex-none border-b border-gray-200">
            <h5 class="card-title">Detail Promo</h5>
            <x-modal.button-close @click=" $dispatch('toggle-modal-detailpromo',{ detailpromoModal: false });" />
        </div>
        @if ($promo)
            <div class="overflow-y-auto flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    <x-span-grid label="Nama Promo" data="{{ $promo->nama }}" />

                    <x-span-grid label="Deskripsi" data="{{ $promo->deskripsi ?? '-' }}" textColor="text-gray-600 dark:text-gray-400" />
                    <x-span-grid label="Status">
                        <x-slot:data>
                            @if ($promo->is_aktif == 1)
                                <x-badge message="Aktif" status="success" icon="tabler--checkbox" />
                            @else
                                <x-badge message="Tidak Aktif" status="warning" icon="solar--bill-cross-line-duotone" />
                            @endif
                        </x-slot:data>
                    </x-span-grid>
                    <x-span-grid label="Tipe Promo" data="{{ $promo->tipe ?? '-' }}" />
                    <x-span-grid label="Tanggal Promo"
                                 data="{{ FormatHelper::formatDate($promo->start_date) }} - {{ FormatHelper::formatDate($promo->end_date) ?? '~' }}" />
                    <x-span-grid label="Member?">
                        <x-slot:data>
                            @if ($promo->is_member == 1)
                                <x-badge message="Member" status="warning" icon="solar--key-minimalistic-square-2-line-duotone" />
                            @else
                                <x-badge message="Public" status="success" icon="nrk--globe" />
                            @endif
                        </x-slot:data>
                    </x-span-grid>

                    @if ($promo->is_member)
                        <div class="col-span-2">
                            <x-form.text-display label="List Profesi Member Promo" value="{{ $promo->profesi_list }}" />
                        </div>
                    @endif

                    @if ($promo->tipe == 'BARANG')
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold">Barang Promo</h3>
                        </div>
                        @foreach ($promo->promoBarang as $index => $promoBarang)
                            <div class="col-span-2">
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
                        <div class="col-span-2 grid grid-cols-12">
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
                        </div>

                    @endif
                </div>
            </div>
        @endif
        {{-- button close --}}
        <div class="flex justify-end flex-none gap-2 p-2 mt-2 border-t ">
            <button type="button" @click=" $dispatch('toggle-modal-detailpromo',{ detailpromoModal: false });"
                    class="btn bg-red-500 hover:bg-red-600 text-white">Close</button>
        </div>
    </div>
</div>
