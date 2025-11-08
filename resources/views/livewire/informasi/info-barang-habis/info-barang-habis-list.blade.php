<div class="card">
    <div class="card-body">

        <x-datatable nama_table="info_barang_habis" :$perPage></x-datatable>

        <x-table nama="info_barang_habis" height="auto">
            <x-table.thead>
                <x-table.th label="NO." />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Barang" name="nama" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Stok" name="stok" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Minimal Stok" name="stok_min" />
                <x-table.th label="Aksi" />
            </x-table.thead>
            <x-table.tbody>
                @forelse ($info_barang_habis as $index => $barang)
                    <x-table.tr wire:key="tabel-barang-habis-{{ $barang->id }}" class="{{ $barang->warna }}">
                        <x-table.td class="text-center">{{ $info_barang_habis->firstItem() + $index }}</x-table.td>
                        <x-table.td class="text-left">{{ $barang->nama }}</x-table.td>
                        <x-table.td class="text-center">@nominal($barang->stok) / {{ $barang->satuan_utama->nama }}</x-table.td>
                        <x-table.td class="text-center">@nominal($barang->stok_min) / {{ $barang->satuan_utama->nama }}</x-table.td>
                        <x-table.td class="text-center">
                            <x-table.action-button tooltip="Tambah Stok" color="info" type="href" href="{{ route('faktur.tambah') }}" wire:navigate>
                                <i class="ti ti-eye"></i> <span class="text-sm">Tambah Stok</span>
                            </x-table.action-button>
                            {{-- <x-table.action-button tooltip="Hapus Info" color="error" wire:click="hapusInfo('{{ $barang->id }}')">
                                <i class="ti ti-trash"></i> <span class="text-sm">Hapus Info</span>
                            </x-table.action-button> --}}
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr-empty colspan="11" />
                @endforelse
                <x-table.loading-spin />
            </x-table.tbody>
        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $info_barang_habis->onEachSide(1)->links(data: ['scrollTo' => '#table-info_barang_habis']) }}
        </div>
    </div>
</div>
