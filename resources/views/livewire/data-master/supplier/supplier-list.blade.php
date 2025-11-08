<div class="card">

    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List Supplier</h5>
        </div>

        <x-datatable nama_table="supplier"></x-datatable>

        <x-table nama="supplier">

            <x-table.thead>
                <x-table.th label="No" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Supplier" name="nama" class="justify-center" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Asal" name="asal" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Alamat" name="alamat" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="No. Telp" name="no_hp" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Include Pajak" name="is_pajak" />
                <x-table.th label='Aksi' class="flex justify-center" />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($suppliers as $index => $supplier)
                    <x-table.tr wire:key='table-supplier-{{ $supplier->id }}'>
                        <x-table.td class="text-center">{{ $suppliers->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-2">
                                {!! $this->highlight($supplier->nama) !!}
                            </h6>
                        </x-table.td>
                        <x-table.td>
                            {!! $this->highlight($supplier->asal) !!}
                        </x-table.td>
                        <x-table.td>
                            {!! $this->highlight($supplier->alamat ?? '-') !!}
                        </x-table.td>
                        <x-table.td>
                            {!! $this->highlight($supplier->no_hp ?? '-') !!}
                        </x-table.td>
                        <x-table.td>
                            {{ $supplier->is_pajak ? 'Ya' : 'Tidak' }}
                        </x-table.td>

                        <x-table.td class="text-center">
                            <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-supplier',{supplier:'{{ $supplier->id }}'})">
                                <span class="iconify tabler--edit-circle text-lg"></span>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Hapus" color="error"
                                                   @click="$wire.dispatch('alert-confirm', { id: '{{ $supplier->id }}', method: 'delete-supplier' });">
                                <span class="iconify tabler--trash-x-filled text-lg"></span>
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
            {{ $suppliers->onEachSide(1)->links(data: ['scrollTo' => '#table-supplier']) }}
        </div>

    </div>
</div>
