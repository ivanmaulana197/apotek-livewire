<div class="flex flex-col gap-y-3">
    <div class="flex justify-between items-center border-b border-gray-200">
        {{-- <h3 class="font-bold text-gray-800">Modal Title</h3> --}}
        <h5 class="card-title">List Detail Transaksi Barang </h5>

        <x-modal.button-close @click="transaksi_barang_detailModal = false" />

    </div>

    <x-table nama="barang-pesanan-kasir" height="h-96 max-h-96 w-auto">
        <x-table.thead>
            <x-table.th label="No" />
            <x-table.th label="Nama Barang" />
            <x-table.th label="Harga" />
            <x-table.th label="Jumlah" />
            <x-table.th label="Total" />
        </x-table.thead>
        <x-table.tbody>
            @forelse (optional($transaksi)->detail_transaksi ?? collect() as $index => $detail_transaksi)
                <x-table.tr class="{{ $detail_transaksi->promo_barang ? '' : ($loop->last ? 'h-full' : '') }}">
                    <x-table.td class="text-center">{{ $index + 1 }}</x-table.td>
                    <x-table.td class="font-bold">{{ $detail_transaksi->detail_barang->barang->nama }}</x-table.td>
                    <x-table.td class="min-w-24">
                        <div class="text-sm">
                            Rp. {{ $detail_transaksi->harga }}
                            / {{ $detail_transaksi->detail_barang->satuan->nama }}
                        </div>
                    </x-table.td>
                    <x-table.td class="min-w-44">

                        <h6 class="text-center">{{ $detail_transaksi->detail_barang->jumlah }} /
                            {{ $detail_transaksi->detail_barang->satuan->nama }}</h6>

                    </x-table.td>
                    <x-table.td class="text-center min-w-24">
                        Rp. {{ $detail_transaksi->total_harga }}
                    </x-table.td>

                </x-table.tr>
                @if ($detail_transaksi->promo_barang)
                    <x-table.tr-empty class="ps-4 text-start align-top" colspan="8">
                        <x-slot:content>
                            **Promo:** {{ $detail_transaksi->promo_barang->deskripsi }}
                        </x-slot:content>
                    </x-table.tr-empty>
                    @if ($loop->last)
                        <x-table.tr-empty class="h-full" colspan="8" content="" />
                    @endif
                @endif
            @empty
                <x-table.tr-empty colspan="8" />
            @endforelse
            <x-table.loading-spin />
        </x-table.tbody>
    </x-table>

    {{-- Success is as dangerous as failure. --}}
</div>
