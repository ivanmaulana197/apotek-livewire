<div class="card">
    <div class="card-body">
        <div class="flex justify-between items-center border-b border-gray-200">
            {{-- <h3 class="font-bold text-gray-800">Modal Title</h3> --}}
            <h5 class="card-title">{{ $isEdit ? 'Update' : 'Tambah' }} User</h5>
            @if ($isModal)
                <x-modal.button-close @click="$dispatch('toggle-modal-user',{ userModal: false }); $wire.resetForm()" />
            @endif
        </div>

        <form wire:submit.prevent='{{ $isEdit ? 'update' : 'save' }}' @edit-user.window="$focus.focus($refs.inputs)">
            <x-form.input label="Nama User" wire:model='name' error-name="name" x-ref="inputs" />
            <x-form.input label="Email User" wire:model='email' error-name="email" />
            <x-form.select label="Role User" multiple name='roles_selected' placeholder="Role" :data-option="$this->roles" option-label='name' option-value='id'
                           wire:model='roles_selected' errorName="roles_selected.*"></x-form.select>
            @if (!$isEdit)
                <x-form.input label="Password User" type="password" wire:model='password' error-name="password" />
                <x-form.input label="Password Confirm User" type="password" wire:model='password_confirmation' error-name="password_confirmation" />
            @endif

            <div class="flex gap-3 justify-end mt-6">
                <button type="submit" class="btn btn-sm">
                    {{ $isEdit ? 'Update' : 'Simpan' }}
                </button>
                @if (!$isModal)
                    <button type="button" @click="$focus.focus($refs.inputs); $wire.resetForm()" class="btn btn-sm btn-light-error">Batal</button>
                @else
                    <button type="button" @click="$dispatch('toggle-modal-user',{ userModal: false }); $wire.resetForm()"
                            class="btn btn-sm btn-light-error">Batal</button>
                @endif
            </div>
        </form>
    </div>
    {{-- Success is as dangerous as failure. --}}
</div>
