@php
    use App\Helpers\FormatHelper;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-3 mb-2">
    <div class="bg-blue-200 dark:bg-blue-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400  font-semibold">Total Transaksi</h3>
            <h3 class="font-bold text-2xl mb-1">@nominal($data->total_transaksi ?? '0')</h3>
            <h6 class="text-gray-700 text-xs"> Transaksi: {{ FormatHelper::formatDate($filter_date['start']) }} sampai
                {{ FormatHelper::formatDate($filter_date['end']) }}</h6>
        </div>
        <div class="bg-blue-400/60 dark:bg-blue-300 text-blue-600 dark:text-sky-800 rounded-xl p-3 shadow">
            <span class="iconify solar--chart-bold-duotone text-2xl font-bold "></span>
        </div>
    </div>
    <div class="bg-green-200 dark:bg-green-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Belanja : </h3>
            <h3 class="font-bold text-2xl mb-1">Rp. @nominal($data->total_bayar ?? '0')</h3>
            <h6 class="text-gray-700 text-xs"> Periode: {{ FormatHelper::formatDate($filter_date['start']) }} sampai
                {{ FormatHelper::formatDate($filter_date['end']) }}</h6>
        </div>
        <div class="bg-green-400/50 dark:bg-green-300 text-green-800 dark:text-emerald-800 rounded-xl p-3 shadow">
            <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
        </div>
    </div>
    <div class="bg-purple-200 dark:bg-purple-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
        <div>
            <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Benefit : </h3>
            <h3 class="font-bold text-2xl mb-1">Rp. @nominal($data->total_benefit ?? '0')</h3>
            <h6 class="text-gray-700 text-xs"> Periode: {{ FormatHelper::formatDate($filter_date['start']) }} sampai
                {{ FormatHelper::formatDate($filter_date['end']) }}</h6>
        </div>
        <div class="bg-purple-400/50 dark:bg-purple-300 text-purple-800 dark:text-emerald-800 rounded-xl p-3 shadow">
            <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
        </div>
    </div>
</div>
