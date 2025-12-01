<x-slot:title>Data Informasi Barang Expired</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Barang Expired" subtitle="Untuk data Expired Buang / Rusak Buang akan masuk dalam Info kerugian">
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
                    <li>Jika Barang sudah tidak ada / atau sudah laku. Info memilih HAPUS INFO</li>
                    <li>Keterangan warna, Pilih Tampil 100 data di Show table untuk mempermudah pengecekan</li>
                </ul>
                <div class="flex flex-row flex-wrap mb-2 gap-3">
                    <button class="btn btn-sm bg-success hover:bg-successemphasis cursor-default">Warna Hijau Kurang Dari 3 Bulan</button>
                    <button class="btn btn-sm bg-warning hover:bg-warningemphasis cursor-default">Warna Kuning Kurang Dari 1 Bulan</button>
                    <button class="btn btn-sm bg-error hover:bg-erroremphasis cursor-default">Warna Merah Sudah Expired</button>
                </div>
            </div>
        </x-slot:message>
    </x-badge>

    @can('info-barang-expired.list')
        <livewire:informasi.info-barang-expired.info-barang-expired-list lazy />
    @endcan

</div>
