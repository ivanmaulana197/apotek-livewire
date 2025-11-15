@php
    use App\Helpers\FormatHelper;
@endphp

@script
    <script>
        Alpine.data('barangList', () => ({
            formattedStok(stok, opsi_satuan, nama_satuan_utama) {
                opsi_satuan.push({
                    'id_satuan': 0,
                    'satuan': nama_satuan_utama,
                    'rasio': 1
                });
                let hasil = [];
                const EPS = 1e-9; // toleransi error kecil
                const fmod = (a, b) => a - Math.floor(a / b) * b;
                opsi_satuan = [...opsi_satuan].sort((a, b) => b.rasio - a.rasio)
                opsi_satuan.forEach((item) => {
                    const rasio = parseFloat(item.rasio);
                    if (rasio <= 0) return;

                    const jumlah = Math.floor((stok / rasio) + EPS);
                    if (jumlah > 0) {
                        hasil.push(`${formatNumberToString(jumlah)} ${item.satuan}`);
                    }

                    stok = fmod(stok, rasio);
                })

                return hasil.join(',  ');
            },
        }))
    </script>
@endscript

<div class="card" x-data="barangList">
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
                            {{-- {{ $barang->konversi_stok() }} --}}
                            <span x-text="formattedStok({{ $barang->stok }}, {{ $barang->satuan_opsi }}, '{{ $barang->nama_satuan_utama }}')"></span>
                            <p class="text-xs">
                                Min: @nominal($barang->stok_min, '0')
                            </p>
                        </x-table.td>
                        <x-table.td>
                            <span class="font-normal">Utama:</span>
                            <span class="font-semibold text-dark dark:text-white">{{ $barang->nama_satuan_utama }}</span>
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
                            {{-- <x-table.action-button tooltip="Edit" color="warning" type="href" href="{{ route('data.barang.edit', $barang->id) }}"
                                                   wire:navigate>
                                <span class="iconify tabler--edit-circle text-lg"></span>
                            </x-table.action-button> --}}
                            {{-- <x-table.action-button tooltip="Hapus" color="error"
                                                   @click="$wire.dispatch('alert-confirm', { id: '{{ $barang->id }}', method: 'delete-barang' });">
                                <span class="iconify tabler--trash-x-filled text-lg"></span>
                            </x-table.action-button> --}}
                            <x-dropdown text="" wire:key='dropdown-{{ $barang->id }}'>
                                <x-dropdown.item href="{{ route('data.barang.detail', $barang->id) }}" wire:navigate class="text-blue-600">
                                    <span class="iconify solar--eye-bold text-lg"></span>
                                    <span class="text-sm">Detail</span>
                                </x-dropdown.item>
                                <x-dropdown.item href="{{ route('data.barang.edit', $barang->id) }}" wire:navigate class="text-yellow-600">
                                    <span class="iconify tabler--edit-circle text-lg"></span>
                                    <span class="text-sm">Edit</span>
                                </x-dropdown.item>
                                <x-dropdown.item class="text-red-600 " href="#"
                                                 @click=" $wire.dispatch('alert-confirm', {id: '{{ $barang->id }}', method: 'delete-barang', text: 'Apakah anda yakin menghapus Barang?'}); ">
                                    <span class="iconify basil--trash-outline text-lg"></span>
                                    <span class="text-sm">Hapus</span>
                                </x-dropdown.item>
                                <x-dropdown.item href="{{ route('data.barang.detail.faktur', $barang->id) }}" wire:navigate>
                                    <span class="iconify solar--eye-bold text-lg"></span>
                                    <span class="text-sm">Detail Faktur</span>
                                </x-dropdown.item>
                                <x-dropdown.item href="{{ route('data.barang.detail.stok', $barang->id) }}" wire:navigate>
                                    <span class="iconify solar--eye-bold text-lg"></span>
                                    <span class="text-sm">Detail Stok</span>
                                </x-dropdown.item>
                                <x-dropdown.item href="{{ route('data.barang.detail.expired', $barang->id) }}" wire:navigate>
                                    <span class="iconify solar--eye-bold text-lg"></span>
                                    <span class="text-sm">Detail Expired</span>
                                </x-dropdown.item>
                                <x-dropdown.item href="{{ route('data.barang.detail.transaksi', $barang->id) }}" wire:navigate>
                                    <span class="iconify solar--eye-bold text-lg"></span>
                                    <span class="text-sm">Detail Transaksi</span>
                                </x-dropdown.item>
                            </x-dropdown>
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
