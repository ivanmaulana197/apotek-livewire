<x-slot:title>Data Rak</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Rak">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12 md:col-span-6 lg:col-span-4">
            @canany(['rak.create', 'rak.edit'])
                @livewire('data-master.rak.rak-form')
            @endcanany
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-8">
            @can('rak.list')
                @livewire('data-master.rak.rak-list')
            @endcan
        </div>
    </div>

</div>
