<div class="card h-fit">
    @can('dokter.detail.list-data-barang-terjual')
        <div class="card-body">
            <h5 class="card-title flex justify-between items-center">
                <span>Barang Terlaris</span>
                {{-- <x-datatable.per-page nama_table="barang_terlaris" :perPage="$perPage" /> --}}
            </h5>

            <x-datatable nama_table="barang_terlaris" :$perPage></x-datatable>

            <x-table nama="barang_terlaris">
                <x-table.thead>
                    <x-table.th label="No" />
                    <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Barang" name="b.nama" />
                    <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Total Terjual" name="total_terjual" />
                </x-table.thead>
                <x-table.tbody>
                    @forelse ($barang_terlaris as $index => $item)
                        <x-table.tr>
                            <x-table.td class="text-center">{{ $index + 1 }}</x-table.td>
                            <x-table.td>{{ $item->nama }}</x-table.td>
                            <x-table.td class="text-center">@decimal($item->total_terjual) / {{ $item->satuan_utama }}</x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr-empty colspan="3" />
                    @endforelse
                    <x-table.loading-spin />
                </x-table.tbody>
            </x-table>

            <div class="overflow-y-hidden mt-3">
                {{ $barang_terlaris->onEachSide(1)->links(data: ['scrollTo' => '#table-barang-terlaris']) }}
            </div>

        </div>
    @endcan
</div>
