<x-slot:title>Detail Transaksi Barang {{ $data->nama ?? '' }}</x-slot:title>

<div class="">
    <div class="card mb-2">
        <div class="card-body p-2">
            <div class="flex flex-wrap gap-2">
                @can('barang.detail')
                    <a wire:navigate href="{{ route('data.barang.detail', $id_barang) }}"
                       class="btn btn-sm bg-dark text-blue-400 hover:text-blue-500 dark:text-blue-600 hover:bg-gray-700 dark:bg-gray-200  dark:hover:bg-gray-400 ">
                        <span class="iconify solar--eye-bold text-lg"></span> Detail
                        Barang</a>
                @endcan
                @can('barang.edit')
                    <a wire:navigate href="{{ route('data.barang.edit', $id_barang) }}"
                       class="btn btn-sm bg-dark text-yellow-500 hover:text-yellow-600 dark:text-orange-400 hover:dark:text-orange-300 hover:bg-gray-700 dark:bg-gray-200 dark:hover:bg-gray-400"><span
                              class="iconify tabler--edit-circle text-lg"></span> Edit Barang</a>
                @endcan
                @can('barang.detail-faktur')
                    <a wire:navigate href="{{ route('data.barang.detail.faktur', $id_barang) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail
                        Faktur Barang</a>
                @endcan
                @can('barang.detail-stok')
                    <a wire:navigate href="{{ route('data.barang.detail.stok', $id_barang) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail
                        Stok Barang</a>
                @endcan
                @can('barang.detail-barang-expired')
                    <a wire:navigate href="{{ route('data.barang.detail.expired', $id_barang) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail
                        Expired Barang</a>
                @endcan
                @can('barang.detail-transaksi')
                    <a wire:navigate href="{{ route('data.barang.detail.transaksi', $id_barang) }}"
                       class="btn btn-sm bg-dark text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-dark dark:hover:bg-gray-400">Detail Transaksi
                        Barang</a>
                @endcan
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-2">
        {{-- permission jumlah terjual --}}
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
        {{-- permission: total transaksi --}}
        <div class="bg-green-200 dark:bg-green-800/40 p-4 mx-2 my-1 rounded-2xl flex justify-between items-center shadow">
            <div>
                <h3 class="text-gray-500 dark:text-gray-400 font-semibold">Total Transaksi {{ $data->nama ?? '' }} : <span
                          class="text-black dark:text-white font-bold">{{ $shift_transaksi }}</span></h3>
                <h3 class="font-bold text-2xl mb-1">Rp. @nominal($data->total_harga ?? '0')</h3>
                <h6 class="text-gray-700 text-xs"> Periode: {{ $filter_date['start'] }} sampai {{ $filter_date['end'] }}</h6>
            </div>

            <div class="bg-green-400/50 dark:bg-green-300 text-green-800 dark:text-emerald-800 rounded-xl p-3 shadow">
                <span class="iconify solar--hand-money-linear text-2xl font-bold "></span>
            </div>
        </div>
    </div>
    <livewire:transaksi.transaksi-list :$id_barang />
</div>
