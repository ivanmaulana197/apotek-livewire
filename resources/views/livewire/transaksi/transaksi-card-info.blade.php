<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 rounded-xl mb-2">
    <div class="bg-green-200 dark:bg-green-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Transaksi : <span
                      class="text-black dark:text-white font-bold">{{ $shift_transaksi }}</span></h3>
            <h3 class="font-bold text-2xl mb-1">Rp. {{ $total_bayar }}</h3>

            <table class="text-xs text-gray-600 dark:text-gray-300">
                <tr>
                    <td class="pr-2">Tunai</td>
                    <td class="px-1">:</td>
                    <td class="ps-1 font-medium">Rp. {{ $total_bayar_tunai }}</td>
                </tr>
                <tr>
                    <td class="pr-2">Non Tunai</td>
                    <td class="px-1">:</td>
                    <td class="ps-1 font-medium">Rp. {{ $total_bayar_non_tunai }}</td>
                </tr>
            </table>
        </div>

        <div class="bg-green-400/50 dark:bg-green-300 text-green-800 dark:text-emerald-800 rounded-xl p-3 shadow">
            <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
        </div>
    </div>

    <div class="bg-orange-200 dark:bg-orange-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400  font-semibold">Total Transaksi</h3>
            <h3 class="text-xl font-semibold"> UMUM </h3>
            <h3 class="font-bold text-2xl mb-1">Rp. {{ $total_bayar_non_resep }}</h3>
        </div>

        <div class="bg-orange-400/60 dark:bg-orange-300 text-orange-600 dark:text-yellow-800 rounded-xl p-3 shadow">
            <span class="iconify guidance--bank text-2xl font-bold "></span>
        </div>
    </div>
    <div class="bg-purple-200 dark:bg-purple-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400  font-semibold">Total Transaksi</h3>
            <h3 class="text-xl font-semibold"> RESEP </h3>
            <h3 class="font-bold text-2xl mb-1">Rp. {{ $total_bayar_resep }}</h3>
        </div>

        <div class="bg-purple-400/60 dark:bg-purple-300 text-purple-600 dark:text-violet-800 rounded-xl p-3 shadow">
            <span class="iconify carbon--reminder-medical text-2xl font-bold"></span>
        </div>
    </div>

    <div class="bg-blue-200 dark:bg-blue-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400  font-semibold">Jumlah Transaksi</h3>
            <h3 class="font-bold text-2xl mb-1">{{ $total_transaksi }}</h3>
            <h6 class="text-gray-700 text-xs"> Transaksi: {{ $status_tanggal }}</h6>
        </div>

        <div class="bg-blue-400/60 dark:bg-blue-300 text-blue-600 dark:text-sky-800 rounded-xl p-3 shadow">
            <span class="iconify solar--chart-bold-duotone text-2xl font-bold "></span>
        </div>
    </div>
</div>
