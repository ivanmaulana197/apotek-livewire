<x-slot:title>Detail Dokter</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Detail Dokter {{ $dokter['nama'] }}">
        <x-slot:button>
            <a class="btn btn-xs bg-slate-500 hover:bg-slate-700" href="{{ route('data-master.dokter') }}" wire:navigate>
                <span class="iconify famicons--caret-back"></span>
                Kembali
            </a>
        </x-slot:button>
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
            <x-breadcrumb.before name="Data Dokter" href="{{ route('data-master.dokter') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="card mb-2">
        <div class="card-body p-4" x-data>
            @if (!$isEdit)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                    <x-form.text-display label="Nama Dokter" :value="$dokter['nama']" />
                    <x-form.text-display label="Spesialis" :value="$dokter['spesialis']" />
                    <x-form.text-display label="Alamat" :value="$dokter['alamat'] ?? '-'" />
                    <x-form.text-display label="No. HP" :value="$dokter['no_hp'] ?? '-'" />
                </div>
                @can('dokter.edit')
                    <div class="flex gap-3 justify-start mt-2">
                        <button wire:click='edit' @click="$focus.focus($refs.inputs);" class="btn btn-sm" wire:loading.remove wire:target='edit'>Edit</button>
                        <button class="btn btn-sm flex items-center justify-center pointer-events-none opacity-50" wire:loading wire:target='edit'>
                            <span class="iconify eos-icons--loading"></span> Edit
                        </button>
                    </div>
                @endcan
            @else
                @can('dokter.edit')
                    <form wire:submit.prevent="updateDokter">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3" s>
                            <x-form.input label="Nama Dokter" wire:model='dokter.nama' error-name="dokter.nama" onkeyup="formatToUpperCase(this)" x-ref="inputs" />
                            <x-form.input label="Spesialis" wire:model='dokter.spesialis' error-name="dokter.spesialis" onkeyup="formatToUpperCase(this)" />
                            <x-form.input label="Alamat Dokter" wire:model='dokter.alamat' error-name="dokter.alamat" />
                            <x-form.input label="No. Telepon Dokter" wire:model='dokter.no_hp' error-name="dokter.no_hp" />
                        </div>
                        <div class="flex gap-3 justify-start mt-2">
                            <button type="button" wire:click='cancel' class="btn btn-sm bg-gray-200 hover:bg-gray-400 text-dark" wire:loading.remove
                                    wire:target='cancel'>Batal</button>
                            <button type="button"
                                    class="btn btn-sm bg-gray-200 hover:bg-gray-400 text-dark flex items-center justify-center pointer-events-none opacity-50"
                                    wire:loading wire:target='cancel'>
                                <span class="iconify eos-icons--loading"></span> Batal
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary" wire:loading.remove wire:target='updateDokter'>Update</button>
                            <button type="button" class="btn btn-sm flex items-center justify-center pointer-events-none opacity-50" wire:loading
                                    wire:target='updateDokter'>
                                <span class="iconify eos-icons--loading"></span> Update
                            </button>

                        </div>
                    </form>
                @endcan
            @endif
        </div>
    </div>
    @canany(['dokter.detail.info-total-tebus-obat', 'dokter.detail.info-total-transaksi'])
        <livewire:data-master.dokter.dokter-detail.dokter-card-info-transaksi :id_dokter="$dokter['id']" :$filter_date />
    @endcanany
    <div class="card my-2">
        <div class="card-body p-4">
            <div class="flex flex-wrap items-center gap-3">
                <h5 class="text-sm font-medium text-slate-600">Periode</h5>
                <div class="grow">
                    <x-form.input-date class="w-full min-w-80" :bigRange=true nama="filter_date" model="filter_date" :select_date='$filter_date'></x-form.input-date>
                </div>
                @can('dokter.cetak-laporan-by-dokter')
                    <button @click="
                    let url = '{{ route('cetak.laporan-dokter.detail', ['id_dokter' => $dokter['id']]) }}'
                            + '?date[start]={{ $filter_date['start'] }}'
                            + '&date[end]={{ $filter_date['end'] }}';
                        window.open(url, '_blank');
                "
                            class="btn btn-sm btn-primary flex-none">Cetak Laporan</button>
                @endcan
            </div>
        </div>
    </div>
    <x-tab class="mt-2">
        <x-slot:tab>
            @can('dokter.detail.list-data-barang-terjual')
                <x-tab.button number="1" class="active" wire:ignore>Informasi</x-tab.button>
            @endcan
            @can('dokter.detail.list-data-transaksi')
                <x-tab.button number="2" wire:ignore>Data Transaksi</x-tab.button>
            @endcan
        </x-slot:tab>
        <x-slot:tabItem>
            @can('dokter.detail.list-data-barang-terjual')
                <x-tab.content number="1" :active=true wire:ignore>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <livewire:data-master.dokter.dokter-detail.dokter-list-barang-terjual :id_dokter="$dokter['id']" :$filter_date />
                        <livewire:data-master.pasien.pasien-list :id_dokter="$dokter['id']" :$filter_date />
                    </div>
                </x-tab.content>
            @endcan
            @can('dokter.detail.list-data-transaksi')
                <x-tab.content number="2" wire:ignore>
                    <livewire:transaksi.transaksi-list :id_dokter="$dokter['id']" :$filter_date />
                </x-tab.content>
            @endcan
        </x-slot:tabItem>
    </x-tab>

</div>
