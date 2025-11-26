@php
    use App\Helpers\FormatHelper;
@endphp

<div class="flex flex-col gap-y-3">
    <div class="flex justify-between items-center border-b border-gray-200">
        {{-- <h3 class="font-bold text-gray-800">Modal Title</h3> --}}
        <h5 class="card-title">List Promo Barang</h5>

        <x-modal.button-close @click="promo_barangModal = false" />

    </div>

    <x-table nama="promo">
        <x-table.thead>
            <x-table.th label="No" />
            <x-table.th label="Nama Promo" />
            <x-table.th label="Qty Pembelian" />
            <x-table.th label="Tipe Promo" />
            <x-table.th label="Promo" />
            <x-table.th label='Aksi' class="text-center" />
        </x-table.thead>
        <x-table.tbody>
            @forelse ($promos as $index => $promo)
                <x-table.tr wire:key="table-promo-{{ $promo->id }}">
                    <x-table.td class="text-center">{{ $index + 1 }}</x-table.td>
                    <x-table.td>{{ $promo->promo->nama }}</x-table.td>
                    <x-table.td>{{ $promo->tipe_qty . ' : ' . $promo->syarat_qty . ' / ' . $promo->satuan->nama }}</x-table.td>
                    <x-table.td>{{ $promo->tipe }}</x-table.td>
                    <x-table.td>
                        @if ($promo->tipe == 'BARANG BONUS')
                            Bonus Barang : <b>{{ $promo->bonus_qty }} / {{ $promo->satuan_bonus->nama }}</b>
                        @elseif ($promo->tipe == 'HARGA')
                            Potongan Harga Rp. <b>{{ FormatHelper::formatNumberToString($promo->diskon_nominal) }}</b>
                        @elseif ($promo->tipe == 'PERSEN')
                            Diskon : <b>{{ $promo->diskon_persen }}%</b>
                        @endif
                        <br>
                        {{ $promo->tipe_qty == 'KELIPATAN' ? 'Berlaku KELIPATAN' : '' }}
                    </x-table.td>
                    <x-table.td class="flex justify-center items-center gap-1">
                        @if ($promo->id == $id_promo_barang_selected)
                            <x-table.action-button tooltip="Show" color="secondary" wire:click="selectPromo(null, '{{ $promo->id_barang }}')">
                                <div class="flex items-center gap-x-1 text-sm">
                                    <span class="iconify tabler--eye-bolt"></span>
                                    <p>Selected</p>
                                </div>
                            </x-table.action-button>
                        @else
                            <x-table.action-button tooltip="Show" color="success" wire:click="selectPromo('{{ $promo->id }}', '{{ $promo->id_barang }}')">
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

    <div class="overflow-y-hidden mt-3">

    </div>
</div>
