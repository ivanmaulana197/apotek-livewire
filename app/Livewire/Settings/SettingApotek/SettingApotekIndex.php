<?php

namespace App\Livewire\Settings\SettingApotek;

use App\Helpers\FormatHelper;
use App\Models\Setting;
use Livewire\Component;

class SettingApotekIndex extends Component
{
    public $setting;

    // public function mount(){

    // }

    public function formatterNumber($isDisplay = true)
    {
        $formatNumber = ['pembulatan_harga_barang', 'pembulatan_resep', 'pembulatan_total', 'persentase_jual', 'persentase_jual_resep'];
        foreach ($formatNumber as $key => $value) {
            $this->setting[$value] = $isDisplay ? FormatHelper::formatNumberToString($this->setting[$value]) : FormatHelper::formatStringToNumber($this->setting[$value]);
        }
    }

    public function save()
    {
        try {
            $this->formatterNumber(false);
            $this->validate([
                'setting.nama' => 'required',
                'setting.alamat' => 'required',
                'setting.kabupaten' => 'required',
                'setting.email' => 'required',
                'setting.no_telp' => 'required',
                'setting.pembulatan_harga_barang' => 'required|numeric',
                'setting.pembulatan_resep' => 'required|numeric',
                'setting.pembulatan_total' => 'required|numeric',
                'setting.persentase_jual' => 'required|numeric',
                'setting.persentase_jual_resep' => 'required|numeric',
            ],[
                'required' => 'Input Wajib Di Isi'
            ]);
            Setting::first()->update($this->setting);
            $this->dispatch('alert', status: 'success', message: 'Berhasil Mengupdate Setting');
            $this->formatterNumber('formatNumberToString');
        } catch (\Exception $th) {
            $this->formatterNumber(true);
            $this->dispatch('alert', status: 'error', message: 'Gagal Menyimpan Setting', subtitle: $th->getMessage());
            throw $th;
        }
    }

    public function render()
    {
        $this->setting = Setting::first()->toArray();
        $this->formatterNumber('formatNumberToString');
        return view('livewire.settings.setting-apotek.setting-apotek-index', [
            'setting'=>$this->setting
        ]);
    }
}