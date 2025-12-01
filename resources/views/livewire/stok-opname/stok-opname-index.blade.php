<x-slot:title>Stok Opname</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Stok Opname">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="grid grid-cols-1 gap-y-3">
        <div class="card">
            <div class="px-8 py-5">
                <div class="flex flex-row flex-wrap gap-3">
                    @can('stok-opname.history')
                        <a href="{{ route('stok-opname.history') }}" class="btn btn-sm bg-info hover:bg-infoemphasis" wire:navigate>
                            <i class="iconify mdi--clipboard-text-history-outline text-2xl"></i> History Stok Opname
                        </a>
                    @endcan
                    @can('stok-opname.refresh-stok-opname')
                        <button wire:click='newStokOpname' class="btn btn-sm bg-error hover:bg-erroremphasis">
                            <i class="iconify solar--trash-bin-trash-bold text-2xl "></i> Refresh Stok Opname
                        </button>
                    @endcan
                    @can('stok-opname.sinkronisasi-stok-keseluruhan')
                        <button @click="$wire.dispatch('alert-confirm', { method: 'sinkronisasi-stok' });" class="btn btn-sm bg-success hover:bg-successemphasis">
                            <i class="iconify fluent--save-sync-20-regular text-2xl"></i> Sinkronisasi Stok Keseluruhan
                        </button>
                    @endcan
                    @can('stok-opname.cetak-form')
                        <a class="btn btn-sm bg-primary hover:bg-primaryemphasis" href="{{ route('cetak.form-stok-opname') }}" wire:navigate>
                            <i class="iconify solar--printer-bold text-2xl"></i> Cetak Form Stok Opname
                        </a>
                    @endcan
                </div>

                {{-- info --}}
                <x-badge class="mt-3">
                    <x-slot:icon>
                        <i class="iconify solar--info-circle-bold text-lg mt-0.5"></i>
                    </x-slot:icon>
                    <x-slot:message>
                        <ul class="list-disc ms-5 pe-6">
                            <li> Refresh Stok Opname akan menghapus seluruh inputan Stok Real yang akan diisi Tanpa Tersimpan </li>
                            <li> Sinkronisasi akan mengintegrasikan Stok Real ke Stok di Sistem </li>
                        </ul>
                    </x-slot:message>
                </x-badge>

            </div>
        </div>
        @can('stok-opname.list')
            <livewire:stok-opname.stok-opname-list />
        @endcan
    </div>
</div>
