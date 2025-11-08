<?php
namespace App\Livewire\DataMaster\Dokter\DokterDetail;

use App\Models\DataMaster\Dokter;
use Livewire\Component;

class DokterDetailIndex extends Component
{
    public $isEdit;
    public $dokter;
    public $temp;
    public $filter_date;

    public function mount($id_dokter)
    {
        $this->isEdit      = false;
        $this->dokter      = Dokter::find($id_dokter)->toArray();
        $this->temp        = $this->dokter;
        $this->filter_date = [
            'start' => now()->startOfMonth()->format('Y-m-d'),
            'end'   => now()->endOfMonth()->format('Y-m-d'),
        ];
    }

    public function updatedFilterDate($value)
    {
        if (! $value) {
            $this->filter_date = [
                'start' => now()->format('Y-m-d'),
                'end'   => now()->format('Y-m-d'),
            ];
        }
        $this->dispatch('update_value_filter_date', value: $this->filter_date);

    }

    public function edit()
    {
        $this->isEdit = true;
        $this->dispatch('edit-dokter');
    }

    public function cancel()
    {
        $this->isEdit = false;
        $this->dokter = $this->temp;
    }

    public function updateDokter()
    {
        $data = $this->validate([
            'dokter.nama'      => 'required',
            'dokter.spesialis' => 'required',
            'dokter.no_hp'     => 'nullable',
            'dokter.alamat'    => 'nullable',
        ], ['required' => 'Kolom Wajib Diisi']);
        $data              = array_map(fn($value) => $value === '' ? null : $value, $data['dokter']);
        $data['nama']      = strtoupper($data['nama']);
        $data['spesialis'] = strtoupper($data['spesialis']);
        Dokter::where('id', $this->dokter['id'])->update($data);

        $this->isEdit = false;
        $this->dokter = Dokter::find($this->dokter['id'])->toArray();
        $this->temp   = $this->dokter;
    }
    public function render()
    {
        return view('livewire.data-master.dokter.dokter-detail.dokter-detail-index');
    }
}
