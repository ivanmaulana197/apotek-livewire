<div class="card">

    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List Member</h5>
        </div>

        <x-datatable nama_table="member">
             <div class="w-svw sm:w-56" wire:ignore>
                <x-form.select label="Profesi :" name="filterprofesi" mainSelect="profesi" wire:model.live='filters.id_profesi' :dataOption="$this->profesies" optionValue="id"
                    optionLabel="nama">
                </x-form.select>
            </div>
        </x-datatable>

        <x-table nama="member">

            <x-table.thead>
                <x-table.th label="No" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Member" name="nama" class="justify-center" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Alamat" name="alamat" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="No. Telp" name="no_hp" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Profesi" name="profesi.nama" />
                <x-table.th label='Aksi' class="flex justify-center" />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($members as $index => $member)
                    <x-table.tr wire:key='table-member-{{ $member->id }}'>
                        <x-table.td class="text-center">{{ $members->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-2">
                                {!! $this->highlight($member->nama) !!}
                            </h6>
                        </x-table.td>
                        <x-table.td>
                            {!! $this->highlight($member->alamat ?? '-') !!}
                        </x-table.td>
                        <x-table.td>
                            {!! $this->highlight($member->no_hp ?? '-') !!}
                        </x-table.td>
                        <x-table.td>
                            {!! $this->highlight($member->profesi->nama) !!}
                        </x-table.td>
                        <x-table.td class="text-center">
                            <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-member',{member:'{{ $member->id }}'})">
                                <i class="ti ti-edit-circle"></i>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Hapus" color="error"
                                                   @click="$wire.dispatch('alert-confirm', { id: '{{ $member->id }}', method: 'delete-member' });">
                                <i class="ti ti-trash-x-filled"></i>
                            </x-table.action-button>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr-empty colspan="7" />
                @endforelse
                <x-table.loading-spin />
            </x-table.tbody>

        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $members->onEachSide(1)->links(data: ['scrollTo' => '#table-member']) }}
        </div>

    </div>
</div>
