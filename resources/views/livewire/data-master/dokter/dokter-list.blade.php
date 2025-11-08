<div class="card">

    <div class="card-body">
        <div class="items-center justify-between mb-2 sm:flex">
            <h5 class="card-title text-md">List Dokter</h5>
        </div>

        <x-datatable nama_table="dokter" :$perPage></x-datatable>

        <x-table nama="dokter">

            <x-table.thead>
                <x-table.th label="No" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Dokter" name="nama" class="justify-center" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Alamat" name="alamat" class="justify-center" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="No. HP" name="no_hp" class="justify-center" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Spesialis" name="spesialis" class="justify-center" />
                <x-table.th label='Aksi' class="flex justify-center" />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($dokters as $index => $dokter)
                    <x-table.tr wire:key='table-dokter-{{ $dokter->id }}'>
                        <x-table.td class="text-center">{{ $dokters->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-2">
                                {!! $this->highlight($dokter->nama) !!}
                            </h6>
                        </x-table.td>
                        <x-table.td>
                            {!! $this->highlight($dokter->alamat ?? '-') !!}
                        </x-table.td>
                        <x-table.td>
                            {!! $this->highlight($dokter->no_hp ?? '-') !!}
                        </x-table.td>
                        <x-table.td>
                            {!! $this->highlight($dokter->spesialis ?? '-') !!}
                        </x-table.td>
                        <x-table.td class="text-center">
                            <x-table.action-button tooltip="Detail" color="info" type="href" wire:navigate
                                                   href="{{ route('data-master.dokter.detail', $dokter->id) }}">
                                <span class="iconify tabler--info-circle text-lg"></span>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-dokter',{dokter:'{{ $dokter->id }}'})">
                                <span class="iconify tabler--edit-circle text-lg"></span>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Hapus" color="error"
                                                   @click="$wire.dispatch('alert-confirm', { id: '{{ $dokter->id }}', method: 'delete-dokter' });">
                                <span class="iconify tabler--trash-x-filled text-lg"></span>
                            </x-table.action-button>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr-empty colspan="6" />
                @endforelse
                <x-table.loading-spin />

            </x-table.tbody>

        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $dokters->onEachSide(1)->links(data: ['scrollTo' => '#table-dokter']) }}
        </div>

    </div>
</div>
