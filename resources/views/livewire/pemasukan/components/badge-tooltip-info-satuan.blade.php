@php

@endphp
<button type="button" class="btn btn-sm bg-info hs-tooltip hover:bg-infoemphasis">
    <span class="text-xl iconify tabler--info-square-rounded"></span>
    <span class="text-xs">Satuan</span>
    <div class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible hidden opacity-0 transition-opacity absolute invisible z-[61] max-w-xs w-full border bg-gray-100 border-gray-400 text-start rounded-md shadow-md after:absolute after:top-0 after:-start-4 after:w-4 after:h-full  dark:bg-gray-900 dark:border-gray-600"
         role="tooltip">
        <template x-if="data_barang">
            <ul class="p-2">
                <li>
                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                        <p class="text-xs">Satuan Utama: <span class="font-bold" x-text="data_barang.satuan_utama.nama"></span></p>
                    </div>
                </li>
                <template x-for="(opsi_satuan, index) in data_barang.opsi_satuan" :key="index">
                    <li>
                        <div class="text-sm text-gray-800 dark:text-neutral-200">
                            <p class="text-xs">-
                                <span class="font-bold" x-text="opsi_satuan.pivot.rasio_satuan_opsi"></span>
                                <span class="font-bold" x-text="opsi_satuan.nama"></span> =
                                <span class="font-bold" x-text="opsi_satuan.pivot.rasio_satuan_utama"></span>
                                <span class="font-bold" x-text="data_barang.satuan_utama.nama"></span>
                            </p>
                        </div>
                    </li>
                </template>
            </ul>
        </template>
        <template x-if="!data_barang">
            <ul class="p-2">
                <li>
                    <div class="flex text-center justify-center gap-x-3 text-sm text-gray-800 dark:text-neutral-200">
                        Pilih Barang Terlebih Dahulu
                    </div>
                </li>
            </ul>
        </template>

    </div>
    <!-- End Popover Content -->
</button>
