<x-slot:title>Transaksi</x-slot:title>

<div>
    <x-breadcrumb name="Transaksi">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>
    @canany(['transaksi.info-total-transaksi-by-metode-pembayaran', 'transaksi.info-total-transaksi-umum', 'transaksi.info-total-transaksi-resep',
        'transaksi.info-total-jumlah-transaksi'])
        <livewire:transaksi.transaksi-card-info :$shift_transaksi :$filter_date />
    @endcanany

    @canany(['transaksi.list-all', 'transaksi.list-own'])
        <livewire:transaksi.transaksi-list :$shift_transaksi :$filter_date />
    @endcanany
</div>
