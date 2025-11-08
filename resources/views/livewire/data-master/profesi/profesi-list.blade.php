<div class="card">

    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List Profesi</h5>
        </div>

        <x-datatable nama_table="profesi"></x-datatable>

        <x-table nama="profesi">

            <x-table.thead>
                <x-table.th label="No" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Profesi" name="nama" class="justify-center" />
                <x-table.th label='Aksi' class="flex justify-center" />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($profesies as $index => $profesi)
                    <x-table.tr wire:key='table-profesi-{{ $profesi->id }}'>
                        <x-table.td class="text-center">{{ $profesies->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-2">
                                {!! $this->highlight($profesi->nama) !!}
                            </h6>
                        </x-table.td>
                        <x-table.td class="text-center">
                            <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-profesi',{profesi:'{{ $profesi->id }}'})">
                                <span class="iconify tabler--edit-circle text-lg"></span>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Hapus" color="error"
                                                   @click="$wire.dispatch('alert-confirm', { id: '{{ $profesi->id }}', method: 'delete-profesi' });">
                                <span class="iconify tabler--trash-x-filled text-lg"></span>
                            </x-table.action-button>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr-empty colspan="3" />
                @endforelse
                <x-table.loading-spin />

            </x-table.tbody>

        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $profesies->onEachSide(1)->links(data: ['scrollTo' => '#table-profesi']) }}
        </div>

    </div>
</div>
