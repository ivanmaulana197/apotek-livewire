@script
    <script>
        Alpine.data('stokOpnameList', () => ({
            bulan: formatDate(moment(), false, false, 'bulan'),
            stok_real: $wire.entangle('stok_real'),
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
            init() {
                Livewire.on('update-stok-real', () => {
                    this.$nextTick(() => {
                        this.stok_real = $wire.entangle('stok_real');

                    });
                })
            }
        }))
    </script>
@endscript

@php
    use App\Helpers\FormatHelper;
@endphp

<div class="card" x-data="stokOpnameList">
    <div class="card-body">
        <div class="card-title mb-0 flex flex-wrap justify-between">
            <div>
                Stok Opname :
                <x-badge icon="" status="success">
                    <x-slot:message>
                        <span class="font-bold text-base" x-text="bulan"></span>
                    </x-slot:message>
                </x-badge>
            </div>
            @can('stok-opname.info-total-kerugian')
                <div
                     class="px-3 py-1.5 inline-flex justify-center items-center text-sm {{ $total_kerugian < 0 ? 'bg-error' : 'bg-success' }} rounded-lg text-white gap-2">
                    <i class="iconify tabler--cash-register text-2xl"></i>
                    Nominal Kerugian : Rp. {{ $total_kerugian > 0 ? '+' : '' }}@nominal($total_kerugian)
                </div>
            @endcan
        </div>

        <x-datatable nama_table="stok_opname" :$perPage placeholder_search="Masukan KODE/NAMA Barang">
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

        <x-table nama="stok_opname" height="h-fit w-full">
            <x-table.thead>
                <x-table.th label="NO." />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Kode Barang" name="kode" class="text-xs " />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Barang" name="barang.nama" />
                {{-- <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Kategori" name="kategori.nama" /> --}}
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="H.Beli" name="barang.harga_beli_default" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="H.Jual" name="barang.harga_jual_utama" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Stok" name="barang.stok" />
                <x-table.th label="Stok Toko" />
                <x-table.th label="Selisih" />
                <x-table.th label="Kerugian" />
                <x-table.th label="Aksi" />
            </x-table.thead>
            <x-table.tbody>
                @forelse ($barangs as $index => $barang)
                    @php
                        $stok_real[$barang->id] = FormatHelper::formatNumberToString($barang->stok_real);
                    @endphp
                    <x-table.tr wire:key='table-stokopname{{ $barang->id }}' x-data="{ stok_real: $wire.entangle('stok_real.{{ $barang->id }}') }">
                        <x-table.td class="text-center !py-0.5 w-4">{{ $barangs->firstItem() + $index }}</x-table.td>
                        <x-table.td class="!py-0.5 w-8">
                            <h6 class="text-sm whitespace-normal line-clamp-2">
                                {!! $this->highlight($barang->kode) !!}
                            </h6>
                        </x-table.td>
                        <x-table.td class="!py-0.5">
                            <h6 class="text-sm whitespace-normal line-clamp-3">
                                {!! $this->highlight($barang->nama) !!}
                            </h6>
                        </x-table.td>
                        {{-- <x-table.td class="!py-0.5">
                            @foreach ($barang->kategories as $index => $kategori)
                                <p>- {{ $kategori->nama }}</p>
                            @endforeach
                        </x-table.td> --}}
                        <x-table.td class="!py-1 !text-xs min-w-24">
                            Rp. @nominal($barang->harga_beli_default)
                        </x-table.td>
                        <x-table.td class="!py-1 !text-xs min-w-24">
                            Rp. @nominal($barang->harga_jual_utama)
                        </x-table.td>
                        <x-table.td class="!py-1 !text-xs">
                            @decimal($barang->stok) / {{ $barang->nama_satuan_utama }}
                        </x-table.td>

                        <x-table.td class="min-w-44">
                            <x-form.input wire:ignore x-data
                                          x-on:keydown.enter.prevent="
                                $wire.simpanStokReal('{{ $barang->id }}');
                                let inputs = [...document.querySelectorAll('input[id^=stok_real_]')];
                                let index = inputs.findIndex(i => i.id === 'stok_real_{{ $barang->id }}');
                                if (index >= 0 && inputs[index + 1]) {
                                    inputs[index + 1].focus();
                                }
                            "
                                          errorName="stok_real.{{ $barang->id }}" placeholder="Masukan Jumlah Stok di Toko"
                                          labelInputGroup="/{{ $barang->nama_satuan_utama }}" inputGroup="right" x-model="stok_real"
                                          id="stok_real_{{ $barang->id }}" onkeyup="formatForInputNumber(this)" />
                            <span class="text-xs"
                                  x-text="formattedStok(formatStringToNumber(stok_real), {{ $barang->satuan_opsi }}, '{{ $barang->nama_satuan_utama }}')"></span>
                        </x-table.td>
                        <x-table.td class="!py-1 !text-xs text-center">
                            @decimal($barang->stok_real - $barang->stok)
                        </x-table.td>
                        <x-table.td class="!py-1 !text-xs min-w-28">
                            Rp. @nominal(($barang->stok_real - $barang->stok) * $barang->harga_beli_default)
                        </x-table.td>
                        <x-table.td class="!py-1 text-center">
                            <div class="inline-flex">
                                <x-table.action-button color="info">
                                    <x-slot name="tooltip" class="max-w-xs w-full text-start">
                                        <div class="p-2 gap-1 text-sm ">
                                            <div>
                                                <span class="font-bold">Kategori :</span>
                                                @foreach ($barang->kategories as $kategori)
                                                    <p class="text-xs">- {{ $kategori->nama }}</p>
                                                @endforeach
                                            </div>
                                            <div>
                                                <span class="font-bold">Rak :</span>
                                                @foreach ($barang->raks as $rak)
                                                    <p class="text-xs">- {{ $rak->nama }}</p>
                                                @endforeach
                                            </div>
                                            <div class="font-normal">
                                                <span class="font-bold">Satuan Utama </span> : {{ $barang->nama_satuan_utama }}
                                            </div>
                                            <div class="font-normal">
                                                <span class="font-bold">Opsi Satuan :</span>
                                                @forelse ($barang->opsi_satuan as $opsi_satuan)
                                                    <p class="text-xs">-
                                                        {{ $opsi_satuan->pivot->rasio_satuan_opsi }}
                                                        {{ $opsi_satuan->nama }} =
                                                        {{ $opsi_satuan->pivot->rasio_satuan_utama }}
                                                        {{ $barang->nama_satuan_utama }}</p>
                                                @empty
                                                    -
                                                @endforelse
                                            </div>
                                        </div>
                                    </x-slot>
                                    <i class="iconify tabler--info-square-rounded-filled"></i>
                                </x-table.action-button>
                                <x-table.action-button tooltip="Confirm" color="success" wire:click="simpanStokReal('{{ $barang->id }}')" class="text-xs">
                                    <i class="ti ti-eye"></i> Confirm
                                </x-table.action-button>
                                <x-table.action-button tooltip="Confirm" color="error" wire:click="clearStokReal('{{ $barang->id }}')" class="text-xs">
                                    <i class="ti ti-trash"></i> Clear
                                </x-table.action-button>
                            </div>

                        </x-table.td>
                    </x-table.tr>
                @empty
                    <tr>
                        <td colspan="11" class="p-2 text-sm font-semibold text-center text-dark dark:text-white">
                            Tidak Ada Data
                        </td>
                    </tr>
                @endforelse
                <tr wire:loading class="absolute inset-0 z-10 bg-gray-700/90 ">
                    <td colspan="100%" class="absolute inset-0 space-x-2 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span>Memproses data...</span>
                    </td>
                </tr>
                {{-- <x-table.loading colspan="11" /> --}}
            </x-table.tbody>
        </x-table>

        <div class="overflow-y-hidden mt-3">
            {{ $barangs->onEachSide(1)->links(data: ['scrollTo' => '#table-stok_opname']) }}
        </div>
    </div>
</div>
