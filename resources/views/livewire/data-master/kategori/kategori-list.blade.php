<div class="card">

    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List Kategori</h5>
        </div>

        <x-datatable nama_table="kategori"></x-datatable>

        <x-table nama="kategori">

            <x-table.thead>
                <x-table.th label="No" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Kategori" name="nama" class="justify-center" />
                <x-table.th label='Aksi' class="flex justify-center" />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($kategories as $index => $kategori)
                    <x-table.tr wire:key='table-kategori-{{ $kategori->id }}'>
                        <x-table.td class="text-center">{{ $kategories->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-2">
                                {!! $this->highlight($kategori->nama) !!}
                            </h6>
                        </x-table.td>
                        <x-table.td class="text-center">
                            @can('kategori.edit')
                                <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-kategori',{kategori:'{{ $kategori->id }}'})">
                                    <span class="iconify tabler--edit-circle text-lg"></span>
                                </x-table.action-button>
                            @endcan
                            @can('kategori.delete')
                                <x-table.action-button tooltip="Hapus" color="error"
                                                       @click="$wire.dispatch('alert-confirm', { id: '{{ $kategori->id }}', method: 'delete-kategori' });">
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
            {{ $kategories->onEachSide(1)->links(data: ['scrollTo' => '#table-kategori']) }}
        </div>

    </div>
</div>
