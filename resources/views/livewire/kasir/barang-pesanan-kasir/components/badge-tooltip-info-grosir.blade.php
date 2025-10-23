@php
    use App\Helpers\FormatHelper;
@endphp
<button type="button"
    class="btn btn-sm w-full bg-info hs-tooltip hover:bg-infoemphasis">
    <i class="text-xl ti ti-info-square-rounded"></i>
    <span class="text-xs">Grosir</span>

    <div class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible hidden opacity-0 transition-opacity absolute invisible z-[61] max-w-xs w-full border border-gray-600 text-start rounded-md shadow-md after:absolute after:top-0 after:-start-4 after:w-4 after:h-full bg-gray-100 dark:bg-gray-900 dark:border-gray-600"
        role="tooltip">
        <ul class="py-3 px-4 space-y-1">
            @if ($data_barang)
                <li>
                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                        <p class="text-xs">Harga Utama : Rp. <span class="font-bold">{{ FormatHelper::formatNumberToString(($pesanan_kasir->mode_kasir == "UMUM") ? $data_barang->harga_jual_utama:$this->hargaJualResep($data_barang->harga_jual_utama,$persen_resep)) }}</span></p>
                    </div>
                </li>
                @forelse ($data_barang->opsi_harga as $index => $opsi_harga)
                    <li>
                        <div class="inline-flex items-center gap-x-3 text-sm text-gray-800 dark:text-neutral-200">
                            -
                            <p class="text-xs">H.Jual: Rp.
                                {{ FormatHelper::formatNumberToString(($pesanan_kasir->mode_kasir == "UMUM") ? $opsi_harga->harga:$this->hargaJualResep($opsi_harga->harga,$persen_resep)) }}
                            </p>
                            <p class="text-xs">Min:
                                {{ FormatHelper::formatNumberToString($opsi_harga->jumlah_min) }}/{{ $opsi_harga->satuan->nama }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li>
                        <div class="flex items-center justify-center gap-x-3 text-sm text-gray-800 dark:text-neutral-200">
                            Tidak ada harga grosir
                        </div>
                    </li>
                @endforelse
            @else
                <li>
                    <div class="flex items-center justify-center gap-x-3 text-sm text-gray-800 dark:text-neutral-200">
                        Pilih Barang Terlebih Dahulu
                    </div>
                </li>
            @endif

        </ul>

    </div>
    <!-- End Popover Content -->
</button>
