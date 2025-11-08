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
                <div class="flex gap-3 justify-start mt-2">
                    <button wire:click='edit' @click="$focus.focus($refs.inputs);" class="btn btn-sm" wire:loading.remove wire:target='edit'>Edit</button>
                    <button class="btn btn-sm flex items-center justify-center pointer-events-none opacity-50" wire:loading wire:target='edit'>
                        <span class="iconify eos-icons--loading"></span> Edit
                    </button>
                </div>
            @else
                <form wire:submit.prevent="updateDokter" @edit-dokter.window="$focus.focus($refs.inputs)">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
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
            @endif
        </div>
    </div>

    <livewire:data-master.dokter.dokter-detail.dokter-card-info-transaksi :id_dokter="$dokter['id']" :$filter_date />

    <div class="mb-2 w-full">
        <x-form.input-date :bigRange=true nama="filter_date" model="filter_date" :select_date='$filter_date'></x-form.input-date>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        <livewire:data-master.dokter.dokter-detail.dokter-list-barang-terjual :id_dokter="$dokter['id']" :$filter_date />
        <livewire:data-master.pasien.pasien-list :id_dokter="$dokter['id']" :$filter_date />
    </div>

</div>
