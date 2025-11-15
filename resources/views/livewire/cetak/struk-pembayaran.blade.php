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

 <div class="text-dark">
     <div class="max-w-sm font-mono ">
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
                 {{-- <thead>
                     <tr>
                         <th class="font-bold" style="width: 45%">Obat</th>
                         <th class="text-center font-bold" style="width: 5%">Qty</th>
                         <th class="text-right font-bold" style="width: 18%">Harga</th>
                         <th class="text-right font-bold" style="width: 32%">Sub Total</th>
                     </tr>
                 </thead> --}}
                 <tbody>
                     @foreach ($transaksi->detail_transaksi as $index => $detail_transaksi)
                         <tr>
                             <td colspan="4"><span class="font-semibold">[{{ $index + 1 }}]</span> {{ $detail_transaksi->nama_barang }}
                                 [{{ $detail_transaksi->nama_satuan }}]</td>
                         </tr>
                         <tr>
                             <td class="text-center">@nominal($detail_transaksi->jumlah) x</td>
                             <td colspan="2" class="text-left">Rp. @nominal($detail_transaksi->harga)</td>
                             <td class="text-right w-[30%]">Rp. @nominal($detail_transaksi->total_harga)</td>
                         </tr>
                         @if ($detail_transaksi->promo_barang)
                             <tr>
                                 <td colspan="3" class="text-left font-bold">**Promo {{ $detail_transaksi->promo_barang->barang->nama }}</td>
                                 <td class="text-right font-bold w-[30%]">Rp. - @nominal($detail_transaksi->promo_barang->nominal_promo)
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
                     @php
                         $potongan_transaksi = $transaksi->transaksi_list_promo->where('tipe_promo', 'TRANSAKSI')->sum('nominal_promo');
                     @endphp
                     <span>Potongan</span>
                     <span>Rp.
                         @nominal($potongan_transaksi)
                     </span>
                 </div>
             @endif
             @if ($transaksi->potongan_diskon)
                 <div class="flex justify-between">
                     <span>Total Potongan</span>
                     <span>Rp. @nominal($transaksi->potongan_diskon)</span>
                 </div>
             @endif
             <div class="flex justify-between">
                 <span>Total</span>
                 <span>Rp. @nominal($transaksi->total_bayar)</span>
             </div>
             <div class="flex justify-between">
                 <span>Bayar</span>
                 <span>Rp. @nominal($transaksi->bayar)</span>
             </div>
             <div class="flex justify-between font-semibold">
                 <span>Kembali</span>
                 <span>Rp. @nominal($transaksi->kembalian)</span>
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
