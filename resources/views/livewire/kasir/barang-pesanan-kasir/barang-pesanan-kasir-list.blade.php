@php
    use App\Helpers\FormatHelper;
@endphp

@script
    <script>
        Alpine.data('barangPesananKasirList', () => ({
            isEditBarang: {},
            editPesananBarang(id_barang_pesanan) {
                this.isEditBarang[id_barang_pesanan] = true;
                $wire.dispatch('focus-edit-jumlah-' + id_barang_pesanan);
            },
            async updatePesananBarang(id_barang_pesanan) {
                await $wire.updateJumlahBarang(id_barang_pesanan)
                    .then(($response) => {
                        if($response){
                            this.isEditBarang[id_barang_pesanan] = false;
                        }
                    })
            },
        }))
    </script>
@endscript

<div class="card" x-data="barangPesananKasirList">
    <div class="card-body p-2">
        <div>
            <button class="btn btn-sm bg-error hover:bg-erroremphasis w-fit" wire:click='deleteAllPesananBarang'>Hapus Semua</button>
            @if (!empty($ceklistDeleteBarang))
                <button class="btn btn-sm bg-error hover:bg-erroremphasis w-fit" wire:click='deleteWithSelectedItem'>Hapus yang Dipilih</button>
            @endif
            @error('barang_pesanan.*')
                <p class="text-red-500 text-sm">* {{ $message }}</p>
            @enderror
        </div>
        <x-table nama="barang-pesanan-kasir" height="h-96 max-h-96 w-auto">
            <x-table.thead>
                <x-table.th label="#" />
                <x-table.th label="No" />
                <x-table.th label="Nama Barang" />
                <x-table.th label="Harga" />
                <x-table.th label="Jumlah" />

                <x-table.th label="Total" />
                <x-table.th label="Aksi" />
            </x-table.thead>
            <x-table.tbody>
                @forelse ($barang_pesanan_kasir as $index => $barang_pesanan)
                    {{-- @php
                        $next = $barang_pesanan_kasir[$index + 1] ?? null;
                        if (!$next || $barang_pesanan->id_barang != $next->id_barang) {
                            $promo_barang = $pesanan_kasir
                                ->promo_pesanan_kasir()
                                ->whereHas('promo_barang', function ($q) use ($barang_pesanan) {
                                    $q->where('id_barang', $barang_pesanan->id_barang);
                                })
                                ->first();
                        }
                    @endphp --}}
                    <x-table.tr class="{{ $barang_pesanan->promo_deskripsi ? '' : ($loop->last ? 'h-full' : '') }} {{ $errors->has('barang_pesanan.' . $barang_pesanan->id) ? 'bg-red-400/80' : '' }}">
                        <x-table.td class="text-center">
                            <input id="hs-table-checkbox-1" wire:model.live="ceklistDeleteBarang" value="{{ $barang_pesanan->id }}" type="checkbox"
                                   class="border-gray-400 w-4 h-4 rounded text-primary focus:ring-primary dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-gray-800">
                            <label for="hs-table-checkbox-1" class="sr-only">Checkbox</label>
                        </x-table.td>
                        <x-table.td class="text-center">{{ $index + 1 }}</x-table.td>
                        <x-table.td class="font-bold">{{ $barang_pesanan->barang->nama }}
                            [{{ FormatHelper::formatNumberToString($barang_pesanan->barang->stok) }}/{{ $barang_pesanan->barang->satuan_utama->nama }}]</x-table.td>
                        <x-table.td class="min-w-24">
                            @if ($barang_pesanan->id_satuan != $barang_pesanan->barang->id_satuan_utama)
                                <div class="text-sm">
                                    Rp. {{ FormatHelper::formatNumberToString($barang_pesanan->harga) }}
                                    / {{ $barang_pesanan->satuan->nama }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    &asymp; Rp. {{ FormatHelper::formatNumberToString($barang_pesanan->harga_satuan_utama) }}
                                    / {{ $barang_pesanan->barang->satuan_utama->nama }}
                                </div>
                            @else
                                <div class="inline-flex text-sm">
                                    <h6>Rp. {{ FormatHelper::formatNumberToString($barang_pesanan->harga_satuan_utama) }}
                                        / {{ $barang_pesanan->barang->satuan_utama->nama }}</h6>
                                </div>
                            @endif
                        </x-table.td>
                        <x-table.td class="min-w-44">
                            <div @focus-edit-jumlah-{{ $barang_pesanan->id }}.window="$focus.focus($refs.edit_jumlah_{{ $barang_pesanan->id }});">
                                <form x-show="isEditBarang['{{ $barang_pesanan->id }}']" @submit.prevent="updatePesananBarang('{{ $barang_pesanan->id }}')">
                                    <x-form.input x-ref="edit_jumlah_{{ $barang_pesanan->id }}" value="{{ $barang_pesanan->jumlah }}"
                                                  wire:model="jumlah_barang_pesanan.{{ $barang_pesanan->id }}" onkeyup="formatForInputNumber(this)"
                                                  errorName="jumlah_barang_pesanan.{{ $barang_pesanan->id }}" placeholder="Masukan Jumlah"
                                                  labelInputGroup="/{{ $barang_pesanan->satuan->nama }}" inputGroup="right" />
                                </form>
                                <h6 x-show="!isEditBarang['{{ $barang_pesanan->id }}']" class="text-center">{{ $barang_pesanan->jumlah }} /
                                    {{ $barang_pesanan->satuan->nama }}</h6>
                            </div>
                        </x-table.td>
                        <x-table.td class="text-center min-w-24">
                            Rp. {{ FormatHelper::formatNumberToString($barang_pesanan->sub_total) }}
                        </x-table.td>
                        <x-table.td class="text-center flex items-center justify-center gap-x-1 min-w-24">
                            {{-- @include('kasir.barang-pesanan-kasir.components.badge-tooltip-info-barang') --}}
                            <x-table.action-button tooltip="List Diskon Barang" color="info" x-show="!isEditBarang['{{ $barang_pesanan->id }}']"
                                                   wire:click="showListDiskonBarang('{{ $barang_pesanan->id }}')">
                                <span class="text-xs">Diskon</span>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Simpan" color="success" x-show="isEditBarang['{{ $barang_pesanan->id }}']"
                                                   @click="updatePesananBarang('{{ $barang_pesanan->id }}')">
                                <i class="ti ti-check"></i>
                            </x-table.action-button>

                            <x-table.action-button tooltip="Edit" color="warning" x-show="!isEditBarang['{{ $barang_pesanan->id }}']"
                                                   @click="editPesananBarang('{{ $barang_pesanan->id }}')">
                                <i class="ti ti-edit"></i>
                            </x-table.action-button>

                            <x-table.action-button tooltip="Hapus" color="error"
                                                   wire:click="$dispatch('alert-confirm', { id: '{{ $barang_pesanan->id }}', method: 'delete-barang-pesanan-kasir' });">
                                <i class="ti ti-trash-x-filled"></i>
                            </x-table.action-button>
                        </x-table.td>

                    </x-table.tr>
                    @if ($barang_pesanan->promo_deskripsi)
                        <x-table.tr-empty class="ps-4 text-start align-top" colspan="8">
                            <x-slot:content>
                                **Promo:** {{ $barang_pesanan->promo_deskripsi }}
                            </x-slot:content>
                        </x-table.tr-empty>
                        @if ($loop->last)
                            <x-table.tr-empty class="h-full" colspan="8" content="" />
                        @endif
                    @endif
                @empty
                    <x-table.tr-empty colspan="8" />
                @endforelse
                {{-- <x-table.loading-spin /> --}}
            </x-table.tbody>
        </x-table>
    </div>
</div>
