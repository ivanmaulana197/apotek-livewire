@php
    use App\Helpers\FormatHelper;
@endphp

<div class="card">
    <div class="card-body">
        <div class="card-title">
            List Barang
        </div>
        <x-datatable nama_table="barang" :$perPage placeholder_search="Masukan KODE/NAMA Barang">
            <div class="w-svw sm:w-56" wire:ignore>
                <x-form.select label="Kategori :" multiple name="kategori" :direction=false wire:model.live='filters.id_kategories' :dataOption="$this->kategories"
                               option-value="id" option-label="nama">
                </x-form.select>
            </div>
            <div class="w-svw sm:w-56" wire:ignore>
                <x-form.select label="Rak :" multiple name="rak" :direction=false wire:model.live='filters.id_raks' :dataOption="$this->raks" option-value="id"
                               option-label="nama">
                </x-form.select>
            </div>
        </x-datatable>

        <x-table nama="barang" height="auto">
            <x-table.thead>
                <x-table.th label="NO." />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="KODE Barang" name="kode" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Barang" name="barang.nama" />
                <x-table.th label="Kategori" />
                <x-table.th label="Rak" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Stok" name="stok" />
                <x-table.th label="Satuan" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="H.Beli" name="barang.harga_beli_default" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="H.Jual" name="barang.harga_jual_utama" />
                <x-table.th label="H.Grosir" />
                <x-table.th label="Aksi" />
            </x-table.thead>
            <x-table.tbody>
                @forelse ($barangs as $index => $barang)
                    <x-table.tr wire:key="table-barang-{{ $barang->id }}">
                        <x-table.td class="text-center">{{ $barangs->firstItem() + $index }}</x-table.td>
                        <x-table.td>{{ $barang->kode }}</x-table.td>
                        <x-table.td>
                            <h6 class="text-sm whitespace-normal line-clamp-3 w-32">
                                {{ $barang->nama }}
                            </h6>
                        </x-table.td>
                        <x-table.td>
                            @foreach ($barang->kategories as $index => $kategori)
                                <p>- {{ $kategori->nama }}</p>
                            @endforeach
                        </x-table.td>
                        <x-table.td>
                            @foreach ($barang->raks as $index => $rak)
                                <p>- {{ $rak->nama }}</p>
                            @endforeach
                        </x-table.td>
                        <x-table.td>
                            {{ $barang->konversi_stok() }}
                            <p class="text-xs">
                                Min: @nominal($barang->stok_min, '0')
                            </p>
                        </x-table.td>
                        <x-table.td>
                            <span class="font-normal">Utama:</span>
                            <span class="font-semibold text-dark dark:text-white">{{ $barang->satuan_utama->nama }}</span>
                            @foreach ($barang->opsi_satuan as $index => $opsi_satuan)
                                <p class="text-xs">- {{ $opsi_satuan->nama }}</p>
                            @endforeach
                        </x-table.td>
                        <x-table.td>
                            Rp. @nominal($barang->harga_beli_default)
                        </x-table.td>
                        <x-table.td>
                            Rp. @nominal($barang->harga_jual_utama)
                        </x-table.td>
                        <x-table.td>
                            @foreach ($barang->opsi_harga as $index => $opsi_harga)
                                <div class="mb-2">
                                    <p class="text-xs">H.Jual: Rp.
                                        @nominal($opsi_harga->harga)</p>
                                    <p class="text-xs">Min:
                                        @nominal($opsi_harga->jumlah_min)/{{ $opsi_harga->satuan->nama }}
                                    </p>
                                </div>
                            @endforeach
                        </x-table.td>

                        <x-table.td class="text-center">
                            <x-table.action-button tooltip="Detail" color="secondary" type="href" href="{{ route('data.barang.detail', $barang->id) }}"
                                                   wire:navigate>
                                <span class="iconify tabler--eye text-lg"></span>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Edit" color="warning" type="href" href="{{ route('data.barang.edit', $barang->id) }}"
                                                   wire:navigate>
                                <span class="iconify tabler--edit-circle text-lg"></span>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Hapus" color="error"
                                                   @click="$wire.dispatch('alert-confirm', { id: '{{ $barang->id }}', method: 'delete-barang' });">
                                <span class="iconify tabler--trash-x-filled text-lg"></span>
                            </x-table.action-button>
                        </x-table.td>
                    </x-table.tr>
                @empty
                    <x-table.tr-empty colspan="11" />
                @endforelse
                <x-table.loading-spin />
            </x-table.tbody>
        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $barangs->onEachSide(1)->links(data: ['scrollTo' => '#table-barang']) }}
        </div>
    </div>
</div>
