<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-2">
    <div class="bg-blue-200 dark:bg-blue-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400  font-semibold">Jumlah Terjual</h3>
            <h3 class="font-bold text-2xl mb-1">@decimal($data->total_jumlah_dalam_satuan_utama ?? '0') / {{ $data->nama_satuan_utama ?? '-' }}</h3>
            <h5 class="text-gray-700 text-md"> setara: <span class="italic font-semibold">{{ $format_jumlah_terjual }}</span></h5>
            <h6 class="text-gray-700 text-xs"> Transaksi: {{ $filter_date['start'] }} sampai {{ $filter_date['end'] }}</h6>
        </div>

        <div class="bg-blue-400/60 dark:bg-blue-300 text-blue-600 dark:text-sky-800 rounded-xl p-3 shadow">
            <span class="iconify solar--chart-bold-duotone text-2xl font-bold "></span>
        </div>
    </div>

    <div class="bg-green-200 dark:bg-green-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Transaksi {{ $nama_barang }} : <span
                      class="text-black dark:text-white font-bold">{{ $shift_transaksi }}</span></h3>
            <h3 class="font-bold text-2xl mb-1">Rp. @nominal($data->total_harga ?? '0')</h3>
            <h6 class="text-gray-700 text-xs"> Periode: {{ $filter_date['start'] }} sampai {{ $filter_date['end'] }}</h6>
        </div>

        <div class="bg-green-400/50 dark:bg-green-300 text-green-800 dark:text-emerald-800 rounded-xl p-3 shadow">
            <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
        </div>
    </div>
</div>
