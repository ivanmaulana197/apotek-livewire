<x-slot:title>Data Promo</x-slot:title>

<div>
    <x-breadcrumb name="Data Promo">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-1 gap-3">

        @can('promo.create')
            <div class="p-2">
                <a href="{{ route('promo.tambah') }}" class="btn" wire:navigate>+ Tambah Promo</a>
            </div>
        @endcan

        @can('promo.list')
            <livewire:promo.promo-list />
        @endcan
    </div>

</div>
