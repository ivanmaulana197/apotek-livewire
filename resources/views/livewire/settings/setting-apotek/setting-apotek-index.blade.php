<x-slot:title>Pengaturan Apotek</x-slot:title>

<div>
    <x-breadcrumb name="Pengaturan Apotek">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="card shadow-md">
        <div class="card-body">
            <form wire:submit.prevent='save'>
                <x-tab.tab-vertikal-custom>
                    <x-slot:tab>
                        <x-tab.tab-vertikal-custom.button number="1" :active=true>Pengaturan UMUM</x-tab.tab-vertikal-custom.button>
                        <x-tab.tab-vertikal-custom.button number="2">Pengaturan Penjualan</x-tab.tab-vertikal-custom.button>
                        
                    </x-slot:tab>
                    <x-slot:tabItem>
                        <x-tab.tab-vertikal-custom.content number="1" :active=true>
                            @include('livewire.settings.setting-apotek.setting-umum')
                        </x-tab.tab-vertikal-custom.content>
                        <x-tab.tab-vertikal-custom.content number="2">
                            @include('livewire.settings.setting-apotek.setting-penjualan')
                        </x-tab.tab-vertikal-custom.content>
                    </x-slot:tabItem>
                </x-tab.tab-vertikal-custom>
                {{-- <div class="flex mt-3 gap-3">
                    <button type="submit" class="btn bg-primary hover:bg-primaryemphasis">
                        Simpan
                    </button>
                    <a href="{{ url()->previous() }}" class="btn bg-gray-200 border hover:bg-gray-400 text-black" wire:navigate>
                        Batal
                    </a>
                </div> --}}
            </form>

        </div>
    </div>

</div>
