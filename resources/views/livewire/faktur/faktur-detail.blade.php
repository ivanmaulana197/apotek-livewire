<x-slot:title>Detail Faktur</x-slot:title>
@php
    use App\Helpers\FormatHelper;
@endphp
<div class="">
    <x-breadcrumb name="Detail Faktur">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
            <x-breadcrumb.before name="Data Faktur Masuk" href="{{ route('faktur') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="card">
        <div class="card-body">
            <div class="flex gap-x-2">
                <a class="btn btn-sm bg-secondary hover:bg-secondaryemphasis" href="{{ route('faktur') }}" wire:navigate>
                    Kembali
                </a>
                <a class="btn btn-sm hover:bg-primaryemphasis" href="{{ route('faktur.tambah') }}" wire:navigate>
                    + Faktur Masuk
                </a>
                <a class="btn btn-sm bg-warning hover:bg-warningemphasis" href="{{ route('faktur.edit', $faktur->id) }}" wire:navigate>
                    Edit
                </a>
                <button class="btn btn-sm bg-info hover:bg-infoemphasis hs-tooltip"
                        @click=" $wire.dispatch('alert-confirm', {id: '{{ $faktur->id }}', method: 'update-status-faktur', text: 'Apakah anda yakin mengubah status Faktur?'}); ">
                    <span
                          class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-slate-700">
                        Ubah Status ke : {{ $faktur->status == 'LUNAS' ? 'BELUM LUNAS' : 'LUNAS' }}
                    </span>
                    <span class="iconify fluent--pen-sync-24-filled"></span>
                    <span class="text-sm">Ubah Status</span>
                </button>

            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4 mb-2">
                <x-span-grid label="No Kwitansi" :data="$faktur->no_faktur" />

                <x-span-grid label="Faktur Masuk" :data="FormatHelper::formatDate($faktur->created_at, true)" />

                <x-span-grid label="Supplier" :data="$faktur->supplier->nama" />

                <x-span-grid label="Nominal" :data="'Rp. ' . FormatHelper::formatNumberToString($faktur->total_biaya)" />

                <x-span-grid label="Jatuh Tempo" :data="FormatHelper::formatDate($faktur->tgl_jatuh_tempo)" />

                <x-span-grid label="Status">
                    <x-slot:data>
                        <x-badge message="{{ $faktur->status }}" status="{{ $faktur->status == 'LUNAS' ? 'success' : 'warning' }}" icon="tabler--checkbox" />
                    </x-slot:data>
                </x-span-grid>
            </div>

            <x-table nama="faktur-barang" height="auto">
                <x-table.thead>
                    <x-table.th label="NO." />
                    <x-table.th label="Nama Barang" />
                    <x-table.th label="Jumlah" />
                    <x-table.th label="Tgl Expired" />
                    <x-table.th label="Harga Beli" />

                </x-table.thead>
                <x-table.tbody>
                    @forelse ($faktur->detail_faktur as $index => $detail_faktur)
                        <x-table.tr>
                            <x-table.td class="ps-4">{{ $index + 1 }}</x-table.td>
                            <x-table.td>{{ $detail_faktur->detail_barang->barang->nama }}</x-table.td>
                            <x-table.td class="text-center">
                                {{ FormatHelper::formatNumberToString($detail_faktur->detail_barang->jumlah) . ' / ' . $detail_faktur->detail_barang->satuan->nama }}
                                @if ($detail_faktur->detail_barang->jumlah !== $detail_faktur->detail_barang->jumlah_dalam_satuan_utama)
                                    <br>
                                    &asymp;
                                    {{ FormatHelper::formatNumberToString($detail_faktur->detail_barang->jumlah_dalam_satuan_utama) . ' / ' . $detail_faktur->detail_barang->barang->satuan_utama->nama }}
                                @endif
                            </x-table.td>
                            <x-table.td class="text-center">{{ $detail_faktur->detail_barang->print_tgl_expired() }}</x-table.td>
                            <x-table.td class="text-center">{{ FormatHelper::formatNumberToString($detail_faktur->harga_beli) ?? '-' }}</x-table.td>

                        </x-table.tr>
                    @empty
                        <x-table.tr-empty colspan="6" />
                    @endforelse
                    <x-table.loading-spin />
                </x-table.tbody>
            </x-table>
        </div>
    </div>
</div>
