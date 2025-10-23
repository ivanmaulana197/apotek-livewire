<div class="card shadow-none  border border-border dark:border-darkborder ">
    <div class="flex flex-col">
        <div class="flex flex-col max-h-[400px]">
            <div class="flex-grow overflow-auto rounded-xl">
                <table class="min-w-full divide-y divide-border dark:divide-darkborder"
                    id="table-rak">
                    <thead class="sticky top-0 bg-gray-200 dark:bg-gray-700">
                        <tr>
                            <x-table.th label="No" />
                            <x-table.th-sortable :sortBy="$sortBy"
                                :sortDir="$sortDir"
                                label="Nama Rak"
                                name="nama" />
                            <x-table.th label='Aksi' />

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border dark:divide-darkborder">
                        @forelse ($raks as $index=>$rak)
                            <tr>
                                <td class="p-2 whitespace-nowrap">
                                    {{ $raks->firstItem() + $index }}
                                </td>
                                <td class="p-1 whitespace-nowrap">
                                    <h6 class="text-sm whitespace-normal line-clamp-2 w-48">
                                        {!! $this->highlight($rak->nama) !!}
                                    </h6>

                                </td>
                                <td class="p-2 flex justify-start">
                                    <button type="button"
                                        @click="$dispatch('edit-rak',{rak:'{{ $rak->id }}'})"
                                        class="p-1 me-1 mb-1 text-xl font-medium rounded-xs border border-transparent bg-warning hover:bg-warningemphasis text-white hs-tooltip ">
                                        <i class="ti ti-edit-circle"></i>
                                        {{-- <iconify-icon icon="fa-solid:edit" class="block"></iconify-icon> --}}
                                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-slate-700"
                                            role="tooltip">
                                            Edit
                                        </span>
                                    </button>
                                    <button type="button"
                                        wire:click="$dispatch('alert-confirm', { id: '{{ $rak->id }}', method: 'delete-rak' });"
                                        class="px-1 me-1 mb-1 text-xl font-medium rounded-xs border border-transparent bg-error hover:bg-erroremphasis text-white hs-tooltip">
                                        <i class="ti ti-trash-x-filled"></i>
                                        {{-- <iconify-icon icon="solar:trash-bin-trash-bold-duotone"
                                             class="block"></iconify-icon> --}}
                                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-slate-700"
                                            role="tooltip">
                                            Hapus
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="p-2 text-sm font-semibold text-center text-dark dark:text-white">
                                    Tidak Ada Data
                                </td>
                            </tr>
                        @endforelse



                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


<x-table nama="rak">
    <x-slot:thead>
        <x-table.thead>
            <x-table.th label="No" class="ps-4" />
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Rak" name="nama" class="justify-center"/>
            <x-table.th label='Aksi' class="flex justify-center" />
        </x-table.thead>
    </x-slot:thead>

    <x-slot:tbody>
        <x-table.tbody>
            @forelse ($raks as $index => $rak)
                <tr>
                    <x-table.td class="ps-4">{{ $raks->firstItem() + $index }}</x-table.td>
                    <x-table.td>
                        <h6 class="text-sm whitespace-normal line-clamp-2">
                            {!! $this->highlight($rak->nama) !!}
                        </h6>
                    </x-table.td>
                    <x-table.td class="flex justify-center">
                        <x-table.action-button tooltip="Edit" color="warning" @click="$dispatch('edit-rak',{rak:'{{ $rak->id }}'})">
                            <i class="ti ti-edit-circle"></i>
                        </x-table.action-button>
                        <x-table.action-button tooltip="Hapus" color="error" @click="$wire.dispatch('alert-confirm', { id: '{{ $rak->id }}', method: 'delete-rak' });">
                            <i class="ti ti-trash-x-filled"></i>
                        </x-table.action-button>
                    </x-table.td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-2 text-sm font-semibold text-center text-dark dark:text-white">
                        Tidak Ada Data
                    </td>
                </tr>
            @endforelse

        </x-table.tbody>
    </x-slot:tbody>
</x-table>