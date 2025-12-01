<x-slot:title>Data Profesi</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Profesi">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-12 gap-3">
        @can('profesi.create')
            <div class="col-span-12 md:col-span-6 lg:col-span-4">
                @livewire('data-master.profesi.profesi-form')
            </div>
        @endcan
        @can('profesi.list')
            <div class="col-span-12 md:col-span-6 lg:col-span-8">
                @livewire('data-master.profesi.profesi-list')
            </div>
        @endcan
    </div>

</div>
