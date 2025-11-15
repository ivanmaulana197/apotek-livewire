<div x-data="{
    filter_date: @js($filter_date)
}" x-init="filter_date.start = formatDate(filter_date.start);
filter_date.end = formatDate(filter_date.end);">

    <div class="mb-2">
        <h2 class="text-2xl text-center font-bold text-gray-800">Laporan Member</h2>
        <p class="text-sm text-gray-600 mt-1">
            PERIODE:
            <span x-text="filter_date.start"></span> - <span x-text="filter_date.end"></span>
            {{-- {{ FormatHelper::formatDate($filter_date['start']) . ' - ' . FormatHelper::formatDate($filter_date['end']) }} --}}
        </p>
        <p class="text-sm text-gray-600">
            Tanggal Cetak: <span class="font-semibold" x-text="formatDate(moment(),true)"></span>
        </p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-2">
        <div>
            <p class="text-sm text-gray-600">Nama Member</p>
            <p class="font-semibold text-gray-800">{{ $data->nama }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">No HP</p>
            <p class="font-semibold text-gray-800">{{ $data->no_hp ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Spesialis</p>
            <p class="font-semibold text-gray-800">{{ $data->profesi->nama }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 mb-2">
        <div class="bg-blue-200 dark:bg-blue-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400  font-semibold">Jumlah Transaksi</h3>
                <h3 class="font-bold text-2xl mb-1">@nominal($data->jumlah_transaksi ?? '0')</h3>
                {{-- <h6 class="text-gray-700 text-xs" x-text="'TRANSAKSI ' + filter_date.start + ' sampai ' + filter_date.end"></h6> --}}
            </div>
            <div class="bg-blue-400/60 dark:bg-blue-300 text-blue-600 dark:text-sky-800 rounded-xl p-3 print:bg-transparent shadow">
                <span class="iconify solar--chart-bold-duotone text-2xl font-bold "></span>
            </div>
        </div>
        <div class="bg-green-200 dark:bg-green-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Belanja : </h3>
                <h3 class="font-bold text-2xl mb-1">Rp. @nominal($data->total_bayar ?? '0')</h3>
                {{-- <h6 class="text-gray-700 text-xs" x-text="'PEMBELANJAAN ' + filter_date.start + ' sampai ' + filter_date.end"></h6> --}}
            </div>
            <div class="bg-green-400/50 dark:bg-green-300 text-green-800 dark:text-emerald-800 rounded-xl p-3 print:bg-transparent shadow">
                <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
            </div>
        </div>
        <div class="bg-purple-200 dark:bg-purple-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Benefit : </h3>
                <h3 class="font-bold text-2xl mb-1">Rp. @nominal($data->total_benefit ?? '0')</h3>
                {{-- <h6 class="text-gray-700 text-xs" x-text="'BENEFIT ' + filter_date.start + ' sampai ' + filter_date.end"></h6> --}}
            </div>
            <div class="bg-purple-400/50 dark:bg-purple-300 text-purple-800 dark:text-emerald-800 rounded-xl p-3 print:bg-transparent shadow">
                <span class="iconify iconamoon--discount-duotone text-2xl font-bold "></span>
            </div>
        </div>
    </div>

    <div class="space-y-2">
        @foreach ($data->transaksi as $item)
            <div class="border-2 border-gray-300 rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gray-800 text-white px-4 py-3 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <span class="font-bold text-lg">{{ $item->no_transaksi }}</span>
                        <span class="text-sm" x-text="formatDate('{{ $item->created_at }}', true, true)"></span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm">Kasir: <span class="font-semibold">{{ $item->user->name }}</span></p>
                        <p class="text-sm">
                            Metode:
                            <span class="bg-green-500 px-2 py-1 rounded text-xs">{{ $item->metode_pembayaran->nama }}</span>
                        </p>
                    </div>
                </div>

                <!-- Detail -->
                <div class="p-4">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-left py-2 px-3 font-semibold text-gray-700">Nama</th>
                                <th class="text-center py-2 px-3 font-semibold text-gray-700">Jumlah</th>
                                <th class="text-center py-2 px-3 font-semibold text-gray-700">Harga </th>
                                <th class="text-right py-2 px-3 font-semibold text-gray-700">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($item->detail_transaksi as $index => $detail_transaksi)
                                @php
                                    $next = $item->detail_transaksi->get($index + 1)?->detail_barang->id_barang;
                                @endphp
                                <tr>
                                    <td class="py-2 px-3">{{ $detail_transaksi->detail_barang->barang->nama }}</td>
                                    <td class="text-center py-2 px-3">Rp. @nominal($detail_transaksi->harga)</td>
                                    <td class="text-center py-2 px-3">
                                        {{ $detail_transaksi->detail_barang->jumlah }}/{{ $detail_transaksi->detail_barang->satuan->nama }}</td>
                                    <td class="text-right py-2 px-3">Rp. @nominal($detail_transaksi->total_harga)</td>
                                </tr>
                                @if (
                                    (!$next || $detail_transaksi->detail_barang->id_barang != $next) &&
                                        ($promo = $item->transaksi_list_promo->where('id_barang', $detail_transaksi->detail_barang->id_barang)->first()))
                                    <tr>
                                        <td class="py-2 px-3 font-semibold" colspan="3">
                                            **Promo:** {{ $promo->deskripsi }}
                                        </td>
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
                                @if ($item->benefit > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Benefit Member:</span>
                                        <span class="font-semibold text-green-600">Rp. @nominal($item->benefit)</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
