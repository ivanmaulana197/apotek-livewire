<x-slot:title>Cetak Laporan Dokter</x-slot:title>

@php
    use App\Helpers\FormatHelper;
@endphp

<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-2">

        <div class="bg-blue-200 dark:bg-blue-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400  font-semibold">Jumlah Tebus Obat</h3>
                <h3 class="font-bold text-2xl mb-1">@nominal($data->sum('jumlah_transaksi') ?? '0')</h3>
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
                <h3 class="font-bold text-2xl mb-1">Rp. @nominal($data->sum('total_transaksi') ?? '0')</h3>
                <h6 class="text-gray-700 text-xs"> Periode: {{ FormatHelper::formatDate($filter_date['start']) }} -
                    {{ FormatHelper::formatDate($filter_date['end']) }}</h6>
            </div>
            <div class="bg-green-400/50 dark:bg-green-300 text-green-800 dark:text-emerald-800 rounded-xl p-3 shadow">
                <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
            </div>
        </div>
    </div>

    <x-table nama="dokter" height="h-full w-full">

        <x-table.thead>
            <x-table.th label="No" />
            <x-table.th label="Nama Dokter" />
            <x-table.th label="No. HP" />
            <x-table.th label="Spesialis" />
            <x-table.th label="Alamat" />
            <x-table.th label="Total Transaksi" />
        </x-table.thead>

        <x-table.tbody>
            @forelse ($data as $index => $item)
                <x-table.tr>
                    <x-table.td class="text-center">{{ $index + 1 }}</x-table.td>
                    <x-table.td>{{ $item->nama }}</x-table.td>
                    <x-table.td>{{ $item->no_hp ?? '-' }}</x-table.td>
                    <x-table.td>{{ $item->spesialis ?? '-' }}</x-table.td>
                    <x-table.td>{{ $item->alamat ?? '-' }}</x-table.td>
                    <x-table.td class="text-right font-semibold">Rp. @nominal($item->total_transaksi ?? '0')</x-table.td>
                </x-table.tr>
            @empty
                <x-table.tr-empty colspan="6" />
            @endforelse

        </x-table.tbody>

    </x-table>
</div>
