<div class="flex flex-col gap-y-3">
    <div class="flex justify-between items-center border-b border-gray-200">
        {{-- <h3 class="font-bold text-gray-800">Modal Title</h3> --}}
        <h5 class="card-title">List Pesanan Kasir</h5>

        <x-modal.button-close @click="promo_transaksiModal = false" />

    </div>

    <x-table nama="promo_transaksi">
        <x-table.thead>
            <x-table.th label="No" />
            <x-table.th label="Nama Promo" />
            <x-table.th label="Minimum Pembelian" />
            <x-table.th label="Tipe Promo" />
            <x-table.th label="Promo" />
            <x-table.th label='Aksi' class="text-center" />
        </x-table.thead>
        <x-table.tbody>
            @forelse ($promo_transaksis as $index => $promo_transaksi)
                <x-table.tr wire:key="table-promo-transaksi-{{ $promo_transaksi->id }}">
                    <x-table.td class="text-center">{{ $index + 1 }}</x-table.td>
                    <x-table.td>{{ $promo_transaksi->promo->nama }}</x-table.td>
                    <x-table.td x-text="formatNumberToString({{ $promo_transaksi->minimal_belanja }})"></x-table.td>
                    <x-table.td>{{ $promo_transaksi->tipe_transaksi }}</x-table.td>
                    <x-table.td>
                        @if ($promo_transaksi->tipe_transaksi == 'HARGA')
                            Potongan Harga Rp. <b x-text="formatNumberToString({{ $promo_transaksi->diskon_nominal }})"></b>
                        @elseif ($promo_transaksi->tipe_transaksi == 'PERSEN')
                            Potongan : <b>{{ $promo_transaksi->diskon_persen }}%</b> = Rp.
                            <span x-text="formatNumberToString({{ ($total_belanja * $promo_transaksi->diskon_persen) / 100 }})"></span>
                        @endif
                    </x-table.td>
                    <x-table.td class="flex justify-center items-center gap-1">
                        @if ($promo_transaksi->id == $id_promo_transaksi_selected)
                            <x-table.action-button tooltip="Show" color="secondary" wire:click="selectPromoTransaksi()">
                                <div class="flex items-center gap-x-1 text-sm">
                                    <span class="iconify tabler--eye-bolt"></span>
                                    <p>Selected</p>
                                </div>
                            </x-table.action-button>
                        @else
                            <x-table.action-button tooltip="Show" color="success" wire:click="selectPromoTransaksi('{{ $promo_transaksi->id }}')">
                                <div class="flex items-center gap-x-1 text-sm">
                                    <span class="iconify tabler--eye-bolt"></span>
                                    <p>Select</p>
                                </div>
                            </x-table.action-button>
                        @endif

                    </x-table.td>

                </x-table.tr>
            @empty
                <x-table.tr-empty colspan="8" />
            @endforelse
            <x-table.loading-spin />
        </x-table.tbody>
    </x-table>

    {{-- Success is as dangerous as failure. --}}
</div>
