<?php
namespace App\Livewire\Barang\BarangForm;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Models\DataMaster\Satuan;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BarangForm extends Component
{
    public BarangFormTrigger $form;
    public $satuans;

    public function mount($id = null)
    {
        $this->satuans                 = Satuan::select('id', 'nama')->get()->toArray();
        $this->form->persen_jual_utama = FormatHelper::formatNumberToString(Setting::first()->persentase_jual);
        if ($id) {
            $this->edit($id);
        }
    }

    public function save()
    {
        try {
            $this->form->formattedForm();
            $data = $this->validate();

            DB::BeginTransaction();
            $barang = Barang::create($data);
            foreach ($data['id_raks'] as $rak) {
                $barang->raks()->attach($rak);
            }
            foreach ($data['id_kategories'] as $kategori) {
                $barang->kategories()->attach($kategori);
            }
            if (isset($data['opsi_satuan'])) {
                foreach ($data['opsi_satuan'] as $opsi_satuan) {
                    $barang->opsi_satuan()->attach($opsi_satuan['id_satuan'], [
                        'rasio_satuan_utama' => $opsi_satuan['rasio_satuan_utama'],
                        'rasio_satuan_opsi'  => $opsi_satuan['rasio_satuan_opsi'],
                    ]);
                }
            }

            // harga barang utama
            if (isset($data['opsi_harga'])) {
                foreach ($data['opsi_harga'] as $opsi_harga) {
                    $barang->opsi_harga()->create($opsi_harga);
                }
            }
            DB::commit();
            session()->flash('alert', [
                'title'   => 'Berhasil!',
                'message' => 'Berhasil Menambahkan Barang',
                'icon'    => 'success', // Bisa 'success', 'error', 'warning', dll.
            ]);
            return $this->redirect(route('data.barang'), true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->form->formattedForm(false);
            $this->dispatch('alert', status: 'error', message: 'Gagal Menambahkan Barang', subtitle: $e->getMessage());
            throw $e;
        }

    }

    public function edit($id_barang)
    {
        $barang = Barang::with([
            'kategories'  => function ($q) {
                $q->select('id')->pluck('id')->toArray();
            },
            'raks'        => function ($q) {
                $q->select('id')->pluck('id')->toArray();
            },
            'satuan_utama:id,nama',
            'opsi_satuan' => function ($q) {
                $q->select('id_satuan', 'rasio_satuan_utama', 'rasio_satuan_opsi')->pluck('id_satuan')->toArray();
            },
            'opsi_harga'  => function ($q) {
                $q->select(
                    'harga_barang.id',
                    'harga_barang.id_barang',
                    'harga_barang.jumlah_min',
                    'harga_barang.id_satuan',
                    'harga_barang.mode',
                    'harga_barang.persen',
                    'harga_barang.harga',
                    'harga_barang.harga_satuan_utama',
                    'satuan.nama as nama_satuan'
                )
                    ->join('satuan', 'satuan.id', '=', 'harga_barang.id_satuan');
            },

        ])->findOrFail($id_barang)->toArray();
        $this->form->isEdit    = true;
        $this->form->id_barang = $id_barang;
        $this->form->setValues($barang);
    }

    public function update()
    {
        try {
            $this->form->formattedForm();
            $data = $this->validate();
            // dd($data['opsi_harga']);
            $barang = Barang::findOrFail($this->form->id_barang);

            DB::BeginTransaction();
            $barang->update($data);

            // Update relasi rak
            $barang->kategories()->sync($data['id_kategories']);
            $barang->raks()->sync($data['id_raks']);
            if (isset($data['opsi_satuan'])) {
                $data_satuan = collect($data['opsi_satuan'])
                    ->mapWithKeys(function ($value) {
                        return [
                            $value['id_satuan'] => [
                                'rasio_satuan_utama' => $value['rasio_satuan_utama'],
                                'rasio_satuan_opsi'  => $value['rasio_satuan_opsi'],
                            ],
                        ];
                    })
                    ->toArray();
                $barang->opsi_satuan()->sync($data_satuan);
            } else {
                $barang->opsi_satuan()->detach();
            }

            $barang->opsi_harga()->delete();
            if (isset($data['opsi_harga'])) {
                foreach ($data['opsi_harga'] as $opsi_harga) {
                    $barang->opsi_harga()->create($opsi_harga);
                }
            }

            DB::commit();
            session()->flash('alert', [
                'title'   => 'Berhasil!',
                'message' => 'Berhasil Mengupdate Barang',
                'icon'    => 'success', // Bisa 'success', 'error', 'warning', dll.
            ]);
            return $this->redirect(route('data.barang'), true);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error
            $this->form->formattedForm(false);
            $this->dispatch('alert', status: 'error', message: $e->getMessage());
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.barang.barang-form.barang-form');
    }

    public function fetchNamaSatuan($id_satuan)
    {
        $data = Satuan::select('nama')->findOrFail($id_satuan);
        // $this->form->nama_satuan_utama = $data->nama;
        return $data->nama;
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function kategories()
    {
        return Kategori::select('id', 'nama')->get();
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function raks()
    {
        return Rak::select('id', 'nama')->get();
    }
}
