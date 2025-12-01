<x-slot:title>Data Supplier</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Supplier">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12 md:col-span-6 lg:col-span-4">
            @canany(['supplier.create', 'supplier.edit'])
                @livewire('data-master.supplier.supplier-form')
            @endcanany
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-8">
            @can('supplier.list')
                @livewire('data-master.supplier.supplier-list')
            @endcan
        </div>
    </div>

</div>
