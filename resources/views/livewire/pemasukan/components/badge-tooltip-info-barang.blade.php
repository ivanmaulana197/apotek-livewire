@php
    use App\Helpers\FormatHelper;
@endphp
<x-table.action-button color="info">
    <span class="iconify tabler--info-square-rounded-filled"></span>
    <x-slot name="tooltip" class="max-w-xs w-full text-start">
        <div class="p-2 gap-1 text-sm ">
            <div class="font-normal">
                <span class="font-bold">Kategori
                    :</span>
                {{ $barang_pesanan->barang->kategori->nama }}
            </div>
            <div class="font-normal">
                <span class="font-bold">Rak</span> :
                @if ($barang_pesanan->barang->raks->isNotEmpty())
                    @php
                        $rakNames = $barang_pesanan->barang->raks->pluck('nama')->toArray();
                    @endphp
                    @if (count($rakNames) === 2)
                        {{ implode(' dan ', $rakNames) }}
                    @else
                        {{ implode(', ', array_slice($rakNames, 0, -1)) }}{{ count($rakNames) > 1 ? ', dan ' : '' }}{{ end($rakNames) }}
                    @endif
                @else
                    <span>Tidak ada rak</span>
                @endif
            </div>
            <div class="font-normal">
                <span class="font-bold">Satuan Utama </span> : {{ $barang_pesanan->barang->satuan_utama->nama }}
            </div>
            <div class="font-normal">
                <span class="font-bold">Opsi Satuan :</span>
                @forelse ($barang_pesanan->barang->opsi_satuan as $index => $opsi_satuan)
                    <p class="text-xs">-
                        {{ $opsi_satuan->pivot->rasio_satuan_opsi }}
                        {{ $opsi_satuan->nama }} =
                        {{ $opsi_satuan->pivot->rasio_satuan_utama }}
                        {{ $barang_pesanan->barang->satuan_utama->nama }}</p>

                @empty
                    -
                @endforelse
            </div>
            <div class="font-normal">
                <span class="font-bold">Harga Utama : </span> Rp.
                {{ FormatHelper::formatNumberToString($pesanan_kasir->mode_kasir == 'UMUM' ? $barang_pesanan->barang->harga_jual_utama : $this->hargaJualResep($barang_pesanan->barang->harga_jual_utama, $persen_resep)) }}
            </div>
            <div class="font-normal">
                <span class="font-bold">Harga Grosir : </span>
                @forelse ($barang_pesanan->barang->opsi_harga as $index => $opsi_harga)
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
                @endforelse

            </div>

        </div>
    </x-slot>
</x-table.action-button>
