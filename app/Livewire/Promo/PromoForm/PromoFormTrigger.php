<?php
namespace App\Livewire\Promo\PromoForm;

use App\Helpers\FormatHelper;
use Livewire\Form;

class PromoFormTrigger extends Form
{
    public $nama = '';
    public $deskripsi;

    public $tipe           = ''; // misal: 'BARANG' atau 'TRANSAKSI'
    public bool $is_member = false;
    public $profesi_member = [];
    public $start_date;
    public $end_date;

    public bool $is_aktif = true;
    public $barang_promo  = [];

    public $minimal_belanja;
    public $tipe_transaksi;
    public $diskon_persen;
    public $diskon_nominal;

    public function initializeDefaults(): void
    {
        // Misalnya mau set default tanggal mulai hari ini
        $this->barang_promo[] = [
            'id_barang'       => '',
            'syarat_qty'      => 1,
            'tipe_qty'        => 'MINIMAL',
            'id_satuan'       => null,
            'tipe'            => 'BARANG BONUS',
            'id_barang_bonus' => null,
            'bonus_qty'       => 1,
            'id_satuan_bonus' => null,
            'diskon_persen'   => null,
            'diskon_nominal'  => null,
        ];
    }

    public function setValues($promo = null)
    {
        // $this->reset();
        // dd($promo);
        $this->nama           = $promo ? $promo['nama'] : null;
        $this->deskripsi      = $promo ? $promo['deskripsi'] : null;
        $this->start_date     = $promo ? $promo['start_date'] : null;
        $this->end_date       = $promo ? $promo['end_date'] : null;
        $this->is_aktif       = $promo ? $promo['is_aktif'] : true;
        $this->is_member      = $promo ? $promo['is_member'] : false;
        $this->profesi_member = $promo ? array_column($promo['profesies'], 'id') : [];
        $this->tipe           = $promo ? $promo['tipe'] : 'BARANG';

        if (! $promo) {
            $this->barang_promo = [];
            $this->initializeDefaults();
        } else {
            $this->barang_promo = $promo['promo_barang'];
        }

        $this->minimal_belanja = isset($promo['promo_transaksi']['minimal_belanja']) ? $promo['promo_transaksi']['minimal_belanja'] : null;
        $this->tipe_transaksi  = isset($promo['promo_transaksi']['tipe_transaksi']) ? $promo['promo_transaksi']['tipe_transaksi'] : 'PERSEN';
        $this->diskon_persen   = isset($promo['promo_transaksi']['diskon_persen']) ? $promo['promo_transaksi']['diskon_persen'] : null;
        $this->diskon_nominal  = isset($promo['promo_transaksi']['diskon_nominal']) ? $promo['promo_transaksi']['diskon_nominal'] : null;
        $this->formattedForm();
        // dd($this->form);
    }

    public function formattedForm($isDisplay = true)
    {
        $this->start_date = $isDisplay ? FormatHelper::formatDate($this->start_date) : FormatHelper::formatDateStringToDate($this->start_date);
        $this->end_date   = $isDisplay ? FormatHelper::formatDate($this->end_date) : FormatHelper::formatDateStringToDate($this->end_date);

        if ($this->tipe == 'BARANG') {
            $this->barang_promo = collect($this->barang_promo)->map(function ($item) use ($isDisplay) {
                $item['syarat_qty']     = $isDisplay ? FormatHelper::formatNumberToString($item['syarat_qty']) : FormatHelper::formatStringToNumber($item['syarat_qty']);
                $item['diskon_persen']  = $isDisplay ? FormatHelper::formatNumberToString($item['diskon_persen']) : FormatHelper::formatStringToNumber($item['diskon_persen']);
                $item['diskon_nominal'] = $isDisplay ? FormatHelper::formatNumberToString($item['diskon_nominal']) : FormatHelper::formatStringToNumber($item['diskon_nominal']);
                return $item;
            })->toArray();
        } else if ($this->tipe == 'TRANSAKSI') {
            $this->minimal_belanja = $isDisplay ? FormatHelper::formatNumberToString($this->minimal_belanja) : FormatHelper::formatStringToNumber($this->minimal_belanja);
            $this->diskon_persen   = $isDisplay ? FormatHelper::formatNumberToString($this->diskon_persen) : FormatHelper::formatStringToNumber($this->diskon_persen);
            $this->diskon_nominal  = $isDisplay ? FormatHelper::formatNumberToString($this->diskon_nominal) : FormatHelper::formatStringToNumber($this->diskon_nominal);
        }
    }

    public function rules()
    {
        $rules = [
            'nama'             => 'required|string|max:100',
            'deskripsi'        => 'nullable',
            'tipe'             => 'required|in:BARANG,TRANSAKSI',
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'is_aktif'         => 'required|boolean',
            'profesi_member'   => $this->is_member ? 'required|array|min:1' : 'nullable|array',
            'profesi_member.*' => $this->is_member ? 'required|exists:profesi,id' : 'nullable',
            'is_member'        => 'required|boolean',
        ];

        if ($this->tipe === 'BARANG') {
            $rules = array_merge($rules, [
                'barang_promo.*.id_barang'  => 'required|exists:barang,id',
                'barang_promo.*.syarat_qty' => 'required|numeric|min:1',
                'barang_promo.*.tipe_qty'   => 'required|in:MINIMAL,KELIPATAN',
                'barang_promo.*.id_satuan'  => 'required|exists:satuan,id',
                'barang_promo.*.tipe'       => 'required|in:BARANG BONUS,PERSEN,HARGA']);
            foreach ($this->barang_promo as $index => $promo) {
                if ($promo['tipe'] === 'PERSEN') {
                    $rules = array_merge($rules, [
                        "barang_promo.$index.diskon_persen" => 'required|numeric|min:0|max:100',
                    ]);
                } else if ($promo['tipe'] === 'HARGA') {
                    $rules = array_merge($rules, [
                        "barang_promo.$index.diskon_nominal" => 'required|numeric|min:0',
                    ]);
                } else if ($promo['tipe'] === 'BARANG BONUS') {
                    $rules = array_merge($rules, [
                        "barang_promo.$index.id_barang_bonus" => 'required|exists:barang,id',
                        "barang_promo.$index.bonus_qty"       => 'required|numeric|min:1',
                        "barang_promo.$index.id_satuan_bonus" => 'required|exists:satuan,id',
                    ]);
                }
            }
        } elseif ($this->tipe === 'TRANSAKSI') {
            $rules = array_merge($rules, [
                'minimal_belanja' => 'required|numeric|min:0',
                'tipe_transaksi'  => 'required|in:PERSEN,HARGA',
            ]);
            if ($this->tipe_transaksi === 'HARGA') {
                $rules = array_merge($rules, [
                    'minimal_belanja' => 'required|numeric|min:0',
                ]);
            } else if ($this->tipe_transaksi === 'PERSEN') {
                $rules = array_merge($rules, [
                    'diskon_persen' => 'required|numeric|min:0|max:100',
                ]);
            }
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'numeric'     => 'Harus berupa angka',
            'required'    => 'Input Wajib diisi',
            'required_if' => 'Input Wajib diisi',
            'in'          => 'Input tidak valid',
            'max'         => 'Input tidak boleh lebih dari :max',
            'min'         => 'Input tidak boleh kurang dari :min',
        ];
    }

}