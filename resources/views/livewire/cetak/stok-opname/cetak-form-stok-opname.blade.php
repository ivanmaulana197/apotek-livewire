<x-slot:title>Cetak Form Stok Opname</x-slot:title>

@script
    <script>
        Alpine.data('cetakStokOpname', () => ({
            tgl_stok_opname: formatDate(moment()),
        }))
    </script>
@endscript

<div class="text-sm text-dark" x-data="cetakStokOpname">
    <h3 class="text-lg font-semibold mb-4">FORM DATA STOK OPNAME</h3>

    <table class="mb-4">
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
                    <th class="border px-2 py-1 w-[38%]">Nama Barang</th>
                    {{-- <th class="border px-2 py-1 w-[25%]">Rak</th> --}}
                    {{-- <th class="border px-2 py-1">Harga Beli</th>
                    <th class="border px-2 py-1">Harga Jual</th>
                    <th class="border px-2 py-1">Stok Awal</th> --}}
                    <th class="border px-2 py-1">Stok Aplikasi</th>
                    <th class="border px-2 py-1 w-[20%]">Stok Toko</th>
                    {{-- <th class="border px-2 py-1">Selisih</th>
                    <th class="border px-2 py-1 w-[28%]">Kerugian</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($this->stokOpnames as $index => $data)
                    <tr>
                        <td class="border px-2 py-1 text-center">{{ $index + 1 }}</td>
                        <td class="border px-2 py-1">{{ $data->nama }} [{{ $data->nama_satuan_utama }}]</td>
                        {{-- <td class="border px-2 py-1">{{ $data->raks->pluck('nama')->implode(', ') }}</td> --}}
                        {{-- <td class="border px-2 py-1">{{ $satuan }}</td> --}}
                        {{-- <td class="border px-2 py-1">{{ $kategori }}</td> --}}
                        {{-- <td class="border px-2 py-1 text-right">{{ nominal($data->HARGA_BELI) }}</td>
                        <td class="border px-2 py-1 text-right">{{ nominal($data->HARGA_JUAL) }}</td>
                        <td class="border px-2 py-1 text-center">{{ $data->STOK_AWAL }}</td> --}}
                        <td class="border px-2 py-1 text-center">@nominal($data->stok)</td>
                        <td class="border px-2 py-1 text-center"></td>
                        {{-- <td class="border px-2 py-1 text-center">@nominal($data->stok_real - $data->stok)</td> --}}
                        {{-- <td class="border px-2 py-1 text-right">Rp. @nominal($data->kerugian)</td> --}}
                    </tr>
                @endforeach
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
