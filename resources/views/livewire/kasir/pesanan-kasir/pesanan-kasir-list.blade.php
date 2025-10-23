@php
    use App\Helpers\FormatHelper;
    use App\Helpers\PembulatanHelper;
    use App\Helpers\GenerateHelper;
    use App\Models\Setting;

    PembulatanHelper::init();
@endphp

<div>
    <div x-data class="ms-2 col-span-2 flex items-center">
        <button type="button" @click="$dispatch('toggle-modal-pesanan_kasir', { pesanan_kasirModal: true })" class="btn btn-sm btn-primary w-full h-full ">
            Pesanan
        </button>
    </div>
    <x-modal name="pesanan_kasir" size="4xl">
        <div class="card">
            <div class="card-body">
                <div class="flex justify-between items-center border-b border-gray-200">
                    {{-- <h3 class="font-bold text-gray-800">Modal Title</h3> --}}
                    <h5 class="card-title">List Pesanan Kasir</h5>

                    <x-modal.button-close @click="pesanan_kasirModal = false" />

                </div>

                <x-table nama="pesanan_kasir">
                    <x-table.thead>
                        <x-table.th label="No" />
                        <x-table.th label="No Pesanan" />
                        <x-table.th label="Akun" />
                        <x-table.th label="Shift" />
                        <x-table.th label="Mode Kasir" />
                        <x-table.th label="Total Harga" />
                        <x-table.th label="Update Terakhir" />
                        <x-table.th label='Aksi' class="text-center" />
                    </x-table.thead>
                    <x-table.tbody>
                        @forelse ($pesanan_kasirs as $index => $pesanan_kasir)
                            <x-table.tr wire:key="table-pesanan_kasir-{{ $pesanan_kasir->id }}">
                                <x-table.td class="text-center">{{ $pesanan_kasirs->firstItem() + $index }}</x-table.td>
                                <x-table.td>{{ $pesanan_kasir->no_pesanan_kasir }}</x-table.td>
                                <x-table.td>{{ $pesanan_kasir->user->name }}</x-table.td>
                                <x-table.td>{{ $pesanan_kasir->mode_shift }}</x-table.td>
                                <x-table.td>{{ $pesanan_kasir->mode_kasir }}</x-table.td>
                                <x-table.td>Rp. {{ PembulatanHelper::pembulatanTotalHarga($pesanan_kasir->total_harga, $pesanan_kasir->mode_kasir,true) }}</x-table.td>
                                <x-table.td>{{ FormatHelper::formatDate($pesanan_kasir->updated_at, true) }}</x-table.td>

                                <x-table.td class="flex justify-center items-center gap-1">
                                        @if ($pesanan_kasir->id == $pesanan_kasir_selected)
                                            <x-table.action-button tooltip="Show" color="secondary" class="pointer-events-none">
                                                <div class="flex items-center gap-x-1 text-sm">
                                                    <i class="ti ti-eye-bolt"></i>
                                                    <p>Pilih</p>
                                                </div>
                                            </x-table.action-button>
                                        @else
                                            <x-table.action-button tooltip="Show" color="success" type="href" wire:navigate
                                            href="{{ route('kasir.show', $pesanan_kasir->id) }}">
                                                <div class="flex items-center gap-x-1 text-sm">
                                                    <i class="ti ti-eye-bolt"></i>
                                                    <p>Pilih</p>
                                                </div>
                                            </x-table.action-button>
                                        @endif
                                        <x-table.action-button tooltip="Hapus" color="error" @click="$wire.dispatch('alert-confirm', {id: '{{ $pesanan_kasir->id }}', method: 'delete-pesanan-kasir'});">
                                            <i class="ti ti-trash-x-filled"></i>
                                        </x-table.action-button>

                                </x-table.td>

                            </x-table.tr>
                        @empty
                            <x-table.tr-empty colspan="8" />
                        @endforelse
                        <x-table.loading-spin />
                    </x-table.tbody>
                </x-table>

                <div class="overflow-y-hidden mt-3">
                    {{ $pesanan_kasirs->onEachSide(1)->links(data: ['scrollTo' => '#table-pesanan_kasir']) }}
                </div>
            </div>
            {{-- Success is as dangerous as failure. --}}
        </div>

    </x-modal>
</div>
