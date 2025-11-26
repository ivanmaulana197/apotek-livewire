<button type="button" class="btn btn-sm w-full bg-info hs-tooltip hover:bg-infoemphasis ">
    <span class="text-xl iconify tabler--info-square-rounded"></span>
    <span class="text-xs">Grosir</span>

    <div class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible hidden opacity-0 transition-opacity absolute invisible z-[61] max-w-xs w-full border border-gray-600 text-start rounded-md shadow-md after:absolute after:top-0 after:-start-4 after:w-4 after:h-full bg-gray-100 dark:bg-gray-900 dark:border-gray-600"
         role="tooltip">

        <template x-if="data_barang">
            <ul class="py-3 px-4 space-y-1">
                <li>
                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                        <p class="text-xs">Harga Utama : Rp.
                            <span class="font-bold"
                                  x-text="formatNumberToString(mode_kasir == 'UMUM' ? data_barang.harga_jual_utama : hargaJualResep(data_barang.harga_jual_utama, persen_resep))"></span>
                        </p>
                    </div>
                </li>
                <template x-if="data_barang.opsi_harga.length > 0">
                    <template x-for="(opsi_harga, index) in data_barang.opsi_harga" :key="index">
                        <li class="flex items-center gap-x-3 text-sm text-gray-800 dark:text-neutral-200">
                            -
                            <p class="text-xs">H.Jual: Rp.
                                <span
                                      x-text="formatNumberToString(mode_kasir == 'UMUM' ? opsi_harga.harga : hargaJualResep(opsi_harga.harga, persen_resep))"></span>
                            </p>
                            <p class="text-xs">Min:
                                <span x-text="formatNumberToString(opsi_harga.jumlah_min)+' / '+opsi_harga.satuan.nama"></span>
                            </p>
                        </li>
                    </template>
                </template>

                <template x-if="data_barang.opsi_harga.length === 0">
                    <li class="flex items-center justify-center gap-x-3 text-xs font-semibold text-gray-800 dark:text-neutral-200">
                        Tidak ada harga grosir
                    </li>
                </template>

            </ul>
        </template>

        <template x-if="!data_barang">
            <ul class="py-3 px-4 space-y-1">
                <li class="flex items-center justify-center gap-x-3 text-sm text-gray-800 dark:text-neutral-200">
                    Pilih Barang Terlebih Dahulu
                </li>
            </ul>
        </template>

    </div>
    <!-- End Popover Content -->
</button>
