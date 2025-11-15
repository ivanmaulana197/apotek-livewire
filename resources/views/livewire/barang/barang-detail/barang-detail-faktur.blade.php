<x-slot:title>Detail Barang Faktur</x-slot:title>

<div>
    <x-breadcrumb name="Detail Data Barang ">
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
        $id_barang = $barang['id'];
    @endphp
    <div class="card mb-2">
        <div class="card-body p-2">
            <div class="flex flex-wrap gap-2">
                <a wire:navigate href="{{ route('data.barang.detail', $id_barang) }}"
                   class="btn btn-sm bg-dark text-blue-400 hover:text-blue-500 dark:text-blue-600 hover:bg-gray-700 dark:bg-gray-200  dark:hover:bg-gray-400 ">
                    <span class="iconify solar--eye-bold text-lg"></span> Detail
                    Barang</a>
                <a wire:navigate href="{{ route('data.barang.edit', $id_barang) }}"
                   class="btn btn-sm bg-dark text-yellow-500 hover:text-yellow-600 dark:text-orange-400 hover:dark:text-orange-300 hover:bg-gray-700 dark:bg-gray-200 dark:hover:bg-gray-400"><span
                          class="iconify tabler--edit-circle text-lg"></span> Edit Barang</a>
                <a wire:navigate href="{{ route('data.barang.detail.faktur', $id_barang) }}"
                   class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail
                    Faktur Barang</a>
                <a wire:navigate href="{{ route('data.barang.detail.stok', $id_barang) }}"
                   class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail
                    Stok Barang</a>
                <a wire:navigate href="{{ route('data.barang.detail.expired', $id_barang) }}"
                   class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail
                    Expired Barang</a>
                <a wire:navigate href="{{ route('data.barang.detail.transaksi', $id_barang) }}"
                   class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail Transaksi
                    Barang</a>
            </div>
        </div>
    </div>

    <div class="card mb-2">
        <div class="card-body">
            <h5 class="card-title">Faktur untuk Barang: {{ $barang['nama'] }}</h5>
            <livewire:faktur.faktur-list :id_barang="$barang['id']">
        </div>
    </div>
</div>
