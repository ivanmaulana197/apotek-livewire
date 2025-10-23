@php
    use App\Helpers\FormatHelper;
@endphp

<div class="card">
    <div class="card-body">
        <h5 class="card-title">List Promo</h5>
        <div class="flex flex-row flex-wrap items-center gap-3">
            <div wire:ignore>
                <x-form.select label="Show :" name="perPage" :empty-option=false :direction=false wire:model.live='perPage'
                    class="min-w-14">
                    <x-slot:custom-option>
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </x-slot:custom-option>
                </x-form.select>
            </div>
            <div class="w-svw sm:w-56" wire:ignore>
                <x-form.select label="Aktif :" placeholder='Status' name="is_aktif" :direction=false
                    wire:model.live='filters.is_aktif'>
                    <x-slot:custom-option>
                        <option value="AKTIF">AKTIF</option>
                        <option value="NON_AKTIF">TIDAK AKTIF</option>
                    </x-slot:custom-option>
                </x-form.select>
            </div>
            <div class="w-svw sm:w-56" wire:ignore>
                <x-form.select label="Profesi :" placeholder='Profesi' name="filters_profesi" :direction=false multiple wire:model.live='filters.id_profesies' :dataOption="$this->profesies" option-value="id" option-label="nama">
                </x-form.select>
            </div>
            <div class="w-svw sm:w-56" wire:ignore>
                <x-form.label label="Tanggal Promo :"></x-form.label>
                <x-form.input-date nama="filter_date" model="filters.date" :singleDatePicker=true ></x-form.input-date>
            </div>

        </div>
        <div class="mb-2">
            <x-form.input wire:model.live.debounce300ms='search' placeholder="Pencarian Diskon" inputGroup="left" rounded='full'>
                <x-slot:labelInputGroup>
                    <iconify-icon icon="solar:magnifer-linear" class=" text-lg leading-tight">
                    </iconify-icon> <span class="text-md ms-1"> Search</span>
                </x-slot:labelInputGroup>
            </x-form.input>
        </div>

        <x-table nama="promo" height="auto">
            <x-table.thead>
                <x-table.th label="NO." />
                {{-- <x-table.th-sortable-alpine name="nama" label="Nama Promo" /> --}}
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Promo" name="nama" />
                <x-table.th label="Tipe Promo" />
                {{-- <x-table.th-sortable-alpine name="start_date" label="Mulai Promo" /> --}}
                <x-table.th label="Mulai Promo" />
                <x-table.th label="Selesai Promo" />
                <x-table.th label="Status" />
                <x-table.th label="Jenis Pengguna" />
                <x-table.th label="Aksi" />
            </x-table.thead>
            <x-table.tbody>
                @forelse ($promos as $index => $item)
                    <x-table.tr wire:loading.remove>
                        <x-table.td class="text-center">{{ $promos->firstItem() + $index }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-2">{{ $item->nama }}</h6>
                        </x-table.td>
                        <x-table.td>{{ $item->tipe }}</x-table.td>
                        <x-table.td>{{ FormatHelper::formatDate($item->start_date) }}</x-table.td>
                        <x-table.td>{{ FormatHelper::formatDate($item->end_date) ?? '-' }}</x-table.td>
                        <x-table.td>
                            @if ($item->is_aktif == 1)
                                <x-badge message="Aktif" status="success" icon="gg:check-r" />
                            @else
                                <x-badge message="Tidak Aktif" status="warning" icon="gg:close-r" />
                            @endif
                        </x-table.td>
                        <x-table.td>
                            @if ($item->is_member == 1)
                                <x-badge message="Member" status="warning" icon="ic:round-key" />
                            @else
                                <x-badge message="Public" status="success" icon="ic:sharp-public" />
                            @endif
                        </x-table.td>
                        <x-table.td class="text-center">
                            {{-- <x-table.action-button tooltip="Detail" color="secondary" type="href" href="{{ route('promo.detail', $item->id) }}" wire:navigate> --}}
                            <x-table.action-button tooltip="Detail" color="info"
                                @click="$dispatch('toggle-modal-detailpromo', { detailpromoModal: true }); $dispatch('detail-promo',{id_promo: '{{ $item->id }}'})">
                                <i class="ti ti-eye"></i>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Edit" color="warning" type="href"
                                href="{{ route('promo.edit', $item->id) }}" wire:navigate>
                                {{-- <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('toggle-modal-promo', { promoModal: true }); $dispatch('edit-promo',{promo: '{{ $item->id }}'})"> --}}
                                <i class="ti ti-edit-circle"></i>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Hapus" color="error" @click="$wire.dispatch('alert-confirm', { id: '{{ $item->id }}', method: 'delete-promo' });">
                                <i class="ti ti-trash-x-filled"></i>
                            </x-table.action-button>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <tr wire:loading.remove>
                        <td colspan="7" class="p-2 text-sm font-semibold text-center text-dark dark:text-white">
                            Tidak Ada Data
                        </td>
                    </tr>
                @endforelse

                <x-table.loading colspan="7" />
            </x-table.tbody>
        </x-table>

        <div class="mt-3 overflow-y-hidden">
            {{ $promos->onEachSide(1)->links(data: ['scrollTo' => '#table-promo']) }}
        </div>
    </div>
    <x-modal name="detailpromo" size="6xl">
        @livewire('promo.promo-detail.promo-detail-modal')
    </x-modal>
</div>
