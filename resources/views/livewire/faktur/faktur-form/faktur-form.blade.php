<x-slot:title>Form Faktur Masuk</x-slot:title>

@php
    use App\Helpers\FormatHelper;
@endphp

@script
    <script>
        Alpine.data('fakturForm', () => ({
            no_faktur: $wire.entangle('form.no_faktur'),
            id_supplier: $wire.entangle('form.id_supplier'),
            total_biaya: $wire.entangle('form.total_biaya'),
            status: $wire.entangle('form.status'),
            tgl_jatuh_tempo: $wire.entangle('form.tgl_jatuh_tempo'),
            faktur_barang: $wire.entangle('form.faktur_barang'),
        }));
    </script>
@endscript

<div>
    <x-breadcrumb name="{{ $form->isEdit ? 'Edit' : 'Tambah' }} Faktur Masuk">
        <x-slot:before>
            <x-breadcrumb.before name="Home" href="{{ route('home') }}"></x-breadcrumb.before>
            <x-breadcrumb.before name="Data Faktur Masuk" href="{{ route('faktur') }}"></x-breadcrumb.before>
        </x-slot:before>
    </x-breadcrumb>

    <div class="card mb-2">
        <div class="card-body">
                <form x-data="fakturForm" wire:submit.prevent='{{ $form->isEdit ? 'update' : 'save' }}'>
                <div class="grid grid-cols-12 gap-x-2">
                    <div class="col-span-12 sm:col-span-6">
                        <x-form.input label="No Kwitansi/No Faktur" errorName="form.no_faktur" x-model='no_faktur' />
                        <x-badge message="Ganti No Kwitansi jika terdapat No Kwitasi/Nota Pembelian" />
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <x-form.select-alpine label="Supplier" name="supplier" :data-option="$this->suppliers" option-value="id" option-label="nama" modal-name="supplier"
                                              :add-value=true x-model="id_supplier" error-name="form.id_supplier" />
                    </div>
                    <div class="col-span-12 mb-2">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <x-form.input label="Biaya Belanja" labelInputGroup="Rp" inputGroup="left" errorName="form.total_biaya"
                                          onkeyup="formatForInputNumber(this)" x-model='total_biaya' />

                            <x-alpine.form.input-date label="Tanggal Jatuh Tempo:" name="tgl_jatuh_tempo" ::id="'tgl_jatuh_tempo'" :singleDatePicker=true
                                                      x-model="tgl_jatuh_tempo" errorName="form.tgl_jatuh_tempo" :ranges=false></x-alpine.form.input-date>

                            <x-form.select-alpine name="status" label="Status" :empty-option=false x-model="status" error-name="form.status">
                                <x-slot:custom-option>
                                    <option value="LUNAS">LUNAS</option>
                                    <option value="BELUM LUNAS">BELUM LUNAS</option>
                                </x-slot:custom-option>
                            </x-form.select-alpine>
                        </div>
                    </div>
                    @error('faktur_barang')
                        <div class="col-span-12">
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn bg-primary hover:bg-primaryemphasis">
                    {{ $form->isEdit ? 'Update' : 'Simpan' }}
                </button>
                <a href="{{ route('faktur') }}" class="btn bg-gray-200 border hover:bg-gray-400 text-black" wire:navigate>
                    Batal
                </a>
            </form>
            </div>
        </div>

    <div class="card shadow-md mb-2">
        <div class="card-body">
            <h2 class="card-title">Form Barang Masuk <a href="{{ route('data.barang.tambah') }}" class="btn btn-sm " wire:navigate>+ Barang</a></h2>
            {{-- @livewire('faktur.faktur-form.faktur-barang-form', []) --}}
            <livewire:faktur.faktur-form.faktur-barang-form :id_faktur="$form->id_faktur" />
        </div>
    </div>

    @error('form.faktur_barang')
        <div class="col-span-12">
            <span class="text-red-500 text-sm">{{ $message }}</span>
        </div>
    @enderror

    <x-modal name="supplier">
        @livewire('data-master.supplier.supplier-form', ['isModal' => true])
    </x-modal>

</div>
