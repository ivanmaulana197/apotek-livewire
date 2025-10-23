<?php
namespace App\Livewire\Faktur\FakturForm;

use App\Helpers\GenerateHelper;
use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
use App\Models\Barang\StokBatchFakturBarang;
use App\Models\DataMaster\Supplier;
use App\Models\Faktur\DetailFakturBarang;
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


    // public $faktur_barang;

    public function mount($id=null)
    {
        if($id){
            $this->edit($id);
        }else{
            $this->form->no_faktur = GenerateHelper::generateKwitansiId();
            $this->getFakturBarang();
        }

    }

    #[On('faktur-barang-updated')]
    public function getFakturBarang()
    {
        if($this->form->isEdit){
            $this->form->faktur_barang = HistoryBarang::select('id')->get()->toArray();
        }else{
            $this->form->faktur_barang = FakturBarangTemp::select('id')->get()->toArray();
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
            $fakturBarangTemp = FakturBarangTemp::all()
                ->each
                ->append('jumlah_dalam_satuan_utama');

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
                $history = $faktur->faktur_barang()->create($item->toArray());
                // dd($item->tgl_expired[0]['tgl']);


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

                // detail faktur barang
                DetailFakturBarang::create([
                    'id_history_barang' => $history->id,
                    'id_stok_batch_barang' => $stokBatchBarang->id,
                    'harga_beli'        => $item->harga_jual_utama, // <- ini harus ada field di $item
                ]);

                // update stok barang total
                $barang = Barang::findOrFail($item->id_barang);
                $barang->increment('stok', $item->jumlah_dalam_satuan_utama);
            }
            FakturBarangTemp::where('id_user', Auth::user()->id)->delete();
            DB::commit();
            return $this->redirect(route('faktur'), true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->form->formattedForm();
            throw $e;
            // dd($e->getMessage());
        }

    }

    public function edit($id_faktur){
        $faktur = Faktur::findOrFail($id_faktur);
        $this->form->setValue($faktur);
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