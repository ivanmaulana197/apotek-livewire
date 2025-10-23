<div class="card">
    <div class="card-body">
        <div class="flex justify-between items-center border-b border-gray-200">
            {{-- <h3 class="font-bold text-gray-800">Modal Title</h3> --}}
            <h5 class="card-title">{{ $isEdit ? 'Update' : 'Tambah' }} Kategori</h5>
            @if ($isModal)
                <x-modal.button-close @click=" $dispatch('toggle-modal-kategori',{ kategoriModal: false }); $wire.resetForm()" />
            @endif
        </div>

        <form wire:submit.prevent='{{ $isEdit ? 'update' : 'save' }}' @edit-kategori.window="$focus.focus($refs.inputs)">
            <x-form.input label="Nama Kategori" wire:model='nama' error-name="nama" x-ref="inputs" />

            <div class="flex gap-3 justify-end mt-6">
                <button type="submit" class="btn btn-sm ">
                    {{ $isEdit ? 'Update' : 'Simpan' }}
                </button>
                @if (!$isModal)
                    <button type="button" @click="$focus.focus($refs.inputs); $wire.resetForm(); " class="btn btn-sm btn-light-error">Batal</button>
                @else
                    <button type="button" @click=" $dispatch('toggle-modal-kategori',{ kategoriModal: false }); $wire.resetForm()" class="btn btn-sm btn-light-error">Batal</button>
                @endif
            </div>
        </form>
    </div>
    {{-- Success is as dangerous as failure. --}}
</div>
