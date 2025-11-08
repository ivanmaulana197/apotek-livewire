@php
    use App\Helpers\FormatHelper;
@endphp

<div class="flex flex-col gap-y-3">
    <div class="flex justify-between items-center border-b border-gray-200">
        {{-- <h3 class="font-bold text-gray-800">Modal Title</h3> --}}
        <h5 class="card-title">List Pesanan Kasir</h5>

        <x-modal.button-close @click="diskon_transaksiModal = false" />

    </div>

    <x-table nama="diskon">
        <x-table.thead>
            <x-table.th label="No" />
            <x-table.th label="Nama Promo" />
            <x-table.th label="Minimum Pembelian" />
            <x-table.th label="Tipe Promo" />
            <x-table.th label="Promo" />
            <x-table.th label='Aksi' class="text-center" />
        </x-table.thead>
        <x-table.tbody>
            @forelse ($diskons as $index => $diskon)
                <x-table.tr wire:key="table-diskon-transaksi-{{ $diskon->id }}">
                    <x-table.td class="text-center">{{ $index + 1 }}</x-table.td>
                    <x-table.td>{{ $diskon->promo->nama }}</x-table.td>
                    <x-table.td>{{ FormatHelper::formatNumberToString($diskon->minimal_belanja) }}</x-table.td>
                    <x-table.td>{{ $diskon->tipe_transaksi }}</x-table.td>
                    <x-table.td>
                        @if ($diskon->tipe_transaksi == 'HARGA')
                            Potongan Harga Rp. <b>{{ FormatHelper::formatNumberToString($diskon->diskon_nominal) }}</b>
                        @elseif ($diskon->tipe_transaksi == 'PERSEN')
                            Diskon : <b>{{ $diskon->diskon_persen }}%</b> = Rp.
                            {{ FormatHelper::formatNumberToString(($sub_total * $diskon->diskon_persen) / 100) }}
                        @endif
                    </x-table.td>
                    <x-table.td class="flex justify-center items-center gap-1">
                        @if ($diskon->id == $id_promo_transaksi_selected)
                            <x-table.action-button tooltip="Show" color="secondary" wire:click="selectDiskon()">
                                <div class="flex items-center gap-x-1 text-sm">
                                    <span class="iconify tabler--eye-bolt"></span>
                                    <p>Selected</p>
                                </div>
                            </x-table.action-button>
                        @else
                            <x-table.action-button tooltip="Show" color="success" wire:click="selectDiskon('{{ $diskon->id }}')">
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
