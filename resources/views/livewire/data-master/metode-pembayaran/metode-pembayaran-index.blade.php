<x-slot:title>Data Metode Pembayaran</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Metode Pembayaran">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-12 gap-3">
        @can('metode-pembayaran.create')
            <div class="col-span-12 md:col-span-6 lg:col-span-4">
                @livewire('data-master.metode-pembayaran.metode-pembayaran-form')
            </div>
        @endcan
        @can('metode-pembayaran.list')
            <div class="col-span-12 md:col-span-6 lg:col-span-8">
                @livewire('data-master.metode-pembayaran.metode-pembayaran-list')
            </div>
        @endcan
    </div>

</div>
