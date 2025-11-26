<x-table.action-button color="info" class="[--trigger:focus]">
    <span class="iconify tabler--info-square-rounded-filled"></span>
    <x-slot name="tooltip" class="max-w-xs w-full text-start">
        <div class="p-2 gap-1 text-sm ">
            {{-- <div class="font-normal">
                <span class="font-bold" x-text="'Kategori :'"></span>
                <span x-text="item.barang.kategori.map(kategori => kategori.nama).join(', ')"></span>
            </div> --}}
            {{-- <div class="font-normal">
                <span class="font-bold">Rak : </span>
                <span x-text="item.barang.raks.map(rak => rak.nama).join(', ') ?? 'Tidak ada rak'"></span>
            </div> --}}
            <div class="font-normal">
                <span class="font-bold">Satuan Utama : </span>
                <span x-text="item.barang.satuan_utama.nama"></span>
            </div>
            <div class="font-normal">
                <span class="font-bold">Opsi Satuan : </span>
                <template x-for="(opsi_satuan, index) in item.barang.opsi_satuan" :key="index">
                    <p class="text-xs">-
                        <span x-text="opsi_satuan.pivot.rasio_satuan_opsi"></span>
                        <span x-text="opsi_satuan.nama"></span> =
                        <span x-text="opsi_satuan.pivot.rasio_satuan_utama"></span>
                        <span x-text="item.barang.satuan_utama.nama"></span>
                    </p>
                </template>
                <span x-show="!(item.barang.opsi_satuan?.length)">
                    -
                </span>
            </div>
            <div class="font-normal">
                <span class="font-bold">Harga Utama : Rp. </span>
                <span
                      x-text="formatNumberToString(mode_kasir == 'UMUM' ? item.barang.harga_jual_utama : hargaJualResep(item.barang.harga_jual_utama, persen_resep))"></span>

            </div>
            <div class="font-normal">
                <span class="font-bold">Harga Grosir : </span>
                <template x-if="item.barang.opsi_harga.length > 0">
                    <template x-for="(opsi_harga, index) in item.barang.opsi_harga" :key="index">
                        <div class="flex items-center gap-x-3 text-sm text-gray-800 dark:text-neutral-200">
                            <p class="text-xs">- H.Jual: Rp.
                                <span
                                      x-text="formatNumberToString(mode_kasir == 'UMUM' ? opsi_harga.harga : hargaJualResep(opsi_harga.harga, persen_resep))"></span>
                            </p>
                            <p class="text-xs">Min:
                                <span x-text="formatNumberToString(opsi_harga.jumlah_min)+' / '+opsi_harga.satuan.nama"></span>
                            </p>
                        </div>
                    </template>
                </template>
                <span x-show="!(item.barang.opsi_harga?.length)">
                    -
                </span>
                {{-- @forelse ($barang_pesanan->barang->opsi_harga as $index => $opsi_harga)
                    <br>
                    <div class="inline-flex items-center gap-x-3 text-sm">
                        <p class="text-xs">- H.Jual: Rp.
                            {{ FormatHelper::formatNumberToString($pesanan_kasir->mode_kasir == 'UMUM' ? $opsi_harga->harga : $this->hargaJualResep($opsi_harga->harga, $persen_resep)) }}
                        </p>
                        <p class="text-xs">Min:
                            {{ FormatHelper::formatNumberToString($opsi_harga->jumlah_min) }}/{{ $opsi_harga->satuan->nama }}
                        </p>
                    </div>
                @empty
                    -
                @endforelse --}}

            </div>

        </div>
    </x-slot>
</x-table.action-button>
