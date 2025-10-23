<?php
namespace App\Livewire\Faktur\FakturForm;

use App\Helpers\FormatHelper;
use Livewire\Form;

class FakturBarangFormTrigger extends Form
{
    public $isEdit = false;

    public $faktur_barang;


    protected function rules()
    {
        return [
            'faktur_barang.*.id_barang'   => 'required|exists:barang,id',
            'faktur_barang.*.jumlah'      => 'required|numeric',
            'faktur_barang.*.id_satuan'   => 'required|exists:satuan,id',
            'faktur_barang.*.tgl_expired' => 'required|date|after_or_equal:today',
        ];
    }



}
