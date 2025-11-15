@php
    use App\Helpers\FormatHelper;
    use Carbon\Carbon;
@endphp

<div>
    {{-- <x-badge status="error" icon="">
        <x-slot:message>
            <div class="flex items-center gap-x-3">
                <span class="iconify tabler--alert-triangle text-lg"></span>
                <h6 class="text-lg">Hutang : </h6>
                <p class="mx-1 text-lg">Rp. {{ FormatHelper::formatNumberToString($hutang) }}</p>
            </div>
        </x-slot:message>
    </x-badge> --}}
    {{-- @include('faktur.components.advance-search-faktur') --}}

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
        <div class="bg-blue-200 dark:bg-blue-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400  font-semibold">Jumlah Faktur</h3>
                <h3 class="font-bold text-2xl mb-1">@nominal($rekap->total_faktur ?? '0')</h3>
            </div>

            <div class="bg-blue-400/60 dark:bg-blue-300 text-blue-600 dark:text-sky-800 rounded-xl p-3 shadow">
                <span class="iconify solar--chart-bold-duotone text-2xl font-bold "></span>
            </div>
        </div>

        <div class="bg-green-200 dark:bg-green-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Pengeluaran</h3>
                <h3 class="font-bold text-2xl mb-1">Rp. @nominal($rekap->total_lunas ?? '0')</h3>
            </div>

            <div class="bg-green-400/50 dark:bg-green-300 text-green-800 dark:text-emerald-800 rounded-xl p-3 shadow">
                <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
            </div>
        </div>
        <div class="bg-red-200 dark:bg-red-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Hutang</h3>
                <h3 class="font-bold text-2xl mb-1">Rp. @nominal($rekap->total_belum_lunas ?? '0')</h3>
            </div>

            <div class="bg-red-400/50 dark:bg-red-300 text-red-800 dark:text-emerald-800 rounded-xl p-3 shadow">
                <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
            </div>
        </div>
    </div>

    <x-datatable nama_table="faktur" :$perPage placeholder_search="Masukan No. Kwitansi / Nama Supplier, dll">
        <div class="">
            <x-form.label label="Tgl Jatuh Tempo :" />
            <x-form.input-date nama="tgl_jatuh_tempo" model="filters.tgl_jatuh_tempo"></x-form.input-date>
        </div>
        <div class="w-svw sm:w-56" wire:ignore>
            <x-form.select label="Supplier :" multiple name="supplier" :direction=false wire:model.live='filters.id_suppliers' :dataOption="$this->suppliers"
                           option-value="id" option-label="nama">
            </x-form.select>
        </div>
        <div class="">
            <x-form.label label="Dibuat :" />
            <x-form.input-date nama="created_at" model="filters.created_at" :select_date="$filters['created_at']"></x-form.input-date>
        </div>
        <div class="w-svw sm:w-56" wire:ignore>
            <x-form.select label="Status :" name="status" :direction=false wire:model.live='filters.status'>
                <x-slot:custom-option>
                    <option value="LUNAS">LUNAS</option>
                    <option value="BELUM LUNAS">BELUM LUNAS</option>
                </x-slot:custom-option>
            </x-form.select>
        </div>
    </x-datatable>

    <x-table nama="faktur" height="auto">
        <x-table.thead>
            <x-table.th label="NO." />
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="No Kwitansi" name="faktur.no_faktur" />
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Supplier" name="supplier.nama" />
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Tgl Masuk" name="faktur.created_at" />
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Tgl Jatuh Tempo" name="faktur.tgl_jatuh_tempo" />
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Biaya Pengeluaran" name="faktur.total_biaya" />
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Status" name="faktur.status" />

            @if ($id_barang)
                <x-table.th label="Jumlah" />
                <x-table.th label="Tgl Expired" />
            @endif

            <x-table.th label="Aksi" />
        </x-table.thead>
        <x-table.tbody>
            @forelse ($fakturs as $index => $faktur)
                @php
                    $today = Carbon::today();
                    $tgl_jatuh_tempo = Carbon::parse($faktur->tgl_jatuh_tempo); // contoh
                    $diffInDays = $today->diffInDays($tgl_jatuh_tempo, false);
                    $diffInMonths = $today->diffInMonths($tgl_jatuh_tempo, false);
                    $warna = '';
                    if ($diffInDays <= 0 && $faktur->status != 'LUNAS') {
                        $warna = 'bg-red-300 dark:bg-red-600/40';
                    } elseif ($diffInMonths <= 1 && $faktur->status != 'LUNAS') {
                        $warna = 'bg-amber-200/60 dark:bg-bg-amber-600/40';
                    }
                @endphp
                <x-table.tr class="{{ $warna }}">
                    <x-table.td class="text-center">{{ $fakturs->firstItem() + $index }}</x-table.td>
                    <x-table.td>
                        <a class="font-bold underline" href="{{ route('faktur.detail', $faktur->id) }}" wire:navigate>
                            {{ $faktur->no_faktur }}
                        </a>
                    </x-table.td>
                    <x-table.td>
                        {{ $faktur->supplier->nama }}
                    </x-table.td>
                    <x-table.td x-text="formatDate('{{ $faktur->created_at }}',true)"></x-table.td>
                    <x-table.td class="font-semibold" x-text="formatDate('{{ $faktur->tgl_jatuh_tempo }}')"></x-table.td>
                    <x-table.td>
                        Rp. @nominal($faktur->total_biaya)
                    </x-table.td>
                    <x-table.td class="text-center">
                        <button type="button"
                                class="btn btn-xs cursor-default {{ $faktur->status == 'LUNAS' ? 'bg-success hover:bg-successemphasis' : 'bg-warning hover:bg-warningemphasis' }}">
                            <span class="iconify {{ $faktur->status == 'LUNAS' ? 'tabler--checkbox' : 'basil--edit-outline' }}"></span>
                            {{ $faktur->status }}</button>
                    </x-table.td>

                    @if ($id_barang)
                        <x-table.td>
                            @foreach ($faktur->detail_faktur_barang as $faktur_barang)
                                <p class="flex items-center"> <i class="iconify iconamoon--arrow-right-2-fill"></i>
                                    @nominal($faktur_barang->jumlah) / {{ $faktur_barang->satuan->nama }}</p>
                            @endforeach
                        </x-table.td>
                        <x-table.td>
                            @foreach ($faktur->detail_faktur_barang as $faktur_barang)
                                @foreach ($faktur_barang->tgl_expired as $item)
                                    <p class="flex items-center"> <i class="iconify iconamoon--arrow-right-2-fill"></i>
                                        @nominal($item['jumlah']) {{ $faktur_barang->barang->satuan_utama->nama }} <br>
                                        [{{ $item['tgl'] }}]
                                    </p>
                                @endforeach
                                {{-- {{ $faktur_barang->tgl_expired[0]['tgl'] }} --}}
                            @endforeach
                        </x-table.td>
                    @endif
                    <x-table.td class="text-center">

                        <x-dropdown text="Aksi" wire:key='dropdown-{{ $faktur->id }}'>
                            <x-dropdown.item class="bg-warning hover:bg-warningemphasis" href="{{ route('faktur.edit', $faktur->id) }}" wire:navigate>
                                <span class="iconify basil--edit-outline"></span>
                                <span class="text-sm">Edit</span>
                            </x-dropdown.item>
                            <x-dropdown.item class="bg-primary hover:bg-primaryemphasis" href="#"
                                             @click=" $wire.dispatch('alert-confirm', {id: '{{ $faktur->id }}', method: 'update-status-faktur', text: 'Apakah anda yakin mengubah status Faktur?'}); ">
                                <span class="iconify fluent--pen-sync-24-filled"></span>
                                <span class="text-sm">Ubah Status</span>
                            </x-dropdown.item>
                            <x-dropdown.item class="bg-info hover:bg-infoemphasis" href="{{ route('faktur.detail', $faktur->id) }}" wire:navigate>
                                <span class="iconify solar--eye-bold"></span>
                                <span class="text-sm">Detail</span>
                            </x-dropdown.item>
                            <x-dropdown.item class="bg-error hover:bg-erroremphasis" href="#"
                                             @click=" $wire.dispatch('alert-confirm', {id: '{{ $faktur->id }}', method: 'delete-faktur', text: 'Apakah anda yakin menghapus Faktur?'}); ">
                                <span class="iconify basil--trash-outline"></span>
                                <span class="text-sm">Hapus</span>
                            </x-dropdown.item>
                        </x-dropdown>
                        {{-- <x-table.action-button @click=" $wire.dispatch('alert-confirm', {id: '{{ $faktur->id }}', method: 'update-status-faktur', text: 'Apakah anda yakin mengubah status Faktur?'}); "
                                               tooltip="Ubah Status ke : {{ $faktur->status == 'LUNAS' ? 'BELUM LUNAS' : 'LUNAS' }}">
                            <span class="iconify fluent--pen-sync-24-filled"></span>
                            <span class="text-sm">Ubah Status</span>
                        </x-table.action-button>
                        <x-table.action-button tooltip="Edit" color="warning" type="href" href="{{ route('faktur.edit', $faktur->id) }}" wire:navigate>
                            <span class="iconify basil--edit-outline"></span>
                            <span class="text-sm">Edit</span>
                        </x-table.action-button>
                        <x-table.action-button @click=" $wire.dispatch('alert-confirm', {id: '{{ $faktur->id }}', method: 'delete-faktur', text: 'Apakah anda yakin menghapus Faktur?'}); "
                                               tooltip="Hapus Faktur" color="error">
                            <span class="iconify basil--trash-outline"></span>
                            <span class="text-sm">Hapus</span>
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
        {{ $fakturs->onEachSide(1)->links(data: ['scrollTo' => '#table-faktur']) }}
    </div>

</div>
