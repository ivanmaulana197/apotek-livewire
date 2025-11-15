<x-slot:title>Detail Member</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Detail Member {{ $member['nama'] }}">
        <x-slot:button>
            <a class="btn btn-xs bg-slate-500 hover:bg-slate-700" href="{{ route('data-master.member') }}" wire:navigate>
                <span class="iconify famicons--caret-back"></span>
                Kembali
            </a>
        </x-slot:button>
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
            <x-breadcrumb.before name="Data Member" href="{{ route('data-master.member') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="card mb-2">
        <div class="card-body">
            @if (!$isEdit)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                    <x-form.text-display label="Nama Member" :value="$member['nama']" />
                    <x-form.text-display label="Profesi" :value="$member['profesi']['nama']" />
                    <x-form.text-display label="Nomor HP" :value="$member['no_hp']" />
                    <x-form.text-display label="Alamat" :value="$member['alamat']" />
                    <div class="flex gap-3 justify-start mt-2">
                        <button wire:click='edit' class="btn btn-sm" wire:loading.remove wire:target='edit'>Edit</button>
                        <button class="btn btn-sm flex items-center justify-center pointer-events-none opacity-50" wire:loading wire:target='edit'>
                            <span class="iconify eos-icons--loading"></span> Edit
                        </button>
                    </div>
                </div>
            @else
                <form wire:submit.prevent="updateMember">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                        <x-form.input label="Nama Member" wire:model='member.nama' error-name="member.nama" onkeyup="formatToUpperCase(this)" x-ref="inputs" />
                        <x-form.select label="Profesi" name="profesi" :data-option="$this->profesies" option-value="id" option-label="nama" modal-name="profesi"
                                       :add-value=true wire:model="member.id_profesi" error-name="member.id_profesi" :item-selected="$member['id_profesi']" />
                        <x-form.input label="Alamat Member" wire:model='member.alamat' error-name="member.alamat" />
                        <x-form.input label="No. Telepon Member" wire:model='member.no_hp' error-name="member.no_hp" />

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
                    </div>
                </form>
            @endif

        </div>
    </div>

    <livewire:data-master.member.member-detail.member-card-info-transaksi :id_member="$member['id']" :$filter_date />

    <div class="card my-2">
        <div class="card-body p-4">
            <div class="flex flex-wrap items-center gap-3">
                <h5 class="text-sm font-medium text-slate-600">Periode</h5>
                <div class="grow">
                    <x-form.input-date class="w-full min-w-80" :bigRange=true nama="filter_date" model="filter_date" :select_date='$filter_date'></x-form.input-date>
                </div>
                <button @click="
                    let url = '{{ route('cetak.laporan-member.detail', ['id_member' => $member['id']]) }}'
                            + '?date[start]={{ $filter_date['start'] }}'
                            + '&date[end]={{ $filter_date['end'] }}';
                        window.open(url, '_blank');"
                        class="btn btn-sm btn-primary flex-none">Cetak Laporan</button>
            </div>
        </div>
    </div>

    <x-tab class="mt-2">
        <x-slot:tab>
            <x-tab.button number="1" class="active" wire:ignore>Informasi</x-tab.button>
            <x-tab.button number="2" wire:ignore>Data Transaksi</x-tab.button>
        </x-slot:tab>
        <x-slot:tabItem>
            <x-tab.content number="1" :active=true wire:ignore>
                <livewire:data-master.member.member-detail.member-list-barang-terjual :id_member="$member['id']" :$filter_date />
            </x-tab.content>
            <x-tab.content number="2" wire:ignore>
                <livewire:transaksi.transaksi-list :id_member="$member['id']" :$filter_date />
            </x-tab.content>
        </x-slot:tabItem>
    </x-tab>

</div>
