<x-slot:title>Data Promo</x-slot:title>

<div>
    <x-breadcrumb name="Data Promo">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-1 gap-3">

        {{-- <x-modal.button name="promo" label="Tambah Promo"  /> --}}
        <div class="p-2">
            <a href="{{ route('promo.tambah') }}" class="btn" wire:navigate>+ Tambah Promo</a>
        </div>
        {{-- @livewire('promo.promo-form') --}}
        <livewire:promo.promo-list />
    </div>

    {{-- <x-modal name="promo" size="6xl">
        @livewire('promo.promo-form-modal',['isModal' => true])
    </x-modal> --}}
</div>
