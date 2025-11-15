<x-slot:title>Cetak Laporan Faktur</x-slot:title>

<div>
    <h2 class="text-2xl text-center font-bold text-gray-800 mb-2">Laporan Faktur</h2>

    <div class="flex flex-col">

        <p class="text-sm text-gray-600">
            Periode: <span class="font-bold" x-text="formatDate('{{ $created_at['start'] }}')"></span> - <span class="font-bold"
                  x-text="formatDate('{{ $created_at['end'] }}')"></span>
        </p>
        <p class="text-sm text-gray-600">
            Jatuh Tempo:
            @if ($tgl_jatuh_tempo == 'SEMUA')
                <span class="font-bold">SEMUA</span>
            @else
                <span class="font-bold" x-text="formatDate('{{ $tgl_jatuh_tempo['start'] }}')"></span> - <span class="font-bold"
                      x-text="formatDate('{{ $tgl_jatuh_tempo['end'] }}')"></span>
            @endif
        </p>
        <p class="text-sm text-gray-600">
            Status: <span class="font-bold">{{ $status }}</span>
        </p>
        <p class="text-sm text-gray-600">
            Supplier:
            @if ($id_suppliers == 'SEMUA')
                <span class="font-bold">SEMUA</span>
            @else
                <span class="font-bold">
                    {{ $suppliers->pluck('nama')->implode(', ') }}
                </span>
            @endif
        </p>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-2">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <p class="text-xs text-blue-600 font-semibold mb-1">
                JUMLAH FAKTUR
            </p>
            <p class="text-2xl font-bold text-blue-800">@nominal($dataInfo->jumlah_faktur ?? 0)</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <p class="text-xs text-green-600 font-semibold mb-1">
                TOTAL BELANJA
            </p>
            <p class="text-2xl font-bold text-green-800">Rp. @nominal($dataInfo->total_belanja ?? 0)</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <p class="text-xs text-purple-600 font-semibold mb-1">TOTAL BELANJA LUNAS</p>
            <p class="text-2xl font-bold text-purple-800">Rp. @nominal($dataInfo->total_pengeluaran ?? 0)</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <p class="text-xs text-red-600 font-semibold mb-1">TOTAL HUTANG</p>
            <p class="text-2xl font-bold text-red-800">Rp. @nominal($dataInfo->total_hutang ?? 0)</p>
        </div>
    </div>

    @foreach ($data as $faktur)
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-4 hover:shadow-xl">
            <div class="py-2 px-5  bg-gray-800 text-white">
                <div class="flex flex-wrap gap-4 justify-between items-center print:flex-nowrap">
                    <div class="flex items-center gap-2">
                        <i class="iconify hugeicons--file-02 text-xl text-blue-500"></i>
                        <div>
                            <p class="font-bold text-lg">{{ $faktur->no_faktur }}</p>
                            <p class="text-sm text-gray-300">Masuk : <span x-text="formatDate('{{ $faktur->created_at }}',true,false,'full','short')"></span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <i class="text-purple-600 iconify mdi--person-badge-outline text-2xl"></i>
                        <div>
                            <p class="font-semibold mb-0.5">{{ $faktur->nama_supplier }}</p>
                            <div class="rounded-lg p-1 bg-gray-700">
                                <p class="text-slate-200 text-xs">Total Belanja : <span class="text-sm font-bold text-white">Rp. @nominal($faktur->total_biaya)</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center ">
                        <div class="flex flex-col gap-2">
                            <div class="text-end">
                                <span
                                      class="px-3 py-1 rounded-full text-xs font-semibold {{ $faktur->status == 'LUNAS' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} ">{{ $faktur->status }}</span>
                            </div>
                            @if ($faktur->status == 'BELUM LUNAS')
                                <div class="rounded-lg p-1 bg-gray-700">
                                    <p class="text-slate-200 text-xs">Jatuh Tempo : <span class="text-sm font-bold text-red-500"
                                              x-text="formatDate('{{ $faktur->tgl_jatuh_tempo }}',false,false,'full','short')"></span></p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Items -->
            <div class=" border-t border-gray-200">

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Nama Barang</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold">Qty</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold">Tgl Expired</th>
                                {{-- <th class="px-4 py-3 text-right text-sm font-semibold">Harga</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold">Subtotal</th> --}}

                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($faktur->detail_faktur as $detail_faktur)
                                <tr class="hover:bg-gray-50 text-dark font-semibold">
                                    <td class="px-4 py-3 text-sm">{{ $detail_faktur->detail_barang->nama_barang }}</td>
                                    <td class="px-4 py-3 text-sm text-center ">{{ $detail_faktur->detail_barang->jumlah }}
                                        {{ $detail_faktur->detail_barang->nama_satuan }}</td>
                                    {{-- <td class="px-4 py-3 text-sm text-right ">Rp 5.500.000</td> --}}
                                    <td class="px-4 py-3 text-sm text-center"
                                        x-text="formatDate('{{ $detail_faktur->detail_barang->tgl_expired[0]['tgl'] }}',false,false,'full','short')">
                                    </td>
                                    {{-- <td class="px-4 py-3 text-sm text-end">
                                        @php
                                            $rasio =
                                                $detail_faktur->detail_barang->barang->satuan_opsi->firstWhere(
                                                    'id_satuan',
                                                    $detail_faktur->detail_barang->id_satuan,
                                                )['rasio'] ?? null;
                                            if ($detail_faktur->harga_beli == null) {
                                                if ($rasio) {
                                                    $harga = $detail_faktur->detail_barang->harga_beli_default * $rasio;
                                                } else {
                                                    $harga = $detail_faktur->detail_barang->harga_beli_default;
                                                }
                                            } else {
                                                $harga = $detail_faktur->detail_barang->barang->harga_jual;
                                            }
                                        @endphp
                                        {{ $harga }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-end">
                                        {{ $detail_faktur->detail_barang->jumlah * $harga }}
                                    </td> --}}

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @endforeach

</div>
