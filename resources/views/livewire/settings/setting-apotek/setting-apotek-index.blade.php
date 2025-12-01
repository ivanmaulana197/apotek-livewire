<x-slot:title>Pengaturan Apotek</x-slot:title>

<div>
    <x-breadcrumb name="Pengaturan Apotek">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="card shadow-md">
        <div class="card-body">
            @can('setting.edit')
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
            @else
                <x-tab.tab-vertikal-custom>
                    <x-slot:tab>
                        <x-tab.tab-vertikal-custom.button number="1" :active=true>Pengaturan UMUM</x-tab.tab-vertikal-custom.button>
                        <x-tab.tab-vertikal-custom.button number="2">Pengaturan Penjualan</x-tab.tab-vertikal-custom.button>

                    </x-slot:tab>
                    <x-slot:tabItem>
                        <x-tab.tab-vertikal-custom.content number="1" :active=true>
                            <div class="grid grid-cols-12 gap-3">
                                <div class="col-span-12 md:col-span-6">
                                    <x-form.text-display label="Nama Apotek" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->nama }}" />
                                    <x-form.text-display label="No. Telp" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->no_telp }}" />
                                    <x-form.text-display label="Email" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->email }}" />
                                    <x-form.text-display label="Kabupaten" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->kabupaten }}" />
                                    <x-form.text-display label="Alamat" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->alamat }}" />
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <x-form.text-display label="Alamat Website" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->link_website }}" />
                                    <x-form.text-display label="Instagram" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->link_instagram }}" />
                                    <x-form.text-display label="Facebook" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->link_facebook }}" />
                                    <x-form.text-display label="Tik Tok" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->link_tiktok }}" />
                                    <x-form.text-display label="Link Google Map" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->link_google_map }}" />
                                    <x-form.text-display label="Catatan Bawah Struk" class="bg-gray-200 dark:bg-gray-800" value="{{ $setting->footer_struk }}" />
                                </div>
                            </div>
                        </x-tab.tab-vertikal-custom.content>
                        <x-tab.tab-vertikal-custom.content number="2">
                            <div class="grid grid-cols-12 gap-3">
                                <div class="col-span-12">
                                    <h1 class="text-xl font-bold mb-1">Pengaturan Penjualan</h1>
                                    <hr>
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <x-form.text-display label="Pembulatan Harga Jual Barang" class="bg-gray-200 dark:bg-gray-800">
                                        <span x-text="'Rp. '+formatNumberToString({{ $setting->pembulatan_harga_barang }})"></span>
                                    </x-form.text-display>
                                    <x-form.text-display label="Pembulatan Total Umum" class="bg-gray-200 dark:bg-gray-800">
                                        <span x-text="'Rp. '+formatNumberToString({{ $setting->pembulatan_harga_barang }})"></span>
                                    </x-form.text-display>
                                    <x-form.text-display label="Pembulatan Total Resep" class="bg-gray-200 dark:bg-gray-800">
                                        <span x-text="'Rp. '+formatNumberToString({{ $setting->pembulatan_resep }})"></span>
                                    </x-form.text-display>

                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <x-form.text-display label="Margin Persen Jual" class="bg-gray-200 dark:bg-gray-800">
                                        <span x-text="formatNumberToString({{ $setting->persentase_jual }}) + ' %'"></span>
                                    </x-form.text-display>
                                    <x-form.text-display label="Margin Persen Jual Resep" class="bg-gray-200 dark:bg-gray-800">
                                        <span x-text="formatNumberToString({{ $setting->persentase_jual_resep }}) + ' %'"></span>
                                    </x-form.text-display>

                                </div>

                                <div class="flex mt-3 gap-3">
                                    <button type="submit" class="btn bg-primary hover:bg-primaryemphasis">
                                        Simpan
                                    </button>
                                    <a href="{{ url()->previous() }}" class="btn bg-gray-200 border hover:bg-gray-400 text-black" wire:navigate>
                                        Batal
                                    </a>
                                </div>
                            </div>
                        </x-tab.tab-vertikal-custom.content>
                    </x-slot:tabItem>
                </x-tab.tab-vertikal-custom>
            @endcan

        </div>
    </div>

</div>
