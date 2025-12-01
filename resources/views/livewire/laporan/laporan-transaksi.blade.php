<div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">
                Cetak Laporan Transaksi
            </div>
            <div class="flex flex-wrap gap-3">
                <div class="grow">
                    <div class="w-full sm:min-w-80">
                        <x-form.label label="Periode :" />
                        <x-form.input-date :bigRange=true nama="filter_date" model="filter_date" :select_date='$filter_date'></x-form.input-date>
                    </div>
                </div>
                <div class="grow">
                    <x-form.select name="shift_transaksi" label="SHIFT TRANSAKSI" :direction=false :empty-option=false :dropdown-input=false
                                   wire:model.live='shift_transaksi'>
                        <x-slot:custom-option>
                            <option value="SEMUA" {{ $shift_transaksi == 'SEMUA' ? 'selected' : '' }}>SEMUA</option>
                            <option value="PAGI" {{ $shift_transaksi == 'PAGI' ? 'selected' : '' }}>PAGI</option>
                            <option value="MALAM" {{ $shift_transaksi == 'MALAM' ? 'selected' : '' }}>MALAM</option>
                        </x-slot:custom-option>
                    </x-form.select>
                </div>
                <div class="grow">
                    <x-form.select name="by_user" label="PENGGUNA" :direction=false :empty-option=false wire:model.live='by_user'>
                        <x-slot:custom-option>
                            <option value="SEMUA">SEMUA</option>
                            @foreach ($this->users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </x-slot:custom-option>
                    </x-form.select>
                </div>
            </div>
            @canany(['transaksi.cetak-laporan-all', 'transaksi.cetak-laporan-own'])
                <button @click="
                    let url = '{{ route('cetak.laporan-transaksi') }}'
                            + '?date[start]={{ $filter_date['start'] }}'
                            + '&date[end]={{ $filter_date['end'] }}'
                            + '&shift={{ $shift_transaksi }}'
                            + '&by_user={{ $by_user }}';
                        window.open(url, '_blank');"
                        class="btn btn-sm btn-primary flex-none">Cetak Laporan</button>
            @endcanany
        </div>
    </div>

</div>
