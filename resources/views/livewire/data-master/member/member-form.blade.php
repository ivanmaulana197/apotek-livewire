<div class="card">
    <div class="card-body">
        <div class="flex justify-between items-center border-b border-gray-200">
            {{-- <h3 class="font-bold text-gray-800">Modal Title</h3> --}}
            <h5 class="card-title">{{ $isEdit ? 'Update' : 'Tambah' }} Member</h5>
            @if ($isModal)
                <x-modal.button-close @click=" $dispatch('toggle-modal-member',{ memberModal: false }); $wire.resetForm()" />
            @endif
        </div>

        <form wire:submit.prevent='{{ $isEdit ? 'update' : 'save' }}' @edit-member.window="$focus.focus($refs.inputs)">
            <x-form.input label="Nama Member" wire:model='nama' error-name="nama" x-ref="inputs" />
            <x-form.select label="Profesi" name='profesi' :add-value=true :data-option="$this->profesies" option-label='nama' option-value='id' wire:model='id_profesi'
                           errorName="id_profesi" dropdownParent=""></x-form.select>
            <x-form.input label="Alamat Member" wire:model='alamat' error-name="alamat" />
            <x-form.input label="No. Telepon Member" wire:model='no_hp' error-name="no_hp" />

            <div class="flex gap-3 justify-end mt-6">
                <button type="submit" class="btn btn-sm ">
                    {{ $isEdit ? 'Update' : 'Simpan' }}
                </button>
                @if (!$isModal)
                    <button type="button" @click="$focus.focus($refs.inputs); $wire.resetForm(); " class="btn btn-sm btn-light-error">Batal</button>
                @else
                    <button type="button" @click=" $dispatch('toggle-modal-member',{ memberModal: false }); $wire.resetForm()"
                            class="btn btn-sm btn-light-error">Batal</button>
                @endif
            </div>
        </form>
    </div>
    @can('profesi.create')
        <x-modal name="profesi">
            @livewire('data-master.profesi.profesi-form', ['isModal' => true])
        </x-modal>
    @endcan
    {{-- Success is as dangerous as failure. --}}
</div>
