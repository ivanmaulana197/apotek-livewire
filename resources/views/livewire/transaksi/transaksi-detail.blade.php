<x-slot:title>Detail Transaksi</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Detail Transaksi">
        <x-slot:button>
            <a class="btn btn-xs bg-secondary hover:bg-secondaryemphasis" href="{{ route('transaksi') }}" wire:navigate>
                <span class="iconify famicons--caret-back"></span>
                Kembali
            </a>
        </x-slot:button>
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
            <x-breadcrumb.before name="Transaksi" href="{{ route('transaksi') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    @script
        <script>
            Alpine.data('transaksiDetail', () => ({
                cetakStrukPembayaran() {
                    let url = "{{ route('cetak.struk-pembayaran', ['id_transaksi' => $transaksi->id]) }}";
                    window.open(url, '_blank', 'width=700,height=600');
                }
            }))
        </script>
    @endscript

    <div class="grid grid-cols-12 gap-2" x-data="transaksiDetail">
        <div class="col-span-12 sm:col-span-8">
            <div class="card mb-3">
                <div class="card-body py-3 px-5 gap-0">
                    <div class="card-title mb-0 flex flex-wrap justify-between">
                        <h6>Data Transaksi</h6>
                        @canany(['kasir.cetak.struk-pembayaran-own', 'kasir.cetak.struk-pembayaran-all'])
                            <button class="btn btn-sm" @click="cetakStrukPembayaran">
                                <span class="iconify tabler--printer text-lg"></span>
                                Cetak Struk
                            </button>
                        @endcanany
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-2">
                        <x-form.text-display label="No Transaksi" class="bg-gray-200 dark:bg-gray-800" value="{{ $transaksi->no_transaksi }}" />
                        <x-form.text-display label="Jenis Transaksi" class="bg-gray-200 dark:bg-gray-800" value="{{ $transaksi->jenis_transaksi }}" />
                        <x-form.text-display label="Shift Transaksi" class="bg-gray-200 dark:bg-gray-800" value="{{ $transaksi->shift_transaksi }}" />
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        Detail Transaksi Barang
                    </div>
                    <x-table nama="list_barang_transaksi">
                        <x-table.thead>
                            <x-table.th label="No" />
                            <x-table.th label="Nama Barang" />
                            <x-table.th label="Harga" />
                            <x-table.th label="Jumlah" />
                            <x-table.th label="Total" />
                            <x-table.th label="Aksi" />
                        </x-table.thead>
                        <x-table.tbody>
                            @foreach ($transaksi->detail_transaksi as $index => $detail_transaksi)
                                <x-table.tr>
                                    <x-table.td class="text-center">{{ $index + 1 }}</x-table.td>
                                    <x-table.td class="font-bold">{{ $detail_transaksi->detail_barang->barang->nama }}</x-table.td>
                                    <x-table.td>
                                        {{-- menCek apakah ini satuan utama --}}
                                        <div class="text-sm">
                                            Rp. {{ $detail_transaksi->harga }} / {{ $detail_transaksi->detail_barang->satuan->nama }}
                                        </div>
                                        @if ($detail_transaksi->detail_barang->id_satuan != $detail_transaksi->detail_barang->barang->id_satuan_utama)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                &asymp; Rp. {{ $detail_transaksi->harga_satuan_utama }}
                                                / {{ $detail_transaksi->detail_barang->barang->satuan_utama->nama }}
                                            </div>
                                        @endif
                                    </x-table.td>
                                    <x-table.td>
                                        <div class="flex justify-center rounded-lg">
                                            {{ $detail_transaksi->detail_barang->jumlah }} / {{ $detail_transaksi->detail_barang->satuan->nama }}
                                        </div>
                                    </x-table.td>
                                    <x-table.td>
                                        Rp. {{ $detail_transaksi->total_harga }}
                                    </x-table.td>
                                    <x-table.td class="flex items-center justify-center gap-x-1">
                                        {{-- @include('transaksi.components.badge-tooltip-info-barang') --}}
                                        <x-table.action-button tooltip="Retur" color="error"
                                                               @click="$wire.dispatch('alert-confirm', { id: '{{ $detail_transaksi->detail_barang->id }}', method: 'retur-transaksi-barang' });">
                                            <span class="iconify tabler--trash-x-filled text-lg"></span>
                                            <p class="text-sm">Retur</p>
                                        </x-table.action-button>
                                    </x-table.td>

                                </x-table.tr>
                                @if ($detail_transaksi->promo_barang)
                                    <x-table.tr-empty class="ps-4 text-start align-top" colspan="8">
                                        <x-slot:content>
                                            **Promo:** {{ $detail_transaksi->promo_barang->deskripsi }}
                                        </x-slot:content>
                                    </x-table.tr-empty>
                                @endif
                            @endforeach
                        </x-table.tbody>
                    </x-table>
                </div>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-4">
            <div class="card">
                <div class="card-body p-3 gap-0 ">
                    <div class="card-title bg-warning py-2 px-1 rounded-lg justify-center mb-2">
                        <span class="text-4xl text-white font-mono">
                            Rp. {{ $transaksi->total_bayar }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-x-3">
                        <div
                             class="flex justify-between py-1 lg:text-lg px-3 rounded-lg border border-bordergray bg-gray-100  dark:bg-gray-700 dark:border-gray-700">
                            No Transaksi : <span class="text-black dark:text-white font-semibold">
                                {{ $transaksi->no_transaksi }}</span>
                        </div>
                        <x-form.text-display label="Member" class="bg-gray-200 dark:bg-gray-800" value="{{ $transaksi->member->nama ?? '-' }}" />
                        @if ($transaksi->jenis_transaksi == 'RESEP')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-3">
                                <x-form.text-display label="Nama Pasien" class="bg-gray-200 dark:bg-gray-800" value="{{ $transaksi->pasien->nama_pasien }}" />

                                <x-form.text-display label="Nama Pasien" class="bg-gray-200 dark:bg-gray-800"
                                                     value="{{ $transaksi->pasien->alamat_pasien }}" />

                                <x-form.text-display label="Nama Pasien" class="bg-gray-200 dark:bg-gray-800" value="{{ $transaksi->pasien->no_hp_pasien }}" />

                                <x-form.text-display label="Dokter" class="bg-gray-200 dark:bg-gray-800" value="{{ $transaksi->dokter->nama ?? '-' }}" />
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-3">
                        <x-form.text-display label="SUB TOTAL" labelInputGroup="Rp" inputGroup="left" class="bg-gray-200 dark:bg-gray-800"
                                             value="{{ $transaksi->sub_total }}" />
                        <x-form.text-display id="total" label="TOTAL BAYAR" labelInputGroup="Rp" inputGroup="left" class="bg-gray-200 dark:bg-gray-800"
                                             value="{{ $transaksi->total_bayar }}" />
                        <x-form.text-display label="POTONGAN" labelInputGroup="Rp" inputGroup="left" class="bg-gray-200 dark:bg-gray-800"
                                             value="{{ $transaksi->potongan_diskon }}" />
                        <x-form.text-display label="BAYAR" labelInputGroup="Rp" inputGroup="left" class="bg-gray-200 dark:bg-gray-800"
                                             value="{{ $transaksi->bayar }}" />
                        <x-form.text-display label="METODE PEMBAYARAN" class="bg-gray-200 dark:bg-gray-800"
                                             value="{{ $transaksi->metode_pembayaran->nama ?? '-' }}" />
                        <x-form.text-display label="KEMBALIAN" labelInputGroup="Rp" inputGroup="left" class="bg-gray-200 dark:bg-gray-800"
                                             value="{{ $transaksi->kembalian }}" />
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
