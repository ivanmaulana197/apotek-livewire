<x-slot:title>Cetak Transaksi </x-slot:title>
@php
    use App\Helpers\FormatHelper;
@endphp

<script>
    window.onload = () => window.print(); // cetak otomatis saat terbuka
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            window.close();
        }
    });
</script>

<style>
    .text-important {
        font-size: 1rem;
    }
</style>

<div>
    <div class="max-w-sm font-mono">
        <div class="mb-2">
            <h1 class="text-xl font-bold">{{ $setting->nama }}</h1>
            <p class="text-xs">{{ $setting->alamat }}</p>
            <p class="text-important flex justify-between">Telp: <span class="font-bold">{{ $setting->no_telp }}</span></p>
            <p class="text-important flex justify-between">Tanggal: <span class="font-bold">{{ $transaksi->created_at }}</span></p>
            <p class="text-important flex justify-between">No. Struk: <span class="font-bold">{{ $transaksi->no_transaksi }}</span></p>
        </div>
        <hr class="my-2 border-gray-800 border-dashed">

        <div class="mb-2">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="font-bold" style="width: 45%">Obat</th>
                        <th class="text-center font-bold" style="width: 5%">Qty</th>
                        <th class="text-right font-bold" style="width: 18%">Harga</th>
                        <th class="text-right font-bold" style="width: 32%">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksi->transaksi_barang as $transaksi_barang)
                        <tr>
                            <td>{{ $transaksi_barang->barang->nama }} [{{ $transaksi_barang->satuan->nama }}]</td>
                            <td class="text-center">{{ FormatHelper::formatNumberToString($transaksi_barang->jumlah) }}</td>
                            <td class="text-right">{{ FormatHelper::formatNumberToString($transaksi_barang->detail_transaksi->harga) }}</td>
                            <td class="text-right">Rp. {{ FormatHelper::formatNumberToString($transaksi_barang->detail_transaksi->total_harga) }}</td>
                        </tr>
                        @if ($transaksi_barang->promo_barang)
                            <tr>
                                <td colspan="3" class="text-left font-bold">Promo {{ $transaksi_barang->promo_barang->barang->nama }}</td>
                                <td class="text-right font-bold">Rp. -{{ FormatHelper::formatNumberToString($transaksi_barang->promo_barang->nominal_promo) }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr class="my-2 border-gray-800 border-dashed">

        <div class="mb-2">
            @if ($transaksi->transaksi_list_promo->where('tipe_promo', 'TRANSAKSI')->count() > 0)
                <div class="flex justify-between">
                    <span>Potongan</span>
                    <span>Rp.
                        {{ FormatHelper::formatNumberToString($transaksi->transaksi_list_promo->where('tipe_promo', 'TRANSAKSI')->sum('nominal_promo')) }}</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span>Total</span>
                <span>Rp. {{ FormatHelper::formatNumberToString($transaksi->total_bayar) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Bayar</span>
                <span>Rp. {{ FormatHelper::formatNumberToString($transaksi->bayar) }}</span>
            </div>
            <div class="flex justify-between font-semibold">
                <span>Kembali</span>
                <span>Rp. {{ FormatHelper::formatNumberToString($transaksi->kembalian) }}</span>
            </div>
            @if ($transaksi->transaksi_list_promo->where('tipe_promo', 'BARANG BONUS')->count() > 0)
                <div class="flex flex-col font-semibold">
                    <span>*Promo</span>
                    @foreach ($transaksi->transaksi_list_promo->where('tipe_promo', 'BARANG BONUS') as $index => $transaksi_list_promo)
                        <span>({{ $index + 1 }})
                            {{ $transaksi_list_promo->deskripsi }}</span>
                    @endforeach
                    </span>
                </div>
            @endif
        </div>

        <hr class="my-2 border-gray-800 border-dashed">

        <p class="text-center text-xs italic">Terima kasih atas kunjungan Anda</p>
    </div>
</div>
