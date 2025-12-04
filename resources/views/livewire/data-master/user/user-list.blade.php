<div class="card">

    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List User</h5>
        </div>
        <x-datatable nama_table="user"></x-datatable>

        <x-table nama="user">
            <x-table.thead>
                <x-table.th label="No" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama User" name="name" class="justify-center" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Email" name="email" class="justify-center" />
                <x-table.th label="Role" />
                <x-table.th label='Aksi' class="flex justify-center" />
            </x-table.thead>
            <x-table.tbody>
                @forelse ($users as $index => $user)
                    <x-table.tr wire:loading.remove>
                        <x-table.td class="text-center">{{ $users->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-2">
                                {{ $user->name }}
                            </h6>
                        </x-table.td>
                        <x-table.td>
                            {{ $user->email }}
                        </x-table.td>
                        <x-table.td>
                            {{ $user->formatRoleList() ?? '-' }}
                        </x-table.td>

                        <x-table.td class="flex justify-center">
                            @can('user.edit')
                                @if (!$user->hasRole('Super Admin') || auth()->user()->hasRole('Super Admin'))
                                    <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-user',{user:'{{ $user->id }}'})">
                                        <span class="iconify tabler--edit-circle text-lg"></span>
                                    </x-table.action-button>
                                @endif
                            @endcan
                            @can('user.delete')
                                @if (!$user->hasRole('Super Admin'))
                                    <x-table.action-button tooltip="Hapus" color="error"
                                                           @click="$wire.dispatch('alert-confirm', { id: '{{ $user->id }}', method: 'delete-user' });">
                                        <span class="iconify tabler--trash-x-filled text-lg"></span>
                                    </x-table.action-button>
                                @endif
                            @endcan
                        </x-table.td>

                    </x-table.tr>
                @empty
                    <tr wire:loading.remove>
                        <td colspan="6" class="p-2 text-sm font-semibold text-center text-dark dark:text-white">
                            Tidak Ada Data
                        </td>
                    </tr>
                @endforelse
                <x-table.loading colspan="6" />
            </x-table.tbody>
        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $users->onEachSide(1)->links(data: ['scrollTo' => '#table-user']) }}
        </div>

    </div>
</div>
