<x-slot:title>Data Member</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Data Member">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12 md:col-span-6 lg:col-span-4">
            @can('member.create')
                @livewire('data-master.member.member-form')
            @endcan
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-8">
            @can('member.list')
                @livewire('data-master.member.member-list')
            @endcan
        </div>
    </div>

</div>
