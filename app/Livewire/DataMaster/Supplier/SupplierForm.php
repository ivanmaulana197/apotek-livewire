<?php
namespace App\Livewire\DataMaster\Supplier;

use App\Enums\SupplierAsalEnum;
use App\Models\DataMaster\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\On;
use Livewire\Component;

class SupplierForm extends Component
{
    public $isEdit = false;
    public Supplier $supplier;

    public $nama, $asal, $alamat, $no_hp, $is_pajak = true;

    public $supplierAsalEnum;

    public function updatedAsal($value)
    {
        $this->asal = $value == '' ? null : $value;
    }

    protected function rules()
    {
        return [
            'nama'     => ['required', $this->isEdit ? Rule::unique('supplier', 'nama')->ignore($this->supplier->id) : 'unique:supplier,nama'],
            'asal'     => ['required', new Enum(SupplierAsalEnum::class)],
            'alamat'   => ['nullable'],
            'no_hp'    => ['nullable', 'numeric'],
            'is_pajak' => ['required'],
        ];
    }
    public function save()
    {
        try {
            $validate = $this->validate();
            DB::BeginTransaction();
            $supplier = Supplier::create($validate);
            DB::commit();
            $this->dispatch('toggle-modal-supplier', supplierModal: false);
            $this->dispatch('alert', status: 'success', message: 'Berhasil Menambahkan Supplier');
            $this->resetForm();
            $this->dispatch('supplier-created', supplier: $supplier);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error
            $this->dispatch('alert', status: 'error', message: 'Gagal Menambahkan Supplier', subtitle: $e->getMessage());
            throw $e;
        }
    }

    #[On('edit-supplier')]
    public function editFormSupplier(Supplier $supplier)
    {
        $this->isEdit   = true;
        $this->supplier = $supplier;
        $this->nama     = $supplier->nama;
        $this->asal     = $supplier->asal;
        $this->dispatch('selected-asal', selected: $supplier->asal);
        $this->alamat   = $supplier->alamat;
        $this->no_hp    = $supplier->no_hp;
        $this->is_pajak = $supplier->is_pajak;
    }

    public function update()
    {
        try {
            $validate = $this->validate();
            DB::BeginTransaction();
            $supplier = tap($this->supplier)->update($validate);
            DB::commit();
            $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate Supplier');
            $this->resetForm();
            $this->dispatch('supplier-created', supplier: $supplier->id);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error
            $this->dispatch('alert', status: 'error', message: 'Gagal Mengupdate Supplier', subtitle: $e->getMessage());
            throw $e;
        }
    }

    public $isModal = false;

    #[On('close-modal-supplier')]
    public function resetForm()
    {
        $this->isEdit = false;
        $this->dispatch('clear-select-asal');
        $this->supplier = new Supplier();
        $this->nama     = null;
        $this->asal     = null;
        $this->alamat   = null;
        $this->no_hp    = null;
        $this->is_pajak = true;
    }

    public function render()
    {
        $this->supplierAsalEnum = SupplierAsalEnum::toArray();
        return view('livewire.data-master.supplier.supplier-form');
    }
}