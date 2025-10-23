@php

@endphp
<button type="button"
    class="btn btn-sm bg-info hs-tooltip hover:bg-infoemphasis">
    <i class="text-xl ti ti-info-square-rounded"></i>
    <span class="text-xs">Satuan</span>
    <div class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible hidden opacity-0 transition-opacity absolute invisible z-[61] max-w-xs w-full border bg-gray-100 border-gray-400 text-start rounded-md shadow-md after:absolute after:top-0 after:-start-4 after:w-4 after:h-full  dark:bg-gray-900 dark:border-gray-600"
        role="tooltip">
        <ul class="p-2">
            @if ($data_barang)
                <li>
                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                        <p class="text-xs">Satuan Utama: <span class="font-bold">{{ $data_barang->satuan_utama->nama }}</span></p>
                    </div>
                </li>
                @foreach ($data_barang->opsi_satuan as $index => $opsi_satuan)
                    <li>
                        <div class="text-sm text-gray-800 dark:text-neutral-200">
                            <p class="text-xs">-
                                {{ $opsi_satuan->pivot->rasio_satuan_opsi }}
                                {{ $opsi_satuan->nama }} =
                                {{ $opsi_satuan->pivot->rasio_satuan_utama }}
                                {{ $data_barang->satuan_utama->nama }}</p>
                        </div>
                    </li>
                @endforeach
            @else
                <li>
                    <div class="flex text-center justify-center gap-x-3 text-sm text-gray-800 dark:text-neutral-200">
                        Pilih Barang Terlebih Dahulu
                    </div>
                </li>
            @endif

        </ul>

    </div>
    <!-- End Popover Content -->
</button>
