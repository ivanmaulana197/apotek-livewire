<?php
namespace App\Livewire\DataMaster\MetodePembayaran;

use App\Models\DataMaster\MetodePembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class MetodePembayaranForm extends Component
{
    public $isEdit = false;
    public MetodePembayaran $metodePembayaran;
    public $isModal = false;

    public $nama;

    protected function rules()
    {
        return [
            'nama' => ['required', $this->isEdit ? Rule::unique('metode_pembayaran', 'nama')->ignore($this->metodePembayaran->id) : 'unique:metode_pembayaran,nama'],
        ];
    }
    public function save()
    {
        if (! Auth::user()->can('metode-pembayaran.create')) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        try {
            $validate = $this->validate();
            DB::BeginTransaction();
            $metodePembayaran = MetodePembayaran::create($validate);
            DB::commit();
            $this->dispatch('toggle-modal-metodePembayaran', metodePembayaranModal: false);
            $this->dispatch('alert', status: 'success', message: 'Berhasil Menambahkan Metode Pembayaran');
            $this->resetForm();
            $this->dispatch('metodePembayaran-created', metodePembayaran: $metodePembayaran);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error
            $this->dispatch('alert', status: 'error', message: 'Gagal Menambahkan Metode Pembayaran', subtitle: $e->getMessage());
            throw $e;
        }
    }

    #[On('edit-metode_pembayaran')]
    public function editFormMetodePembayaran(MetodePembayaran $metodePembayaran)
    {
        if (! Auth::user()->can('metode-pembayaran.edit')) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        $this->isEdit           = true;
        $this->metodePembayaran = $metodePembayaran;
        $this->nama             = $metodePembayaran->nama;
    }

    public function update()
    {
        if (! Auth::user()->can('metode-pembayaran.edit')) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        try {
            $validate = $this->validate();
            DB::BeginTransaction();
            $metodePembayaran = tap($this->metodePembayaran)->update($validate);
            DB::commit();
            $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate Supplier');
            $this->resetForm();
            $this->dispatch('metodePembayaran-created', metodePembayaran: $metodePembayaran->id);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error
            $this->dispatch('alert', status: 'error', message: 'Gagal Mengupdate Supplier', subtitle: $e->getMessage());
            throw $e;
        }
    }

    #[On('close-modal-metode_pembayaran')]
    public function resetForm()
    {
        $this->isEdit = false;
        $this->dispatch('clear-select-asal');
        $this->metodePembayaran = new MetodePembayaran();
        $this->nama             = null;
    }

    public function render()
    {
        return view('livewire.data-master.metode-pembayaran.metode-pembayaran-form');
    }
}
