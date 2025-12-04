<div class="card">
    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List Role</h5>
            <a href="{{ route('data-master.role.tambah') }}" wire:navigate class="btn btn-sm ">Tambah Role</a>
        </div>
        <x-datatable nama_table="role"></x-datatable>

        <x-table nama="role">
            <x-table.thead>
                <x-table.th class="min-w-[20%] w-[20%]" label="No" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Rak" name="nama" class="justify-center min-w-[40%] w-[40%]" />
                <x-table.th label='Aksi' class="min-w-[40%] w-[40%]" />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($roles as $index => $role)
                    <x-table.tr wire:key="table-role-{{ $role->id }}">
                        <x-table.td class="text-center">{{ $roles->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            {!! $this->highlight($role->name) !!}
                        </x-table.td>
                        <x-table.td class="flex justify-center items-center gap-1">

                            {{-- <x-table.action-button tooltip="Rak Barang" color="info" class="text-sm" type="href" href="/" wire:navigate>
                                <span class="iconify tabler--eye text-lg"></span>
                            </x-table.action-button> --}}
                            @can('role.edit')
                                @if ($role->name != 'Super Admin')
                                    <x-table.action-button tooltip="Edit" color="warning" type="href" href="{{ route('data-master.role.edit', $role->id) }}"
                                                           wire:navigate>
                                        <span class="iconify tabler--edit-circle text-lg"></span>
                                    </x-table.action-button>
                                @endif
                            @endcan
                            @can('role.delete')
                                @if ($role->name != 'Super Admin')
                                    <x-table.action-button tooltip="Hapus" color="error"
                                                           @click="$wire.dispatch('alert-confirm', { id: '{{ $role->id }}', method: 'delete-role' });">
                                        <span class="iconify tabler--trash-x-filled text-lg"></span>
                                    </x-table.action-button>
                                @endif
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
            {{ $roles->onEachSide(1)->links(data: ['scrollTo' => '#table-role']) }}
        </div>

    </div>
</div>
