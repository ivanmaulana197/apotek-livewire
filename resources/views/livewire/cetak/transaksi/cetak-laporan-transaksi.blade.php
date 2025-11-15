<x-slot:title>Cetak List Transaksi</x-slot:title>

@php
    use App\Helpers\FormatHelper;
@endphp

@script
    <script>
        Alpine.data('cetakTransaksi', () => ({
            datetime_cetak: formatDate(moment(), true),
            tgl_cetak: formatDate(moment()),
            tgl_start: formatDate("{{ $filter_date['start'] }}"),
            tgl_end: formatDate("{{ $filter_date['end'] }}"),
        }))
    </script>
@endscript

<div x-data="cetakTransaksi">
    <div class="mb-2">
        <h2 class="text-2xl text-center font-bold text-gray-800">Laporan Transaksi</h2>
        <p class="text-sm text-gray-600 mt-1">
            Tanggal:
            <span x-text="tgl_start"></span> - <span x-text="tgl_end"></span>
            {{-- {{ FormatHelper::formatDate($filter_date['start']) . ' - ' . FormatHelper::formatDate($filter_date['end']) }} --}}
        </p>
        <p class="text-sm text-gray-600">
            Tanggal Cetak: <span class="font-semibold" x-text="datetime_cetak"></span>
        </p>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-2">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <p class="text-xs text-blue-600 font-semibold mb-1">
                JUMLAH TRANSAKSI
            </p>
            <p class="text-2xl font-bold text-blue-800">{{ count($transaksi) ?? '0' }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <p class="text-xs text-green-600 font-semibold mb-1">
                TOTAL PENDAPATAN
            </p>
            <p class="text-2xl font-bold text-green-800">Rp. @nominal($sum_total_bayar)</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <p class="text-xs text-purple-600 font-semibold mb-1">TOTAL KOMISI</p>
            <p class="text-2xl font-bold text-purple-800">Rp. @nominal($sum_total_komisi)</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <p class="text-xs text-red-600 font-semibold mb-1">TOTAL POTONGAN HARGA</p>
            <p class="text-2xl font-bold text-red-800">Rp. @nominal($sum_potongan_promo)</p>
        </div>

    </div>

    <div class="space-y-2">
        @foreach ($transaksi as $item)
            <div class="border-2 border-gray-300 rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gray-800 text-white px-4 py-3 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="font-bold text-base">{{ $item->no_transaksi }}</span>
                        <span class="text-xs">{{ FormatHelper::formatDate($item->created_at, true, true) }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm mb-2">Kasir: <span class="font-semibold">{{ $item->nama_user }} | <span
                                      class="bg-gray-500 px-2 py-1 rounded text-xs">{{ $item->shift_transaksi }}</span></span></p>
                        <p class="text-sm">
                            <span class="bg-green-500 px-2 py-1 rounded text-xs">{{ $item->nama_metode_pembayaran }}</span>
                        </p>
                    </div>
                </div>
                <!-- Detail -->
                <div class="p-4">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Nama</th>
                                <th class="text-center py-2 px-3 font-semibold text-gray-700">Harga Beli</th>
                                <th class="text-center py-2 px-3 font-semibold text-gray-700">Harga Jual</th>
                                <th class="text-center py-2 px-3 font-semibold text-gray-700">Jumlah</th>
                                <th class="text-right py-2 px-3 font-semibold text-gray-700">Komisi</th>
                                <th class="text-right py-2 px-3 font-semibold text-gray-700">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($item->detail_transaksi as $index => $detail_transaksi)
                                @php
                                    $next = $item->detail_transaksi->get($index + 1)?->id_barang;
                                @endphp
                                <tr>
                                    <td class="py-2 px-3">{{ $detail_transaksi->nama_barang }}</td>
                                    <td class="text-center py-2 px-3">Rp. @nominal($detail_transaksi->harga_beli)</td>
                                    <td class="text-center py-2 px-3">Rp. @nominal($detail_transaksi->harga)</td>
                                    <td class="text-center py-2 px-3">
                                        @nominal($detail_transaksi->jumlah) {{ $detail_transaksi->nama_satuan }}
                                    </td>
                                    <td class="text-right py-2 px-3 {{ $detail_transaksi->komisi < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp. @nominal($detail_transaksi->komisi)
                                    </td>
                                    <td class="text-right py-2 px-3">Rp. @nominal($detail_transaksi->total_harga)</td>
                                </tr>
                                @if ((!$next || $detail_transaksi->id_barang != $next) && ($promo = $item->promo_barang->where('id_barang', $detail_transaksi->id_barang)->first()))
                                    <tr>
                                        <td class="py-2 px-3 font-semibold" colspan="{{ $promo->tipe_promo == 'BARANG BONUS' ? 4 : 5 }}">
                                            **Promo:** {{ $promo->deskripsi }}
                                        </td>
                                        @if ($promo->tipe_promo == 'BARANG BONUS')
                                            <td class="text-right py-2 px-3 text-red-600">Rp. @nominal(-$promo->nominal_promo)</td>
                                        @endif
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Total -->
                    <div class="pt-1 border-t-2 border-gray-300">
                        <div class="flex justify-end">
                            <div class="w-64 space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-semibold">Rp. @nominal($item->sub_total)</span>
                                </div>
                                @if ($item->potongan_diskon > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Diskon:</span>
                                        <span class="font-semibold text-red-600">Rp. @nominal(-$item->potongan_diskon)</span>
                                    </div>
                                @endif
                                @if ($item->pembulatan)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Pembulatan:</span>
                                        <span class="font-semibold text-green-600">Rp. @nominal($item->pembulatan)</span>
                                    </div>
                                @endif
                                <div class="flex justify-between border-t border-gray-300 pt-1">
                                    <span class="font-bold text-lg">TOTAL:</span>
                                    <span class="font-bold text-lg text-green-600">Rp. @nominal($item->total_bayar)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm">Komisi:</span>
                                    <span class="text-sm {{ $item->komisi_bersih < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Rp. @nominal($item->komisi_bersih)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
