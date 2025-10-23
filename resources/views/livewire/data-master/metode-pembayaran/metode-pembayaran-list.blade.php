<div class="card">

    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List Metode Pembayaran</h5>
        </div>

        <x-datatable nama_table="metodePembayaran"></x-datatable>

        <x-table nama="metodePembayaran">

            <x-table.thead>
                <x-table.th label="No"  />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Metode Pembayaran" name="nama" class="justify-center" />
                <x-table.th label='Aksi' class="flex justify-center" />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($metodePembayarans as $index => $metodePembayaran)
                    <x-table.tr wire:key='table-metodePembayaran-{{ $metodePembayaran->id }}'>
                        <x-table.td class="text-center">{{ $metodePembayarans->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-2">
                                {!! $this->highlight($metodePembayaran->nama) !!}
                            </h6>
                        </x-table.td>

                        <x-table.td class="text-center">
                            <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-metodePembayaran',{metodePembayaran:'{{ $metodePembayaran->id }}'})">
                                <i class="ti ti-edit-circle"></i>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Hapus" color="error" @click="$wire.dispatch('alert-confirm', { id: '{{ $metodePembayaran->id }}', method: 'delete-metodePembayaran' });">
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
            {{ $metodePembayarans->onEachSide(1)->links(data: ['scrollTo' => '#table-metodePembayaran']) }}
        </div>


    </div>
</div>
