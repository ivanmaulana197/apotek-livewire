<x-slot:title>Detail Expired Barang</x-slot:title>

<div class="">
    <x-breadcrumb name="Detail Expired Data Barang">
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

    <x-badge class="w-full !flex-col ps-5 mb-2">
        <x-slot:icon>
            <i class="iconify solar--info-circle-bold text-lg mt-1 text-blue-700 dark:text-info"></i>
            <span class="text-lg text-blue-700 dark:text-info">Catatan Penting</span>
        </x-slot:icon>
        <x-slot:message>
            <div class="">
                <ul class="list-disc ms-5 pe-6 text-sm font-normal text-gray-700 dark:text-white mb-2">
                    <li>Jika Barang sudah tidak ada / atau sudah laku. Info memilih HAPUS INFO</li>
                    <li>Keterangan warna, Pilih Tampil 100 data di Show table untuk mempermudah pengecekan</li>
                </ul>
                <div class="flex flex-row flex-wrap mb-2 gap-3">
                    <button class="btn btn-sm bg-success hover:bg-successemphasis cursor-default">Warna Hijau Lebih dari 1 Bulan</button>
                    <button class="btn btn-sm bg-warning hover:bg-warningemphasis cursor-default">Warna Kuning Kurang Dari 1 Bulan</button>
                    <button class="btn btn-sm bg-error hover:bg-erroremphasis cursor-default">Warna Merah Sudah Expired</button>
                </div>
            </div>
        </x-slot:message>
    </x-badge>

    <div class="card mb-2">
        <div class="card-body p-2">
            <div class="flex flex-wrap gap-2">
                @can('barang.detail')
                    <a wire:navigate href="{{ route('data.barang.detail', $id) }}"
                       class="btn btn-sm bg-dark text-blue-400 hover:text-blue-500 dark:text-blue-600 hover:bg-gray-700 dark:bg-gray-200  dark:hover:bg-gray-400 ">
                        <span class="iconify solar--eye-bold text-lg"></span> Detail
                        Barang</a>
                @endcan
                @can('barang.edit')
                    <a wire:navigate href="{{ route('data.barang.edit', $id) }}"
                       class="btn btn-sm bg-dark text-yellow-500 hover:text-yellow-600 dark:text-orange-400 hover:dark:text-orange-300 hover:bg-gray-700 dark:bg-gray-200 dark:hover:bg-gray-400"><span
                              class="iconify tabler--edit-circle text-lg"></span> Edit Barang</a>
                @endcan
                @can('barang.detail-faktur')
                    <a wire:navigate href="{{ route('data.barang.detail.faktur', $id) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail
                        Faktur Barang</a>
                @endcan
                @can('barang.detail-stok')
                    <a wire:navigate href="{{ route('data.barang.detail.stok', $id) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail
                        Stok Barang</a>
                @endcan
                @can('barang.detail-barang-expired')
                    <a wire:navigate href="{{ route('data.barang.detail.expired', $id) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail
                        Expired Barang</a>
                @endcan
                @can('barang.detail-transaksi')
                    <a wire:navigate href="{{ route('data.barang.detail.transaksi', $id) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail Transaksi
                        Barang</a>
                @endcan
            </div>
        </div>
    </div>
    @can('barang.detail-barang-expired')
        <livewire:informasi.info-barang-expired.info-barang-expired-list :id_barang="$id" lazy>
        @endcan
</div>
