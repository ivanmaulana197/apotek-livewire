<div class="card" x-data>
    <div class="card-body gap-0">
        <div class="card-title flex flex-wrap justify-between">
            <h6 class="text-xl font-bold">{{ $title ?? 'List Transaksi' }}</h6>
            @if (!$id_dokter && !$id_member)
                @canany(['transaksi.cetak-laporan-all', 'transaksi.cetak-laporan-own'])
                    <button @click="
                        let url = '';
                        @if ($id_barang) url = '{{ route('cetak.transaksi-barang', $id_barang) }}';
                        @else
                            url = '{{ route('cetak.laporan-transaksi') }}'; @endif

                        url = url + '?date[start]={{ $filter_date['start'] }}'
                                + '&date[end]={{ $filter_date['end'] }}'
                                + '&shift={{ $shift_transaksi }}'

                        window.open(url, '_blank');
                    "
                            class="btn btn-sm btn-primary">Cetak Transaksi</button>
                @endcanany
            @endif
        </div>

        @if ($id_dokter || $id_member)
            <div class="my-2">
                <x-datatable nama_table="transaksi" :perPage="$perPage" placeholder_search="Masukan No Transaksi" />
            </div>
        @else
            @php
                $cek_shift = now()->hour < 16;
                $cek = !auth()->user()->can('transaksi.filter-transaksi') && $cek_shift;
            @endphp
            <x-datatable nama_table="transaksi" :perPage="$perPage" placeholder_search="Masukan No Transaksi">
                @if (auth()->user()->can('transaksi.filter-transaksi') || $cek_shift)
                    <div class="w-svw sm:w-56" wire:ignore>
                        <x-form.select name="shift_transaksi" :direction=false :empty-option=false wire:model.live='shift_transaksi'>
                            <x-slot:custom-option>
                                <option value="PAGI" {{ $shift_transaksi == 'PAGI' ? 'selected' : '' }}>PAGI</option>
                                <option value="MALAM" {{ $shift_transaksi == 'MALAM' ? 'selected' : '' }}>MALAM</option>
                                <option value="SEMUA" {{ $shift_transaksi == 'SEMUA' ? 'selected' : '' }}>SEMUA</option>
                            </x-slot:custom-option>
                        </x-form.select>
                    </div>
                @endif
                @can('transaksi.filter-transaksi')
                    <div class="w-svw sm:w-80" wire:ignore>
                        <x-form.input-date :bigRange="$big_range_date" nama="filter_date" model="filter_date" :select_date='$filter_date'></x-form.input-date>
                    </div>
                @endcan
            </x-datatable>
        @endif

        <x-table nama="transaksi">
            <x-table.thead>
                <x-table.th label="No" />
                <x-table.th label='Aksi' />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Tgl Data" name="transaksi.created_at" />
                <x-table.th label="No Transaksi" />
                <x-table.th label="Nama Pembeli" />
                <x-table.th label="Sub Total" />
                <x-table.th label="Metode Pembayaran" />
                <x-table.th label="Potongan Diskon" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Total Bayar" name="transaksi.total_bayar" />
                <x-table.th label="Pembulatan" />
                <x-table.th label="Shift Transaksi" />
            </x-table.thead>

            <x-table.tbody>
                @forelse ($transaksis as $index => $transaksi)
                    <x-table.tr wire:key="table-transaksi-{{ $transaksi->id }}">
                        <x-table.td class="text-center">{{ $transaksis->firstItem() + $index }}</x-table.td>
                        <x-table.td class="text-center min-w-28">
                            <x-dropdown text="Aksi" wire:key='dropdown-{{ $transaksi->id }}'>
                                @canany(['cetak.struk-pembayaran-own', 'cetak.struk-pembayaran-all'])
                                    <x-dropdown.item href="{{ route('cetak.struk-pembayaran', ['id_transaksi' => $transaksi->id]) }}"
                                                     onclick="window.open(this.href,'_blank','width=800,height=700'); return false;">
                                        <span class="iconify tabler--printer text-lg"></span>
                                        Cetak Struk
                                    </x-dropdown.item>
                                @endcanany
                                @can('transaksi.retur')
                                    <x-dropdown.item href="#"
                                                     @click="$wire.dispatch('alert-confirm', { id: '{{ $transaksi->id }}', method: 'retur-transaksi' });">
                                        <span class="iconify tabler--truck-return text-lg"></span>
                                        Retur
                                    </x-dropdown.item>
                                @endcan
                                @canany(['transaksi.detail-all', 'transaksi.detail-own'])
                                    <x-dropdown.item href="{{ route('transaksi.detail', ['id_transaksi' => $transaksi->id]) }}" wire:navigate>
                                        <span class="iconify tabler--eye-search text-lg"></span>
                                        Detail
                                    </x-dropdown.item>
                                @endcanany
                                @can('transaksi.change-shift-transaksi')
                                    <x-dropdown.item href="#"
                                                     @click="$wire.dispatch('alert-confirm', { id: '{{ $transaksi->id }}', method: 'change-shift-transaksi' });">
                                        <span class="iconify tabler--truck-return text-lg"></span>
                                        Pindah Ke Shift {{ $transaksi->shift_transaksi == 'PAGI' ? 'MALAM' : 'PAGI' }}
                                    </x-dropdown.item>
                                @endcan
                            </x-dropdown>

                        </x-table.td>
                        <x-table.td>{{ $transaksi->tgl_created }}</x-table.td>
                        <x-table.td>
                            @canany(['transaksi.detail-all', 'transaksi.detail-own'])
                                <button class="underline"
                                        @click="$wire.dispatch('set-transaksi-barang', { id_transaksi: '{{ $transaksi->id }}'});
                                    $wire.dispatch('toggle-modal-transaksi_barang_detail',{transaksi_barang_detailModal:true})">
                                    {{ $transaksi->no_transaksi }}
                                </button>
                            @else
                                {{ $transaksi->no_transaksi }}
                            @endcanany
                            <h6 class="font-semibold">{{ $transaksi->jenis_transaksi }}</h6>
                        </x-table.td>
                        <x-table.td>{{ $transaksi->nama_pasien ?? $transaksi->nama_member }}</x-table.td>
                        <x-table.td class="min-w-28">Rp. {{ $transaksi->sub_total }}</x-table.td>
                        <x-table.td class="text-center">{{ $transaksi->nama_metode_pembayaran }}</x-table.td>
                        <x-table.td>Rp. {{ $transaksi->potongan_diskon }}</x-table.td>
                        <x-table.td class="min-w-28">Rp. {{ $transaksi->total_bayar }}</x-table.td>
                        <x-table.td>Rp. {{ $transaksi->pembulatan }}</x-table.td>
                        <x-table.td class="text-center">{{ $transaksi->shift_transaksi }}</x-table.td>

                    </x-table.tr>
                @empty
                    <x-table.tr-empty colspan="11" />
                @endforelse
                <x-table.loading-spin />

            </x-table.tbody>
        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $transaksis->onEachSide(1)->links(data: ['scrollTo' => '#table-transaksi']) }}
        </div>

        <x-modal name="transaksi_barang_detail" size="4xl">
            <livewire:transaksi.transaksi-barang-detail-modal />
        </x-modal>

    </div>
</div>
