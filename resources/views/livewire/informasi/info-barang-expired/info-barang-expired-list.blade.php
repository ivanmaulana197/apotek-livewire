@php
    use App\Helpers\FormatHelper;
@endphp

@script
    <script>
        Alpine.data('infoBarangExpiredList', () => ({
            ceklistStokBarang: $wire.entangle('ceklistStokBarang'),
            statusCeklist: $wire.entangle('status_ceklist'),
            newTglExpiredCeklist: $wire.entangle('new_tgl_expired_ceklist'),
            status: [],
            jumlah_dilaporkan: [],
            new_tgl_expired: [],
            submit(id) {
                $wire.status[id] = this.status[id];
                $wire.jumlah_dilaporkan[id] = formatStringToNumber(this.jumlah_dilaporkan[id]);
                $wire.new_tgl_expired[id] = formatDateStringToDate(this.new_tgl_expired[id]);
                $wire.submit(id);
            }

        }));
    </script>
@endscript

<div class="card" x-data="infoBarangExpiredList">
    <div class="card-body">

        <x-datatable nama_table="stok_batch_barang" :$perPage></x-datatable>

        <div class="flex flex-col sm:flex-row gap-x-4 gap-y-2" x-show="ceklistStokBarang.length > 0">
            <div class="flex flex-row flex-wrap items-center">
                <label class="font-semibold me-2"> <span x-text="ceklistStokBarang.length"></span> Terpilih: </label>
                <label class="me-2"> Pilh Status :</label>
                <x-form.select-alpine name="status_ceklist" placeholder="Status" :direction=false class="min-w-56" x-model="statusCeklist"
                                      error-name="status_ceklist">
                    <x-slot:custom-option>
                        <option value="HAPUS INFO">HAPUS INFO</option>
                        <option value="EXPIRED BUANG">EXPIRED BUANG</option>
                        <option value="EXPIRED GANTI / RETUR">EXPIRED GANTI / RETUR</option>
                        <option value="RUSAK BUANG">RUSAK BUANG</option>
                        <option value="RUSAK GANTI">RUSAK GANTI</option>
                        <option value="HILANG">HILANG</option>
                    </x-slot:custom-option>
                </x-form.select-alpine>
            </div>
            <div class="flex flex-row flex-wrap items-center" x-show="statusCeklist == 'EXPIRED GANTI / RETUR' || statusCeklist == 'RUSAK GANTI'">
                <x-form.label label="Tgl Expired Baru" />
                <x-alpine.form.input-date :ranges=false minDate='{{ now()->toDateString() }}' :singleDatePicker=true errorName="new_tgl_expired_ceklist"
                                          nama="new_tgl_expired_ceklist" placeholder="Tgl Expired Baru" x-model="newTglExpiredCeklist" />
            </div>
            <div class="min-w-28">
                <button class="btn btn-sm hover:bg-primaryemphasis w-fit" wire:click='triggerCeklistStokBarang'>Submit</button>
            </div>
        </div>

        {{-- @if ($ceklistInfoBarang)
            <div class="flex flex-col sm:flex-row gap-x-4 gap-y-2">
                <div class="flex flex-row flex-wrap items-center">
                    <label class="font-semibold me-2"> {{ count($ceklistInfoBarang) }} Terpilih: </label>
                    <label class="me-2"> Pilh Status :</label>
                    <x-form.select errorName="status_ceklist" placeholder="Status" name="status_ceklist" :direction=false wire:model.live='status_ceklist' class="min-w-48">
                        <x-slot:custom-option>
                            <option value="HAPUS INFO" {{ ($status_ceklist ?? '') == 'HAPUS INFO' ? 'selected' : '' }}>HAPUS INFO</option>
                            <option value="EXPIRED BUANG" {{ ($status_ceklist ?? '') == 'EXPIRED BUANG' ? 'selected' : '' }}>EXPIRED BUANG</option>
                            <option value="EXPIRED GANTI / RETUR" {{ ($status_ceklist ?? '') == 'EXPIRED GANTI / RETUR' ? 'selected' : '' }}>EXPIRED GANTI / RETUR</option>
                            <option value="RUSAK BUANG" {{ ($status_ceklist ?? '') == 'RUSAK BUANG' ? 'selected' : '' }}>RUSAK BUANG</option>
                            <option value="RUSAK GANTI" {{ ($status_ceklist ?? '') == 'RUSAK GANTI' ? 'selected' : '' }}>RUSAK GANTI</option>
                            <option value="HILANG" {{ ($status_ceklist ?? '') == 'HILANG' ? 'selected' : '' }}>HILANG</option>
                        </x-slot:custom-option>
                    </x-form.select>
                </div>
                @if ($status_ceklist == 'EXPIRED GANTI / RETUR' || $status_ceklist == 'RUSAK GANTI')
                    <div class="flex flex-row flex-wrap items-center">
                        <x-form.label label="Tgl Expired Baru" />
                        <x-form.input-date :ranges=false minDate='{{ now()->toDateString() }}' :singleDatePicker=true errorName="tgl_expired_ceklist" nama="tgl_expired_ceklist" placeholder="Tgl Expired Baru" model="tgl_expired_ceklist" />
                    </div>
                @endif

                <div class="min-w-28">
                    <button class="btn btn-sm hover:bg-primaryemphasis w-fit" wire:click='triggerCeklistInfoBarang'>Submit</button>
                </div>
            </div>
        @endif --}}

        <x-table nama="stok_batch_barang" height="auto">
            <x-table.thead>
                <x-table.th label="#" />
                <x-table.th label="NO." />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Nama Barang" name="barang.nama" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Tgl Expired" name="tgl_expired" />
                <x-table.th-sortable :sortBy="$sortBy" :sortDir="$sortDir" label="Jumlah" name="jumlah" />
                <x-table.th label="Jumlah Yang Dilaporkan" />
                <x-table.th label="Keterangan Status" />
                <x-table.th label="Aksi" />
            </x-table.thead>
            <x-table.tbody>
                @php
                    $now = now();
                @endphp
                @forelse ($stok_batch_barang as $index => $item)
                    <x-table.tr wire:key="table-expired-{{ $item->id }}" class="{{ $item->warna }}" :key="'{{ $item->id }}'">
                        <x-table.td class="text-center">
                            <input id="hs-table-checkbox-{{ $index }}" x-model="ceklistStokBarang" value="{{ $item->id }}" type="checkbox"
                                   class="border-gray-400 w-4 h-4 rounded text-primary focus:ring-primary dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-gray-800">
                            <label for="hs-table-checkbox-{{ $index }}" class="sr-only">Checkbox</label>
                        </x-table.td>
                        <x-table.td class="text-center">{{ $stok_batch_barang->firstItem() + $index }}</x-table.td>
                        <x-table.td class="text-left">{{ $item->nama_barang }}</x-table.td>
                        <x-table.td class="text-center">{{ FormatHelper::formatDate($item->tgl_expired) }}</x-table.td>
                        <x-table.td class="text-center">{{ FormatHelper::formatNumberToString($item->jumlah) }} / {{ $item->nama_satuan }}</x-table.td>
                        <x-table.td class="min-w-44">
                            <x-form.input errorName="jumlah_dilaporkan.{{ $item->id }}" placeholder="Masukan Jumlah Stok"
                                          labelInputGroup="/{{ $item->barang->satuan_utama->nama }}" inputGroup="right" onkeyup="formatForInputNumber(this)"
                                          @keydown.enter="submit('{{ $item->id }}')" x-model="jumlah_dilaporkan['{{ $item->id }}']" />
                        </x-table.td>
                        <x-table.td class="min-w-44">
                            <x-form.select errorName="status.{{ $item->id }}" placeholder="Status" name="status_{{ $item->id }}" :direction=false
                                           x-model="status['{{ $item->id }}']">
                                {{-- wire:model.live='status.{{ $item->id }}'> --}}
                                <x-slot:custom-option>
                                    <option value="HAPUS INFO" {{ ($status[$item->id] ?? '') == 'HAPUS INFO' ? 'selected' : '' }}>HAPUS INFO</option>
                                    <option value="EXPIRED BUANG" {{ ($status[$item->id] ?? '') == 'EXPIRED BUANG' ? 'selected' : '' }}>EXPIRED BUANG</option>
                                    <option value="EXPIRED GANTI / RETUR" {{ ($status[$item->id] ?? '') == 'EXPIRED GANTI / RETUR' ? 'selected' : '' }}>EXPIRED
                                        GANTI / RETUR</option>
                                    <option value="RUSAK BUANG" {{ ($status[$item->id] ?? '') == 'RUSAK BUANG' ? 'selected' : '' }}>RUSAK BUANG</option>
                                    <option value="RUSAK GANTI" {{ ($status[$item->id] ?? '') == 'RUSAK GANTI' ? 'selected' : '' }}>RUSAK GANTI</option>
                                    <option value="HILANG" {{ ($status[$item->id] ?? '') == 'HILANG' ? 'selected' : '' }}>HILANG</option>
                                </x-slot:custom-option>
                            </x-form.select>

                            {{-- @if (array_key_exists("$item->id", $status) && ($status["$item->id"] == 'EXPIRED GANTI / RETUR' || $status["$item->id"] == 'RUSAK GANTI'))
                                <x-form.label label="Tgl Expired Baru" />
                                <x-form.input-date :ranges=false :minDate='$now' :select_date="$tgl_expired[$item->id] ?? null" :singleDatePicker=true
                                                   errorName="tgl_expired.{{ $item->id }}" nama="tgl_expired_{{ $item->id }}"
                                                   placeholder="Tgl Expired Baru" model="tgl_expired.{{ $item->id }}" />
                            @endif --}}
                            <div x-show="status['{{ $item->id }}'] == 'EXPIRED GANTI / RETUR' || status['{{ $item->id }}'] == 'RUSAK GANTI'">
                                <x-form.label label="Tgl Expired Baru" />
                                <x-alpine.form.input-date nama="new_tgl_expired_{{ $item->id }}" :minDate='$now' placeholder="Tgl Expired Baru"
                                                          :singleDatePicker=true x-model="new_tgl_expired['{{ $item->id }}']"
                                                          errorName="tgl_expired.{{ $item->id }}" :ranges=false />
                            </div>
                        </x-table.td>
                        <x-table.td>
                            {{-- <x-table.action-button tooltip="Submit" color="success" wire:click="submit('{{ $item->id }}')">
                                <i class="ti ti-check"></i> <span class="text-sm">Submit</span>
                            </x-table.action-button> --}}
                            <x-table.action-button tooltip="Submit" color="success" @click="submit('{{ $item->id }}')">
                                <i class="ti ti-check"></i> <span class="text-sm">Submit</span>
                            </x-table.action-button>
                            <x-table.action-button tooltip="Hapus Info" color="error" wire:click="hapusInfo('{{ $item->id }}')">
                                <i class="ti ti-trash"></i> <span class="text-sm">Hapus Info</span>
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
            {{ $stok_batch_barang->onEachSide(1)->links(data: ['scrollTo' => '#table-stok_batch_barang']) }}
        </div>
    </div>
</div>
