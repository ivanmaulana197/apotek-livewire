<div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">
                Cetak Laporan Faktur
            </div>
            <div class="flex flex-wrap gap-3">
                <div class="grow">
                    <x-form.label label="Tanggal Dibuat :" />
                    <x-form.input-date :bigRange=true nama="created_at" model="created_at" :select_date='$created_at'></x-form.input-date>
                </div>
                <div class="grow">
                    <x-form.label label="Tgl Jatuh Tempo :" />
                    <x-form.input-date nama="tgl_jatuh_tempo" model="tgl_jatuh_tempo"></x-form.input-date>

                </div>
                <div class="grow">
                    <div class="w-full" wire:ignore>
                        <x-form.select label="Supplier :" multiple name="supplier" :direction=false wire:model.live='id_suppliers' :dataOption="$this->suppliers"
                                       option-value="id" option-label="nama">
                        </x-form.select>
                    </div>
                </div>
                <div class="grow">
                    <div class="w-full" wire:ignore>
                        <x-form.select label="Status :" name="status" :direction=false wire:model.live='status'>
                            <x-slot:custom-option>
                                <option value="LUNAS">LUNAS</option>
                                <option value="BELUM LUNAS">BELUM LUNAS</option>
                            </x-slot:custom-option>
                        </x-form.select>
                    </div>
                </div>
            </div>
            @can('faktur.cetak-laporan')
                <button @click="
                    const params = new URLSearchParams();

                    params.append('date[start]', '{{ $created_at['start'] ?? null }}');
                    params.append('date[end]', '{{ $created_at['end'] ?? null }}');
                    params.append('status', '{{ $status ?? null }}');
                    params.append('tgl_jatuh_tempo[start]', '{{ $tgl_jatuh_tempo['start'] ?? null }}');
                    params.append('tgl_jatuh_tempo[end]', '{{ $tgl_jatuh_tempo['end'] ?? null }}');

                    const suppliers = @js($id_suppliers ?? []);
                    suppliers.forEach((id, index) => {
                        params.append(`id_suppliers[${index}]`, id);
                    });

                    const url = '{{ route('cetak.laporan-faktur') }}' + '?' + params.toString();
                    window.open(url, '_blank');
                "
                        class="btn btn-sm btn-primary flex-none">
                    Cetak Laporan
                </button>
            @endcan

        </div>
    </div>

</div>
