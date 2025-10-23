<div class="grid grid-cols-12 gap-3">
    <div class="col-span-12">
        <h1 class="text-xl font-bold mb-1">Pengaturan Penjualan</h1>
        <hr>
    </div>

    <div class="col-span-12 md:col-span-6">
        <x-form.input label="Pembulatan Harga Jual Barang" onkeyup="formatForInputNumber(this)" errorName="setting.pembulatan_harga_barang"
                      wire:model='setting.pembulatan_harga_barang' input-group="left" label-input-group="Rp" />
        <x-form.input label="Pembulatan Total Umum" onkeyup="formatForInputNumber(this)" errorName="setting.pembulatan_total"
                      wire:model='setting.pembulatan_total' input-group="left" label-input-group="Rp" />
        <x-form.input label="Pembulatan Total Resep" onkeyup="formatForInputNumber(this)" errorName="setting.pembulatan_resep"
                      wire:model='setting.pembulatan_resep' input-group="left" label-input-group="Rp" />
    </div>

    <div class="col-span-12 md:col-span-6">
        <x-form.input label="Margin Persen Jual" onkeyup="formatForInputNumber(this)" errorName="setting.persentase_jual" wire:model='setting.persentase_jual'
                      input-group="right" label-input-group="%" />
        <x-form.input label="Margin Persen Jual Resep" onkeyup="formatForInputNumber(this)" errorName="setting.persentase_jual_resep"
                      wire:model='setting.persentase_jual_resep' input-group="right" label-input-group="%" />
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
