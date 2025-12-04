<x-slot:title>Data Role</x-slot:title>

<div class="max-w-full">
    <x-breadcrumb name="Detail Role">
        <x-slot:button>
            <a class="btn btn-xs bg-slate-500 hover:bg-slate-700" href="{{ route('data-master.role') }}" wire:navigate>
                <span class="iconify famicons--caret-back"></span>
                Kembali
            </a>
        </x-slot:button>
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
            <x-breadcrumb.before name="Data Role" href="{{ route('data-master.role') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="save">
                <h5 class="card-title">{{ $isEdit ? 'Update' : 'Tambah' }} Role</h5>
                <x-form.input label="Nama Role" wire:model='name' error-name="name" x-ref="inputs" />

                <h3 class="font-semibold mb-2">Permission:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($permissionList as $group => $items)
                        <div class="border rounded p-3">
                            <h4 class="font-bold text-blue-600 mb-2 uppercase">
                                {{ str_replace('-', ' ', $group) }}
                            </h4>

                            <div class="flex flex-col gap-1">
                                @foreach ($items as $p)
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" wire:model="selected_permission" value="{{ $p->name }}">
                                        {{ $p->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-3 justify-end mt-6">
                    <button type="submit" class="btn btn-sm">
                        {{ $isEdit ? 'Update' : 'Simpan' }}
                    </button>
                    <a href="{{ route('data-master.role') }}" wire:navigate class="btn btn-sm btn-light-error">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
