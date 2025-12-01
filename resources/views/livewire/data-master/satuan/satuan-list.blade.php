<div class="card">

    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List Satuan</h5>
        </div>
        <x-datatable nama_table="satuan"></x-datatable>

        <x-table nama="satuan">
            <x-table.thead>
                <x-table.th label="No" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Satuan" name="nama" class="justify-center" />
                <x-table.th label='Aksi' />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($satuans as $index => $satuan)
                    <x-table.tr wire:key="table-satuan-{{ $satuan->id }}">
                        <x-table.td class="text-center">{{ $satuans->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-2">
                                {!! $this->highlight($satuan->nama) !!}
                            </h6>
                        </x-table.td>
                        <x-table.td class="text-center">
                            @can('satuan.edit')
                                <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-satuan',{satuan:'{{ $satuan->id }}'})">
                                    <span class="iconify tabler--edit-circle text-lg"></span>
                                </x-table.action-button>
                            @endcan
                            @can('satuan.delete')
                                <x-table.action-button tooltip="Hapus" color="error"
                                                       @click="$wire.dispatch('alert-confirm', { id: '{{ $satuan->id }}', method: 'delete-satuan' });">
                                    <span class="iconify tabler--trash-x-filled text-lg"></span>
                                </x-table.action-button>
                            @endcan
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr-empty colspan="3" />
                @endforelse
                <x-table.loading-spin />

            </x-table.tbody>
        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $satuans->onEachSide(1)->links(data: ['scrollTo' => '#table-satuan']) }}
        </div>

    </div>
</div>
