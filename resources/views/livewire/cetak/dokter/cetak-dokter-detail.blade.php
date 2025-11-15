<x-slot:title>Cetak Detail Dokter</x-slot:title>

@php
    use App\Helpers\FormatHelper;
@endphp

<div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-2">
        <div>
            <p class="text-sm text-gray-600">Nama Dokter</p>
            <p class="font-semibold text-gray-800">{{ $data->nama }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">No HP</p>
            <p class="font-semibold text-gray-800">{{ $data->no_hp ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Spesialis</p>
            <p class="font-semibold text-gray-800">{{ $data->spesialis }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 mb-2">
        <div class="bg-blue-200 dark:bg-blue-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400  font-semibold">Jumlah Tebus Obat</h3>
                <h3 class="font-bold text-2xl mb-1">@nominal($data->transaksi->count() ?? '0')</h3>
                <h6 class="text-gray-700 text-xs"> Transaksi: {{ FormatHelper::formatDate($filter_date['start']) }} -
                    {{ FormatHelper::formatDate($filter_date['end']) }}</h6>
            </div>

            <div class="bg-blue-400/60 dark:bg-blue-300 text-blue-600 dark:text-sky-800 rounded-xl p-3 shadow">
                <span class="iconify solar--chart-bold-duotone text-2xl font-bold "></span>
            </div>
        </div>
        <div class="bg-green-200 dark:bg-green-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Transaksi : </h3>
                <h3 class="font-bold text-2xl mb-1">Rp. @nominal($data->total_penjualan ?? '0')</h3>
                <h6 class="text-gray-700 text-xs"> Periode: {{ FormatHelper::formatDate($filter_date['start']) }} -
                    {{ FormatHelper::formatDate($filter_date['end']) }}</h6>
            </div>
            <div class="bg-green-400/50 dark:bg-green-300 text-green-800 dark:text-emerald-800 rounded-xl p-3 shadow">
                <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white">
        <table class="min-w-full text-xs sm:text-sm text-gray-700">
            <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <tr>
                    <th class="border-b border-blue-400 px-3 py-2 text-left font-semibold">No Transaksi</th>
                    <th class="border-b border-blue-400 px-3 py-2 text-left font-semibold">Tanggal</th>
                    <th class="border-b border-blue-400 px-3 py-2 text-left font-semibold">Nama Pasien</th>
                    <th class="border-b border-blue-400 px-3 py-2 text-left font-semibold">Obat</th>
                    <th class="border-b border-blue-400 px-3 py-2 text-left font-semibold">Jumlah</th>
                    <th class="border-b border-blue-400 px-3 py-2 text-right font-semibold">Harga Satuan</th>
                    <th class="border-b border-blue-400 px-3 py-2 text-right font-semibold">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($data->transaksi as $index => $item)
                    @foreach ($item->detail_transaksi as $i => $detail_transaksi)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            @if ($i == 0)
                                <td rowspan="{{ $item->detail_transaksi->count() }}" class="px-3 py-2 align-top font-semibold text-gray-800 bg-blue-50">
                                    {{ $item->no_transaksi }}
                                </td>
                                <td rowspan="{{ $item->detail_transaksi->count() }}" class="px-3 py-2 align-top bg-blue-50">
                                    {{ FormatHelper::formatDate($item->created_at) }}
                                </td>
                                <td rowspan="{{ $item->detail_transaksi->count() }}" class="px-3 py-2 align-top bg-blue-50">
                                    {{ $item->pasien->nama_pasien }}
                                </td>
                            @endif

                            <td class="border-b px-3 py-2">{{ $detail_transaksi->detail_barang->barang->nama }}</td>
                            <td class="border-b px-3 py-2">
                                @nominal($detail_transaksi->detail_barang->jumlah)
                                <span class="text-gray-500">{{ $detail_transaksi->detail_barang->satuan->nama }}</span>
                            </td>
                            <td class="border-b px-3 py-2 text-right font-medium">
                                Rp. @nominal($detail_transaksi->harga)
                            </td>
                            <td class="border-b px-3 py-2 text-right font-bold">
                                Rp. @nominal($detail_transaksi->total_harga)
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-blue-50">
                        <td colspan="6" class=" border-y px-3 py-2 text-right font-bold text-gray-800 ">Total Transaksi + Pembulatan</td>
                        <td class="border-y px-3 py-2 text-right font-bold text-green-600">
                            Rp. @nominal($item->total_bayar)
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-4 text-center text-gray-500 bg-gray-50">Tidak Ada Data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
