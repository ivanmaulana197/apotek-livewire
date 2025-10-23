<x-slot:title>Data Satuan</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Satuan">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12 md:col-span-6 lg:col-span-4">
            @livewire('data-master.satuan.satuan-form')
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-8">
            @livewire('data-master.satuan.satuan-list')
        </div>
    </div>


</div>


