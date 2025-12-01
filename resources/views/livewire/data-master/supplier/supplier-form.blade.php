<div class="card">
    <div class="card-body">
        <div class="flex justify-between items-center border-b border-gray-200">
            {{-- <h3 class="font-bold text-gray-800">Modal Title</h3> --}}
            <h5 class="card-title">{{ $isEdit ? 'Update' : 'Tambah' }} Supplier</h5>
            @if ($isModal)
                <x-modal.button-close @click=" $dispatch('toggle-modal-supplier',{ supplierModal: false }); $wire.resetForm()" />
            @endif
        </div>

        <form wire:submit.prevent='{{ $isEdit ? 'update' : 'save' }}' @edit-supplier.window="$focus.focus($refs.inputs)">
            <x-form.input label="Nama Supplier" wire:model='nama' error-name="nama" x-ref="inputs" />
            <x-form.select label="Asal PT/CV/UD" name='asal' :data-option="$supplierAsalEnum" option-label='value' option-value='value' wire:model='asal' errorName="asal"
                           dropdownParent=""></x-form.select>
            <x-form.input label="Alamat Supplier" wire:model='alamat' error-name="alamat" />
            <x-form.input label="No. Telepon Supplier" wire:model='no_hp' error-name="no_hp" />
            <x-form.checkbox label-dir="right" wire:model='is_pajak' error-name="is_pajak">Apakah supplier sudah dikenakan pajak?</x-form.checkbox>

            <div class="flex gap-3 justify-end mt-6">
                <button type="submit" class="btn btn-sm ">
                    {{ $isEdit ? 'Update' : 'Simpan' }}
                </button>
                @if (!$isModal)
                    <button type="button" @click="$focus.focus($refs.inputs); $wire.resetForm(); " class="btn btn-sm btn-light-error">Batal</button>
                @else
                    <button type="button" @click=" $dispatch('toggle-modal-supplier',{ supplierModal: false }); $wire.resetForm()"
                            class="btn btn-sm btn-light-error">Batal</button>
                @endif
            </div>
        </form>
    </div>
    {{-- Success is as dangerous as failure. --}}
</div>
