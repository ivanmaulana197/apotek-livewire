<x-slot:title>Data Role</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Role">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12">
            @can('role.list')
                @livewire('data-master.role.role-list')
            @endcan
        </div>
    </div>

</div>
