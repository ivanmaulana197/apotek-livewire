@php
    use App\Helpers\FormatHelper;
    use Carbon\Carbon;
@endphp

<div>
    <x-modal name="select_barang" size="4xl">
        <div class="card p-1">
            <div class="card-body gap-3">
                <div class="flex justify-between items-center border-b border-gray-200">
                    <h5 class="card-title">Select Barang</h5>
                    <x-modal.button-close @click="select_barangModal = false" />
                </div>

                <x-datatable nama_table="select_barang" placeholder_search="Search Kode / Nama Barang"></x-datatable>

                <x-table nama="select_barang" height="max-h-96">
                    <x-table.thead>
                        <x-table.th label="NO." />
                        <x-table.th label="KODE Barang" />
                        <x-table.th label="Nama Barang" />
                        <x-table.th label="Stok Real" />
                        <x-table.th label="H.Beli" />
                        <x-table.th label="H.Jual" />
                        <x-table.th label="H.Grosir" />
                        <x-table.th label="Aksi" />
                    </x-table.thead>
                    <x-table.tbody>
                        @forelse ($barangs as $index => $barang)
                            <x-table.tr class="hover:bg-gray-100 hover:dark:bg-blue-900 cursor-pointer" wire:click="selected('{{ $barang->id }}')">
                                <x-table.td class="text-center">{{ $barangs->firstItem() + $index }}</x-table.td>
                                <x-table.td>
                                    {!! $this->highlight($barang->kode) !!}
                                </x-table.td>
                                <x-table.td>
                                    {!! $this->highlight($barang->nama) !!}
                                </x-table.td>
                                <x-table.td>
                                    {{ FormatHelper::formatNumberToString($barang->stok) }}
                                </x-table.td>
                                <x-table.td>
                                    Rp. {{ FormatHelper::formatNumberToString($barang->harga_beli_default) }}
                                </x-table.td>
                                <x-table.td>
                                    Rp. {{ FormatHelper::formatNumberToString($barang->harga_jual_utama) }}
                                </x-table.td>
                                <x-table.td>
                                    @foreach ($barang->opsi_harga as $index => $opsi_harga)
                                        <div class="mb-2">
                                            <p class="text-xs">H.Jual: Rp.
                                                {{ FormatHelper::formatNumberToString($opsi_harga->harga) }}
                                            </p>
                                            <p class="text-xs">Min:
                                                {{ FormatHelper::formatNumberToString($opsi_harga->jumlah_min) }}/{{ $opsi_harga->satuan->nama }}
                                            </p>
                                        </div>
                                    @endforeach
                                </x-table.td>

                                <x-table.td class="text-center">
                                    <x-table.action-button wire:click="selected('{{ $barang->id }}')" tooltip="Select Barang" color=""
                                                           class="{{ $barang->id == $id_barang ? 'bg-secondary' : 'bg-success' }}">
                                        <span class="iconify fluent--select-all-on-16-filled"></span>
                                        <span class="text-sm">Select</span>
                                    </x-table.action-button>
                                    <x-table.action-button type="href" href="{{ route('data.barang.edit', $barang->id) }}" wire:navigate tooltip="Hapus"
                                                           color="warning">
                                        <span class="iconify tabler--edit-circle text-lg"></span>
                                    </x-table.action-button>
                                </x-table.td>

                            </x-table.tr>
                        @empty
                            <x-table.tr-empty colspan="11" />
                        @endforelse
                        <x-table.loading-spin />
                    </x-table.tbody>
                </x-table>

                <div class="overflow-y-hidden mt-3">
                    {{ $barangs->onEachSide(1)->links(data: ['scrollTo' => '#table-select_barang']) }}
                </div>
            </div>

        </div>
    </x-modal>
</div>
