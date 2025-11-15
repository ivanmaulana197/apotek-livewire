<x-slot:title>Data Barang</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Barang">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-1 gap-3">

        <div class="card">
            <div class="card-body">
                <div class="flex flex-wrap gap-x-3 gap-y-2">
                    <a href="{{ route('data.barang.tambah') }}" wire:navigate class="btn btn-primary w-fit">+ Tambah Barang</a>
                    <button class="btn bg-success hover:bg-successemphasis w-fit" wire:click='pembulatanAll'>
                        <span class="iconify solar--pen-new-square-outline text-lg"></span> Pembulatan All
                    </button>
                </div>
            </div>
        </div>

        <livewire:barang.barang-list />

    </div>

</div>
