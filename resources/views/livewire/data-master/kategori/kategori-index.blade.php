<x-slot:title>Data Kategori</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Kategori">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12 md:col-span-6 lg:col-span-4">
            @canany(['kategori.create', 'kategori.edit'])
                @livewire('data-master.kategori.kategori-form')
            @endcanany
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-8">
            @can('kategori.list')
                @livewire('data-master.kategori.kategori-list')
            @endcan
        </div>
    </div>

</div>
