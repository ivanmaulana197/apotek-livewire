<div class="card h-fit">
    <div class="card-body">
        <h5 class="card-title flex justify-between items-center">
            <span>List Pasien</span>
            <x-datatable.per-page nama_table="list_pasien" :perPage="$perPage" />
        </h5>

        <x-table nama="list_pasien">
            <x-table.thead>
                <x-table.th label="No" />
                <x-table.th label="Nama Pasien" />
                <x-table.th label="Alamat" />
                <x-table.th label="No HP" />
                @if ($id_dokter)
                    <x-table.th label="Total Belanja" />
                @endif
            </x-table.thead>
            <x-table.tbody>
                @forelse ($pasiens as $index => $item)
                    <x-table.tr>
                        <x-table.td class="text-center">{{ $pasiens->firstItem() + $index }}</x-table.td>
                        <x-table.td>{{ $item->nama_pasien }}</x-table.td>
                        <x-table.td>{{ $item->alamat_pasien }}</x-table.td>
                        <x-table.td>{{ $item->no_hp_pasien }}</x-table.td>
                        @if ($id_dokter)
                            <x-table.td>Rp. @nominal($item->total_bayar) </x-table.td>
                        @endif
                    </x-table.tr>
                @empty
                    <x-table.tr-empty colspan="{{ $id_dokter ? 5 : 4 }}" />
                @endforelse
                <x-table.loading-spin />
            </x-table.tbody>
        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $pasiens->onEachSide(1)->links(data: ['scrollTo' => '#table-barang-terlaris']) }}
        </div>

    </div>
</div>
