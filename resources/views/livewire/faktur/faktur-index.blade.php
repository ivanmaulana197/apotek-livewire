<x-slot:title>Faktur Barang</x-slot:title>
<div class="max-w-full">
    <x-breadcrumb name="Faktur Barang">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="card">
        <div class="card-body">
            <div class="flex items-center">
                <div class="mb-2">
                    <a class="btn btn-sm me-2" href="{{ route('faktur.tambah') }}" wire:navigate>
                        + Faktur Masuk
                    </a>
                    <button type="button" class="btn btn-sm bg-gray-400 hover:bg-gray-500 me-2">
                        Secondary
                    </button>
                </div>
            </div>

            @livewire('faktur.faktur-list')
        </div>
    </div>

</div>
