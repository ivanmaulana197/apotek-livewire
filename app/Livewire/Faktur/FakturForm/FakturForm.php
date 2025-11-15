<?php
namespace App\Livewire\Faktur\FakturForm;

use App\Helpers\GenerateHelper;
use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
use App\Models\DataMaster\Supplier;
use App\Models\Faktur\Faktur;
use App\Models\Faktur\FakturBarangTemp;
use App\Models\HistoryBarang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class FakturForm extends Component
{
    use WithPagination, WithoutUrlPagination;

    public FakturFormTrigger $form;
    public $id_faktur = null;

    // public $faktur_barang;

    public function mount($id = null)
    {
        if ($id) {
            $this->edit($id);
        } else {
            $this->form->no_faktur = GenerateHelper::generateKwitansiId();
            $this->getFakturBarang();
        }

    }

    #[On('faktur-barang-updated')]
    public function getFakturBarang()
    {
        if ($this->form->isEdit) {
            $this->form->faktur_barang = HistoryBarang::select('id')->whereHas('detail_faktur', function ($query) {
                $query->where('id_faktur', $this->id_faktur);
            })->get()->toArray();
        } else {
            $this->form->faktur_barang = FakturBarangTemp::select('id')->where('id_user', Auth::user()->id)->get()->toArray();
        }
    }

    public function save()
    {
        try {
            $this->form->formattedForm(false);
            $validate = $this->validate();
            DB::beginTransaction();
            $faktur = Faktur::create($validate);

            // ambil semua FakturBarangTemp dengan accessor
            $fakturBarangTemp = FakturBarangTemp::all()->each->append('jumlah_dalam_satuan_utama');
            foreach ($fakturBarangTemp as $item) {
                // bungkus expired ke dalam array sesuai format
                $item->tgl_expired = [
                    [
                        'tgl'    => $item->tgl_expired,
                        'jumlah' => $item->jumlah_dalam_satuan_utama,
                    ],
                ];

                $item->status = 'MASUK';

                // simpan ke faktur_barang
                $history = HistoryBarang::create($item->toArray());
                $history->load('barang:id,nama,kode,id_satuan_utama,stok,stok_min');
                // $history = $faktur->faktur_barang()->create($item->toArray());

                // cek stok batch
                $stokBatchBarang = StokBatchBarang::where('id_barang', $item->id_barang)
                    ->where('tgl_expired', $item->tgl_expired[0]['tgl'])
                    ->first();

                if ($stokBatchBarang) {
                    $stokBatchBarang->increment('jumlah', $item->jumlah_dalam_satuan_utama);
                } else {
                    $stokBatchBarang = StokBatchBarang::create([
                        'id_barang'   => $item->id_barang,
                        'tgl_expired' => $item->tgl_expired[0]['tgl'],
                        'jumlah'      => $item->jumlah_dalam_satuan_utama,
                        'harga_beli'  => $item->harga_beli,
                    ]);
                }

                $faktur->detail_faktur()->create([
                    'id_history_barang' => $history->id,
                    'harga_beli'        => $item->harga_beli,
                ]);

                // update stok barang total
                // $history->barang->increment('stok', $item->jumlah_dalam_satuan_utama);
                $history->barang->update(['stok' => $history->barang->stok + $item->jumlah_dalam_satuan_utama]);
            }
            FakturBarangTemp::where('id_user', Auth::user()->id)->delete();
            DB::commit();
            return $this->redirect(route('faktur'), true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->form->formattedForm();
            throw $e;
        }

    }

    public function edit($id_faktur)
    {
        $faktur          = Faktur::findOrFail($id_faktur);
        $this->id_faktur = $faktur->id;
        $this->form->setValue($faktur);
    }

    public function update()
    {
        $this->form->formattedForm(false);
        $validate = $this->validate();
        // dd($validate);
        try {
            DB::beginTransaction();
            $faktur = Faktur::findOrFail($this->id_faktur);
            $faktur->update($validate);
            DB::commit();
            return $this->redirect(route('faktur'), true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->form->formattedForm();
            throw $e;
        }

    }

    public function render()
    {
        return view('livewire.faktur.faktur-form.faktur-form');
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function suppliers()
    {
        return Supplier::select('id', 'nama')->get();
    }
}