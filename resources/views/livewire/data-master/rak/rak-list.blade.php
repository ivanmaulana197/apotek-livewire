<div class="card">
    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List Rak</h5>
        </div>
        <x-datatable nama_table="rak"></x-datatable>

        <x-table nama="rak">
            <x-table.thead>
                <x-table.th class="min-w-[20%] w-[20%]" label="No" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Rak" name="nama" class="justify-center min-w-[40%] w-[40%]" />
                <x-table.th label='Aksi' class="min-w-[40%] w-[40%]" />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($raks as $index => $rak)
                    <x-table.tr wire:key="table-rak-{{ $rak->id }}">
                        <x-table.td class="text-center">{{ $raks->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            {!! $this->highlight($rak->nama) !!}
                        </x-table.td>
                        <x-table.td class="flex justify-center items-center gap-1">
                            {{-- <div class="flex justify-center"> --}}
                            <x-table.action-button tooltip="Rak Barang" color="info" class="text-sm" type="href" href="/" wire:navigate>
                                Rak Barang
                            </x-table.action-button>
                            <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-rak',{rak:'{{ $rak->id }}'})">
                                <i class="ti ti-edit-circle"></i>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Hapus" color="error"
                                                   @click="$wire.dispatch('alert-confirm', { id: '{{ $rak->id }}', method: 'delete-rak' });">
                                <i class="ti ti-trash-x-filled"></i>
                            </x-table.action-button>
                            {{-- </div> --}}
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr-empty colspan="3" />
                @endforelse
                <x-table.loading-spin />

            </x-table.tbody>
        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $raks->onEachSide(1)->links(data: ['scrollTo' => '#table-rak']) }}
        </div>

    </div>
</div>
