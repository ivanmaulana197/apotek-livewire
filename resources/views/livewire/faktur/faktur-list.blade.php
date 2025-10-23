@php
    use App\Helpers\FormatHelper;
    use Carbon\Carbon;
@endphp

<div>
    <x-badge status="error" icon="">
        <x-slot:message>
            <div class="flex items-center gap-x-3">
                <i class="ti ti-alert-triangle text-lg"></i>
                <h6 class="text-lg">Hutang : </h6>
                <p class="mx-1 text-lg">Rp. {{ FormatHelper::formatNumberToString($hutang) }}</p>
            </div>
        </x-slot:message>
    </x-badge>
    {{-- @include('faktur.components.advance-search-faktur') --}}

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
            <x-form.input-date nama="created_at" model="filters.created_at"></x-form.input-date>
        </div>
        <div class="w-svw sm:w-56" wire:ignore>
            <x-form.select label="Status :" multiple name="rak" :direction=false wire:model.live='filters.status'>
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
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Tgl Masuk" name="faktur.tgl_masuk" />
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Tgl Jatuh Tempo" name="faktur.tgl_jatuh_tempo" />
            <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Biaya Pengeluaran" name="faktur.biaya_belanja" />
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
                <x-table.tr wire:loading.remove class="{{ $warna }}">
                    <x-table.td class="text-center">{{ $fakturs->firstItem() + $index }}</x-table.td>
                    <x-table.td>
                        <a class="font-bold underline" href="{{ route('faktur.detail', $faktur->id) }}" wire:navigate>
                            {!! $this->highlight($faktur->no_faktur) !!}
                        </a>
                    </x-table.td>
                    <x-table.td>
                        {!! $this->highlight($faktur->supplier->nama) !!}
                    </x-table.td>
                    <x-table.td>
                        <h6 >{!! $this->highlight(FormatHelper::formatDate($faktur->tgl_masuk)) !!}</h6>
                    </x-table.td>
                    <x-table.td>
                        <h6 class="font-semibold">{!! $this->highlight(FormatHelper::formatDate($faktur->tgl_jatuh_tempo)) !!}</h6>
                    </x-table.td>
                    <x-table.td>
                        Rp. {{ FormatHelper::formatNumberToString($faktur->total_biaya) }}
                    </x-table.td>
                    <x-table.td class="text-center">

                        <button type="button"
                                class="btn btn-xs cursor-default {{ $faktur->status == 'LUNAS' ? 'bg-success hover:bg-successemphasis' : 'bg-warning hover:bg-warningemphasis' }}">
                            <iconify-icon icon="{{ $faktur->status == 'LUNAS' ? 'gg:check-r' : 'basil:edit-outline' }}"></iconify-icon>
                            {{ $faktur->status }}</button>
                    </x-table.td>

                    @if ($id_barang)
                        <x-table.td>
                            @foreach ($faktur->faktur_barang()->where('id_barang', $id_barang)->get() as $faktur_barang)
                                <p>- {{ FormatHelper::formatNumberToString($faktur_barang->jumlah) }} / {{ $faktur_barang->satuan->nama }}</p>
                            @endforeach
                        </x-table.td>
                        <x-table.td>
                            @foreach ($faktur->faktur_barang()->where('id_barang', $id_barang)->get() as $faktur_barang)
                                {{ $faktur_barang->print_tgl_expired(true) }}
                            @endforeach
                        </x-table.td>
                    @endif
                    <x-table.td class="text-center">

                        <x-dropdown text="Aksi" wire:key='dropdown-{{ $faktur->id }}'>
                            <x-dropdown.item class="bg-warning hover:bg-warningemphasis" href="{{ route('faktur.edit', $faktur->id) }}" wire:navigate>
                                <iconify-icon icon="basil:edit-outline"></iconify-icon>
                                <span class="text-sm">Edit</span>
                            </x-dropdown.item>
                            <x-dropdown.item class="bg-primary hover:bg-primaryemphasis" href="#"
                                             @click=" $wire.dispatch('alert-confirm', {id: '{{ $faktur->id }}', method: 'update-status-faktur', text: 'Apakah anda yakin mengubah status Faktur?'}); ">
                                <iconify-icon icon="fluent:pen-sync-24-filled"></iconify-icon>
                                <span class="text-sm">Ubah Status</span>
                            </x-dropdown.item>
                            <x-dropdown.item class="bg-info hover:bg-infoemphasis" href="{{ route('faktur.detail', $faktur->id) }}" wire:navigate>
                                <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                <span class="text-sm">Detail</span>
                            </x-dropdown.item>
                            <x-dropdown.item class="bg-error hover:bg-erroremphasis" href="#"
                                             @click=" $wire.dispatch('alert-confirm', {id: '{{ $faktur->id }}', method: 'delete-faktur', text: 'Apakah anda yakin menghapus Faktur?'}); ">
                                <iconify-icon icon="basil:trash-outline"></iconify-icon>
                                <span class="text-sm">Hapus</span>
                            </x-dropdown.item>
                        </x-dropdown>
                        {{-- <x-table.action-button @click=" $wire.dispatch('alert-confirm', {id: '{{ $faktur->id }}', method: 'update-status-faktur', text: 'Apakah anda yakin mengubah status Faktur?'}); "
                                               tooltip="Ubah Status ke : {{ $faktur->status == 'LUNAS' ? 'BELUM LUNAS' : 'LUNAS' }}">
                            <iconify-icon icon="fluent:pen-sync-24-filled"></iconify-icon>
                            <span class="text-sm">Ubah Status</span>
                        </x-table.action-button>
                        <x-table.action-button tooltip="Edit" color="warning" type="href" href="{{ route('faktur.edit', $faktur->id) }}" wire:navigate>
                            <iconify-icon icon="basil:edit-outline"></iconify-icon>
                            <span class="text-sm">Edit</span>
                        </x-table.action-button>
                        <x-table.action-button @click=" $wire.dispatch('alert-confirm', {id: '{{ $faktur->id }}', method: 'delete-faktur', text: 'Apakah anda yakin menghapus Faktur?'}); "
                                               tooltip="Hapus Faktur" color="error">
                            <iconify-icon icon="basil:trash-outline"></iconify-icon>
                            <span class="text-sm">Hapus</span>
                        </x-table.action-button> --}}
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

    <div class="overflow-y-hidden mt-3">
        {{ $fakturs->onEachSide(1)->links(data: ['scrollTo' => '#table-faktur']) }}
    </div>

</div>
