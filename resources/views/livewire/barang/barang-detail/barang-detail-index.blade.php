<x-slot:title>Detail Barang</x-slot:title>
@php
    use App\Helpers\FormatHelper;
@endphp
<div>
    <x-breadcrumb name="Detail Data Barang">
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

    @php
        $id_barang = $barang->id;
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

    <div class="card">
        <div class="card-body py-2">
            <div class="grid grid-cols-1 md:grid-cols-2 sm:gap-y-2 sm:gap-x-5 my-2">
                <x-span-grid label="Kode Barang" :data="$barang->kode" />
                <x-span-grid label="Nama Barang" :data="$barang->nama" />
                <x-span-grid label="Stok">
                    <x-slot:data>
                        {{-- @decimal($barang->stok) {{ $barang->satuan_utama->nama }} --}}
                        <p>{{ FormatHelper::formatNumberToString($barang->stok) }} / {{ $barang->satuan_utama->nama }}</p>
                        <p class="font-semibold italic"> = {{ $barang->konversi_stok() }}</p>
                        <p class="text-xs">
                            Min: {{ FormatHelper::formatNumberToString($barang->stok_min) }} {{ $barang->satuan_utama->nama }}
                        </p>
                    </x-slot:data>
                </x-span-grid>

                <x-span-grid label="Satuan">
                    <x-slot:data>
                        <span class="font-normal">Utama:</span>
                        <span class="font-semibold text-dark dark:text-white">{{ $barang->satuan_utama->nama }}</span>
                        @foreach ($barang->opsi_satuan as $opsi_satuan)
                            <p class="text-xs">- {{ $opsi_satuan->pivot->rasio_satuan_opsi }} {{ $opsi_satuan->nama }} =
                                {{ $opsi_satuan->pivot->rasio_satuan_utama }} {{ $barang->satuan_utama->nama }}</p>
                        @endforeach
                    </x-slot:data>
                </x-span-grid>

                <x-span-grid label="Kategori">
                    <x-slot:data>
                        @foreach ($barang->kategories as $kategori)
                            <p>- {{ $kategori->nama }}</p>
                        @endforeach
                    </x-slot:data>
                </x-span-grid>

                <x-span-grid label="Rak">
                    <x-slot:data>
                        @foreach ($barang->raks as $rak)
                            <p>- {{ $rak->nama }}</p>
                        @endforeach
                    </x-slot:data>
                </x-span-grid>

                <x-span-grid label="Harga Beli" :data="'Rp. ' . FormatHelper::formatNumberToString($barang->harga_beli_default)" />

                <x-span-grid label="Harga Jual">
                    <x-slot:data>
                        Rp. {{ FormatHelper::formatNumberToString($barang->harga_jual_utama) }}
                        @foreach ($barang->opsi_harga as $opsi_harga)
                            <div class="mb-2">
                                <p class="text-xs">H.Jual: Rp.
                                    {{ FormatHelper::formatNumberToString($opsi_harga->harga) }}</p>
                                <p class="text-xs">Min:
                                    {{ FormatHelper::formatNumberToString($opsi_harga->jumlah_min) }}/{{ $opsi_harga->satuan->nama }}
                                </p>
                            </div>
                        @endforeach
                    </x-slot:data>
                </x-span-grid>

            </div>

        </div>
    </div>
    {{-- <x-tab class="mt-2">
        <x-slot:tab>
            <x-tab.button number="1" class="active">Stok Barang</x-tab.button>
            <x-tab.button number="2">Info Barang</x-tab.button>
            <x-tab.button number="3">Transaksi Barang</x-tab.button>
            <x-tab.button number="4">Faktur Barang</x-tab.button>
        </x-slot:tab>
        <x-slot:tabItem>
            <x-tab.content number="1" :active=true>
                <livewire:barang.barang-detail.components.stok-barang-index :id_barang="$barang->id" :nama_barang="$barang->nama" :nama_satuan_utama="$barang->satuan_utama->nama" :stok_sistem_barang="$barang->stok"
                                                                            :all_satuan="$barang->all_satuan->sortByDesc('rasio')->values()->toArray()" />
            </x-tab.content>
            <x-tab.content number="2">
                <livewire:informasi.info-barang-expired.info-barang-expired-list :id_barang="$barang->id" :minimum_alert="now()->addYears(10)">
            </x-tab.content>
            <x-tab.content number="3">
                <livewire:barang.barang-detail.components.transaksi-barang-card-info :id_barang="$barang->id" :nama_barang="$barang->nama" :all_satuan="$barang->all_satuan->sortByDesc('rasio')->values()->toArray()" />
                <livewire:transaksi.transaksi-list :id_barang="$barang->id" />
            </x-tab.content>
            <x-tab.content number="4">
                <div class="card">
                    <div class="card-body">
                        <livewire:faktur.faktur-list :id_barang="$barang->id">
                    </div>
                </div>
            </x-tab.content>
        </x-slot:tabItem>
    </x-tab> --}}

</div>
