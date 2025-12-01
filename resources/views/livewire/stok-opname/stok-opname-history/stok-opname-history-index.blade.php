<x-slot:title>History Stok Opname</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="History Stok Opname">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
            <x-breadcrumb.before name="Stok Opname" href="{{ route('stok-opname') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-1 gap-y-3">
        <div class="card">
            <div class="card-body">
                {{-- <x-form.input type="month" label="Bulan" wire:model="bulan"></x-form.input> --}}
                <div class="flex flex-row flex-wrap items-center gap-3">
                    <div wire:ignore class="flex flex-row flex-wrap items-center">
                        <label class="me-2">Show :</label>
                        <x-datatable.per-page nama_table="stok_opname_history" :perPage="$perPage" />
                    </div>
                    <div class="flex flex-row flex-wrap items-center" wire:ignore>
                        <label class="me-2">Bulan :</label>
                        <x-form.select wire:model="bulan" name="bulan" :direction=false class="min-w-36">
                            <x-slot:customOption>
                                @foreach (['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $nama)
                                    <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </x-slot:customOption>
                        </x-form.select>
                    </div>
                    <div class=" flex flex-row flex-wrap items-center">
                        <label class="me-2">Tahun :</label>
                        <x-form.select wire:model="tahun" name="tahun" :direction=false required class="min-w-32">
                            <x-slot:customOption>
                                @for ($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </x-slot:customOption>
                        </x-form.select>
                    </div>
                    <div class="">
                        <button wire:click='triggerSearch' class="btn btn-sm">Search</button>
                    </div>
                </div>

                <x-table nama="stok_opname_history">
                    <x-table.thead>
                        <x-table.th label="No" />
                        <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Tangga Stok Opname" name="created_at" />
                        <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Jumlah Barang" name="jumlah_barang" />
                        <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Kerugian" name="kerugian" />
                        <x-table.th label='Aksi' class="flex justify-center" />
                    </x-table.thead>
                    <x-table.tbody>
                        @forelse ($stokOpnameHistories as $index => $stokOpname)
                            <x-table.tr>
                                <x-table.td class="text-center">{{ $stokOpnameHistories->firstItem() + $index }}</x-table.td>
                                <x-table.td>
                                    {{-- {{ FormatHelper::formatDate($stokOpname->created_at, true) }} --}}
                                    {{ $stokOpname->created_at }}
                                </x-table.td>
                                <x-table.td>
                                    @nominal($stokOpname->jumlah_barang, '-')
                                </x-table.td>
                                <x-table.td>
                                    Rp. @nominal($stokOpname->kerugian)
                                </x-table.td>

                                <x-table.td class="flex justify-center">
                                    @can('stok-opname.cetak-laporan')
                                        <x-table.action-button tooltip="Edit" color="warning" type="href"
                                                               href="{{ route('cetak.stok-opname', $stokOpname->created_at) }}" :newTab=true>
                                            <i class="ti ti-edit-circle"></i> <span class="text-sm">Lihat</span>
                                        </x-table.action-button>
                                    @endcan
                                    {{-- <x-table.action-button tooltip="Edit" color="error" type="href"
                                                           href="{{ route('cetak.stok-opname.pdf', $stokOpname->created_at) }}">
                                        <i class="ti ti-edit-circle"></i> <span class="text-sm">PDF</span>
                                    </x-table.action-button> --}}

                                    {{-- <x-table.action-button tooltip="Hapus" color="error" @click="$wire.dispatch('alert-confirm', { id: '{{ $stokOpname->id }}', method: 'delete-stokOpname' });">
                                        <i class="ti ti-trash-x-filled"></i>
                                    </x-table.action-button> --}}
                                </x-table.td>

                            </x-table.tr>
                        @empty
                            <x-table.tr-empty colspan="7" />
                        @endforelse
                        <x-table.loading-spin />
                    </x-table.tbody>
                </x-table>

                <div class="overflow-y-hidden mt-3">
                    {{ $stokOpnameHistories->onEachSide(1)->links(data: ['scrollTo' => '#table-stokOpname']) }}
                </div>

            </div>
        </div>
    </div>
</div>
