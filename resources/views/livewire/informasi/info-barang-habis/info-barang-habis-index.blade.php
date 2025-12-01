<x-slot:title>Data Informasi Barang Habis</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Barang Habis" subtitle="Berikut adalah data barang yang habis / melebihi minimum stok yang ditentukan">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
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
                    <li>Keterangan warna, Pilih Tampil 100 data di Show table untuk mempermudah pengecekan</li>
                </ul>
                <div class="flex flex-row flex-wrap mb-2 gap-3">
                    <button class="btn btn-sm bg-warning hover:bg-warningemphasis cursor-default">Kurang Dari Stok Minimum</button>
                    <button class="btn btn-sm bg-error hover:bg-erroremphasis cursor-default">Stok Barang Sudah Habis</button>
                </div>
            </div>
        </x-slot:message>
    </x-badge>

    @can('info-barang-habis.list')
        <livewire:informasi.info-barang-habis.info-barang-habis-list lazy />
    @endcan

</div>
