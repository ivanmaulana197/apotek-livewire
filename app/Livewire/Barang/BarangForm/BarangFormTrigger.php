<?php
namespace App\Livewire\Barang\BarangForm;

use App\Enums\ModeHargaEnum;
use App\Helpers\FormatHelper;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class BarangFormTrigger extends Form
{
    public $isEdit = false;
    public $id_barang;

    public $kode,
    $nama,
    $stok,
    $stok_min,
    $id_kategories,
    $id_raks,
    $keterangan,
    $id_satuan_utama,
    $nama_satuan_utama,
    $opsi_satuan = [],
    $harga_beli_default,
    $mode_jual_utama,
    $persen_jual_utama,
    $harga_jual_utama,
    $opsi_harga = [];

    public function setValues($barang = null)
    {
        $this->kode               = $barang['kode'] ?? null;
        $this->nama               = $barang['nama'] ?? null;
        $this->stok               = $barang['stok'] ?? null;
        $this->stok_min           = $barang['stok_min'] ?? null;
        $this->id_satuan_utama    = $barang['id_satuan_utama'] ?? null;
        $this->nama_satuan_utama  = $barang['satuan_utama']['nama'] ?? null;
        $this->keterangan         = $barang['keterangan'] ?? null;
        $this->harga_beli_default = $barang['harga_beli_default'] ?? null;
        $this->mode_jual_utama    = $barang['mode_jual_utama'] ?? null;
        $this->persen_jual_utama  = $barang['persen_jual_utama'] ?? '';
        $this->harga_jual_utama   = $barang['harga_jual_utama'] ?? '';

        $this->id_kategories = isset($barang['kategories']) ? array_column($barang['kategories'], 'id') : [];
        $this->id_raks       = isset($barang['raks']) ? array_column($barang['raks'], 'id') : [];
        if (isset($barang['opsi_satuan'])) {
            $opsiSatuanTanpaPivot = array_map(function ($item) {
                unset($item['pivot']);
                return $item;
            }, $barang['opsi_satuan']);
            $this->opsi_satuan = $opsiSatuanTanpaPivot;
        } else {
            $this->opsi_satuan = [];
        }
        if (isset($barang['opsi_harga'])) {
            $opsiHargaBersih = array_map(function ($item) {
                unset($item['id'], $item['id_barang']);
                return $item;
            }, $barang['opsi_harga']);
            $this->opsi_harga = $opsiHargaBersih;

        } else {
            $this->opsi_harga = [];
        }
        $this->formattedForm(false);
    }

    protected function rules()
    {
        $rules = [
            'kode'                             => 'nullable',
            'nama'                             => ['required', $this->isEdit ? Rule::unique('barang', 'nama')->ignore($this->id_barang) : 'unique:barang,nama'],
            'stok'                             => 'required|numeric',
            'stok_min'                         => 'required|numeric',
            'keterangan'                       => 'nullable|string|max:500',
            'id_kategories'                    => 'required|array',
            'id_kategories.*'                  => 'required|exists:kategori,id',
            'id_raks'                          => 'required|array',
            'id_raks.*'                        => 'required|exists:rak,id',
            'id_satuan_utama'                  => 'required|exists:satuan,id',

            'opsi_satuan.*.id_satuan'          => 'required|exists:satuan,id',
            'opsi_satuan.*.rasio_satuan_utama' => 'required|numeric|min:1',
            'opsi_satuan.*.rasio_satuan_opsi'  => 'required|numeric|min:1',

            'harga_beli_default'               => 'required|numeric|min:0',
            'mode_jual_utama'                  => ['required', new Enum(ModeHargaEnum::class)],

            'opsi_harga.*.jumlah_min'          => 'required|numeric|min:1',
            'opsi_harga.*.id_satuan'           => 'required|exists:satuan,id',
            'opsi_harga.*.mode'                => 'required|in:PERSEN,HARGA',
            'opsi_harga.*.harga_satuan_utama'  => 'required|numeric|min:0',

            // 'mode_diskon'                      => ['required', new Enum(MarkupModeEnum::class)],
        ];

        if ($this->mode_jual_utama == 'PERSEN') {
            $rules['persen_jual_utama'] = 'required|min:0';
            $rules['harga_jual_utama']  = 'numeric|min:0';
        } elseif ($this->mode_jual_utama == 'HARGA') {
            $rules['harga_jual_utama']  = 'required|numeric|min:0';
            $rules['persen_jual_utama'] = 'min:0';
        }

        foreach ($this->opsi_harga as $index => $item) {
            if ($item['mode'] == 'PERSEN') {
                $rules['opsi_harga.' . $index . '.persen'] = 'required|min:0';
                $rules['opsi_harga.' . $index . '.harga']  = 'numeric|min:0';
            } elseif ($item['mode'] == 'HARGA') {
                $rules['opsi_harga.' . $index . '.persen'] = 'min:0';
                $rules['opsi_harga.' . $index . '.harga']  = 'required|numeric|min:0';

            }
        }

        // if ($this->mode_diskon == MarkupModeEnum::PERSEN) {
        //     $rules['persen_diskon'] = 'required|min:0';
        // } elseif ($this->mode_diskon == MarkupModeEnum::HARGA) {
        //     $rules['harga_diskon'] = 'required|numeric|min:0';
        // }

        return $rules;
    }

    protected function messages()
    {
        return [
            'min'      => 'minimal bernilai :min',
            'required' => 'Kolom Wajib Diisi',
            'exists'   => 'Input tidak valid',
            'in'       => 'Input tidak valid',
            'numeric'  => 'Input harus berupa angka',
        ];
    }

    public function formattedForm($serverMode = true)
    {
        $this->stok               = $serverMode ? FormatHelper::formatStringToNumber($this->stok) : FormatHelper::formatNumberToString($this->stok);
        $this->stok_min           = $serverMode ? FormatHelper::formatStringToNumber($this->stok_min) : FormatHelper::formatNumberToString($this->stok_min);
        $this->harga_beli_default = $serverMode ? FormatHelper::formatStringToNumber($this->harga_beli_default) : FormatHelper::formatNumberToString($this->harga_beli_default);
        $this->persen_jual_utama  = $serverMode ? FormatHelper::formatStringToNumber($this->persen_jual_utama) : FormatHelper::formatNumberToString($this->persen_jual_utama);
        $this->harga_jual_utama   = $serverMode ? FormatHelper::formatStringToNumber($this->harga_jual_utama) : FormatHelper::formatNumberToString($this->harga_jual_utama);
        // $this->persen_diskon            = $serverMode ? FormatHelper::formatStringToNumber($this->persen_diskon) : FormatHelper::formatNumberToString($this->persen_diskon);
        // $this->harga_diskon             = $serverMode ? FormatHelper::formatStringToNumber($this->harga_diskon) : FormatHelper::formatNumberToString($this->harga_diskon);

        $this->opsi_satuan = collect($this->opsi_satuan)->map(function ($item) use ($serverMode) {
            $item['rasio_satuan_utama'] = $serverMode ? FormatHelper::formatStringToNumber($item['rasio_satuan_utama']) : FormatHelper::formatNumberToString($item['rasio_satuan_utama']);
            $item['rasio_satuan_opsi']  = $serverMode ? FormatHelper::formatStringToNumber($item['rasio_satuan_opsi']) : FormatHelper::formatNumberToString($item['rasio_satuan_opsi']);
            return $item;
        })->toArray();

        $this->opsi_harga = collect($this->opsi_harga)->map(function ($item) use ($serverMode) {
            $item['jumlah_min']         = $serverMode ? FormatHelper::formatStringToNumber($item['jumlah_min']) : FormatHelper::formatNumberToString($item['jumlah_min']);
            $item['persen']             = $serverMode ? FormatHelper::formatStringToNumber($item['persen']) : FormatHelper::formatNumberToString($item['persen']);
            $item['harga']              = $serverMode ? FormatHelper::formatStringToNumber($item['harga']) : FormatHelper::formatNumberToString($item['harga']);
            $item['harga_satuan_utama'] = $serverMode ? FormatHelper::formatStringToNumber($item['harga_satuan_utama']) : FormatHelper::formatNumberToString($item['harga_satuan_utama']);
            return $item;
        })->toArray();

    }
}