<x-slot:title>Data User</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data User">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12 md:col-span-6 lg:col-span-4">
            @canany(['user.create', 'user.edit'])
                @livewire('data-master.user.user-form')
            @endcanany
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-8">
            @can('user.list')
                @livewire('data-master.user.user-list')
            @endcan
        </div>
    </div>

</div>
