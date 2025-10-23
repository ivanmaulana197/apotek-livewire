<div class="grid grid-cols-12 gap-3">
    <div class="col-span-12 md:col-span-6">
        <x-form.input label="Nama Apotek" errorName="setting.nama" wire:model='setting.nama' />
        <x-form.input label="No. Telp" errorName="setting.no_telp" wire:model='setting.no_telp' />
        <x-form.input label="Email" errorName="setting.email" type="email" wire:model='setting.email' />

        <x-form.input label="Kabupaten" errorName="setting.kabupaten" wire:model='setting.kabupaten' />
        <x-form.input label="Alamat" errorName="setting.alamat" type="textarea" wire:model='setting.alamat' />
    </div>

    <div class="col-span-12 md:col-span-6">
        <x-form.input label="Alamat Website" errorName="setting.link_website" wire:model='setting.link_website' />
        <x-form.input label="Instagram" errorName="setting.link_instagram" wire:model='setting.link_instagram' />
        <x-form.input label="Facebook" errorName="setting.link_facebook" wire:model='setting.link_facebook' />
        <x-form.input label="Tik Tok" errorName="setting.link_tiktok" wire:model='setting.link_tiktok' />
        <x-form.input label="Link Google Map" errorName="setting.link_google_map" type="textarea" wire:model='setting.link_google_map' />
        {{-- <x-form.input label="Catatan Bawah Struk" errorName="setting.footer_struk" type="textarea"  /> --}}
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
