<x-slot:title>Cetak List Transaksi</x-slot:title>

@script
    <script>
        Alpine.data('cetakStokOpname', () => ({
            tgl_stok_opname: formatDate('{{ $created_at }}', true),
            datetime_cetak: formatDate(moment(), true),
            tgl_cetak: formatDate(moment()),
        }))
    </script>
@endscript

<div class="text-sm text-dark" x-data="cetakStokOpname">
    <h3 class="text-lg font-semibold mb-4">LAPORAN DATA STOK OPNAME</h3>

    <table class="mb-4">
        <tr>
            <td>Dicetak Tanggal</td>
            <td class="px-2">:</td>
            <td><span class="font-semibold" x-text="datetime_cetak"></span></td>
        </tr>
        <tr>
            <td>Stok Opname Tanggal</td>
            <td class="px-2">:</td>
            <td x-text="tgl_stok_opname"></td>
        </tr>
    </table>

    <div class="overflow-x-auto">
        <table class="min-w-full border text-xs">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-2 py-1 w-[7%]">No</th>
                    <th class="border px-2 py-1 w-[35%]">Nama Barang</th>
                    {{-- <th class="border px-2 py-1">Kategori</th> --}}
                    {{-- <th class="border px-2 py-1">Harga Beli</th>
                    <th class="border px-2 py-1">Harga Jual</th>
                    <th class="border px-2 py-1">Stok Awal</th> --}}
                    <th class="border px-2 py-1">Stok Aplikasi</th>
                    <th class="border px-2 py-1">Stok Toko</th>
                    <th class="border px-2 py-1">Selisih</th>
                    <th class="border px-2 py-1 w-[28%]">Kerugian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stokOpnames as $index => $data)
                    <tr>
                        <td class="border px-2 py-1 text-center">{{ $index + 1 }}</td>
                        <td class="border px-2 py-1">{{ $data->nama }} [{{ $data->satuan_utama_nama }}]</td>
                        {{-- <td class="border px-2 py-1">{{ $satuan }}</td> --}}
                        {{-- <td class="border px-2 py-1">{{ $kategori }}</td> --}}
                        {{-- <td class="border px-2 py-1 text-right">{{ nominal($data->HARGA_BELI) }}</td>
                        <td class="border px-2 py-1 text-right">{{ nominal($data->HARGA_JUAL) }}</td>
                        <td class="border px-2 py-1 text-center">{{ $data->STOK_AWAL }}</td> --}}
                        <td class="border px-2 py-1 text-center">@nominal($data->stok)</td>
                        <td class="border px-2 py-1 text-center">@nominal($data->stok_real)</td>
                        <td class="border px-2 py-1 text-center">@nominal($data->stok_real - $data->stok)</td>
                        <td class="border px-2 py-1 text-right">Rp. @nominal($data->kerugian)</td>
                    </tr>
                @endforeach

                <tr class="font-semibold bg-gray-100">
                    <td colspan="5" class="text-center border px-2 py-1">TOTAL KEUNTUNGAN</td>
                    <td class="border px-2 py-1 text-right">Rp @nominal($stokOpnames->sum('kerugian'))</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-10">
        <p>{{ $kontak->KABUPATEN ?? 'Kota' }}, <span x-text="tgl_cetak"></span></p>
        <p>Admin,</p>
        <br><br>
        <p><strong><u>{{ $admin->nama ?? '' }}</u></strong></p>
    </div>
</div>
