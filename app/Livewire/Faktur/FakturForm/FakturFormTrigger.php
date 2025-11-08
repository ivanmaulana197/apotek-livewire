<?php
namespace App\Livewire\Faktur\FakturForm;

use App\Helpers\FormatHelper;
use Livewire\Form;

class FakturFormTrigger extends Form
{
    public $isEdit = false, $id_faktur;

    public $no_faktur,
    $id_supplier,
    $total_biaya,
    $tgl_jatuh_tempo,
    $status = 'LUNAS';

    public $faktur_barang;

    public function setValue($faktur)
    {
        $this->no_faktur       = $faktur->no_faktur;
        $this->id_supplier     = $faktur->id_supplier;
        $this->total_biaya     = $faktur->total_biaya;
        $this->tgl_jatuh_tempo = $faktur->tgl_jatuh_tempo;
        $this->status          = $faktur->status;
        $this->isEdit          = true;
        $this->id_faktur       = $faktur->id;
        $this->formattedForm();
    }

    public function formattedForm($display = true)
    {
        $this->total_biaya     = $display ? FormatHelper::formatNumberToString($this->total_biaya) : FormatHelper::formatStringToNumber($this->total_biaya);
        $this->tgl_jatuh_tempo = $display ? FormatHelper::formatDate($this->tgl_jatuh_tempo) : FormatHelper::formatDateStringToDate($this->tgl_jatuh_tempo);
    }

    protected function rules()
    {
        $rules = [
            'no_faktur'       => 'required',
            'id_supplier'     => 'required|exists:supplier,id',
            'total_biaya'     => 'required|numeric',
            'status'          => 'required',
            'tgl_jatuh_tempo' => $this->status == 'BELUM LUNAS' ? 'required|date' : 'nullable',
        ];

        if (! $this->isEdit) {
            $rules = array_merge($rules, [
                'faktur_barang' => 'required|array|min:1',
            ]);
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'faktur_barang.required' => 'Barang tidak boleh kosong',
            'faktur_barang.array'    => 'Barang tidak boleh kosong',
        ];
    }

}
