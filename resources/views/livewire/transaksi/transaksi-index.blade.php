<x-slot:title>Transaksi</x-slot:title>

<div>
    <x-breadcrumb name="Transaksi">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <livewire:transaksi.transaksi-card-info :$shift_transaksi :$filter_date />

    <livewire:transaksi.transaksi-list :$shift_transaksi :$filter_date />

</div>
