<?php
namespace App\Livewire\Faktur\FakturForm;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
use App\Models\Faktur\DetailFakturBarang;
use App\Models\Faktur\FakturBarangTemp;
use App\Models\HistoryBarang;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class FakturBarangForm extends Component
{
    // public FakturBarangFormTrigger $form;
    public $faktur_barang;
    public $isEdit = false;
    public $id_faktur;

    public function mount($id_faktur = null)
    {
        if ($id_faktur) {
            $this->id_faktur = $id_faktur;
            $this->isEdit    = true;
        }
        $this->faktur_barang = $this->getAllDataFakturBarang();
        // dd($this->faktur_barang);
        foreach ($this->faktur_barang as $index => $item) {
            $this->faktur_barang[$index]['isEdit'] = false;
            $this->formattedForm($index);
        }
    }

    public function getAllDataFakturBarang()
    {
        if ($this->id_faktur) {
            //edit the faktur
            $data = HistoryBarang::whereHas('detail_faktur', function ($q) {
                $q->where('id_faktur', $this->id_faktur);
            })->with([
                'detail_faktur:id,id_history_barang,id_faktur,harga_beli',
                'barang:id,kode,nama,stok,id_satuan_utama',
                'satuan:id,nama',
            ])
                ->get()
                ->each(function ($item) {
                    $item->tgl_expired = $item->tgl_expired[0]['tgl'];
                    $item->harga_beli  = $item->detail_faktur->harga_beli;
                    $item->barang->append('all_satuan');
                })->toArray();
        } else {
            //create new faktur
            $data = FakturBarangTemp::with([
                'barang:id,id_satuan_utama,kode,nama,stok,harga_beli_default,harga_jual_utama',
                'satuan:id,nama',
            ])
                ->where('id_user', Auth::id())
                ->get()
                ->each(function ($item) {
                    $item->barang->append('all_satuan'); // 👈 append di relasi
                })->toArray();
        }

        return $data;
    }

    public function getDataFakturBarang($id)
    {
        if ($this->isEdit) {
            $data = HistoryBarang::whereHas('detail_faktur', function ($q) {
                $q->where('id_faktur', $this->id_faktur);
            })->with([
                'detail_faktur:id,id_history_barang,id_faktur,harga_beli',
                'barang:id,kode,nama,stok,id_satuan_utama',
                'satuan:id,nama',
            ])
                ->findOrFail($id);

            $data->tgl_expired = $data->tgl_expired[0]['tgl'];
            $data->harga_beli  = $data->detail_faktur->harga_beli;

        } else {
            $data = FakturBarangTemp::with([
                'barang:id,id_satuan_utama,kode,nama,stok,harga_beli_default,harga_jual_utama',
                'satuan:id,nama',
            ])->findOrFail($id);
        }
        if ($data->barang) {
            $data->barang->append('all_satuan');
        }
        $data           = $data->toArray();
        $data['isEdit'] = false;
        return $data;
    }

    #[On('select-barang-faktur')]
    public function selectBarangFaktur($id_barang, $index)
    {
        $this->faktur_barang[$index]['id_barang'] = $id_barang;
        $this->faktur_barang[$index]['barang']    = Barang::find($id_barang)->append('all_satuan')->toArray();
        $this->faktur_barang[$index]['id_satuan'] = $this->faktur_barang[$index]['barang']['all_satuan'][0]['id_satuan'];
        $this->dispatch('update_satuan_barang_' . $index,
            data: $this->faktur_barang[$index]['barang']['all_satuan'],
            selected: $this->faktur_barang[$index]['id_satuan']);
    }

    public function createOrUpdateFakturBarang($index)
    {
        try {
            $data = $this->faktur_barang[$index]['id'] ?? null;
            $this->formattedForm($index, false);
            // dd($this->faktur_barang[$index], $this->isEdit);
            $this->validate([
                'faktur_barang.' . $index . '.id_barang'   => 'required|exists:barang,id',
                'faktur_barang.' . $index . '.id_satuan'   => 'required|exists:satuan,id',
                'faktur_barang.' . $index . '.jumlah'      => 'required|numeric',
                'faktur_barang.' . $index . '.tgl_expired' => 'required|date|after_or_equal:today',
                'faktur_barang.' . $index . '.harga_beli'  => 'nullable|numeric',
            ], ['required' => 'Kolom Wajib Diisi', 'date' => 'Format Tanggal Tidak Valid', 'after_or_equal' => 'Tanggal Expired harus sama atau Lebih Besar dari Hari Ini']);
            if ($data) {
                if ($this->isEdit) {
                    $this->removeHistoryBarang($this->faktur_barang[$index]['id']);
                    $data = $this->saveHistoryBarang($index);

                } else {
                    FakturBarangTemp::find($data)->update($this->faktur_barang[$index]);
                }
            } else {
                if ($this->isEdit) {
                    $data = $this->saveHistoryBarang($index);
                } else {
                    $this->faktur_barang[$index]['id_user'] = Auth::user()->id;
                    $fakturbarang                           = FakturBarangTemp::create($this->faktur_barang[$index]);
                    $data                                   = $fakturbarang->id;
                }
            }
            $this->faktur_barang[$index] = $this->getDataFakturBarang($data);
            $this->formattedForm($index);
            $this->dispatch('faktur-barang-updated');
            $this->dispatch('alert', status: 'success', message: 'Berhasil Menyimpan Barang');
        } catch (\Exception $e) {
            $this->formattedForm($index);
            $this->dispatch('alert', status: 'error', message: 'Gagal Menyimpan Barang', subtitle: $e->getMessage());
            throw $e;
        }
    }

    public function removeFakturBarang($index)
    {
        // dd($this->faktur_barang);
        try {
            if ($this->isEdit) {
                $this->removeHistoryBarang($this->faktur_barang[$index]['id']);
            } else {
                FakturBarangTemp::findOrFail($this->faktur_barang[$index]['id'])->delete();
            }
            $this->dispatch('alert', status: 'success', message: 'Berhasil Menghapus Barang ke Faktur');
            $this->dispatch('faktur-barang-updated');
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false];
        }
    }

    public function saveHistoryBarang($index)
    {
        $this->faktur_barang[$index]['status']  = 'MASUK';
        $this->faktur_barang[$index]['id_user'] = Auth::user()->id;
        $historyBarang                          = HistoryBarang::create($this->faktur_barang[$index]);
        $historyBarang->append('jumlah_dalam_satuan_utama');
        $historyBarang->update(['tgl_expired' => [[
            'tgl'    => $this->faktur_barang[$index]['tgl_expired'],
            'jumlah' => $historyBarang->jumlah_dalam_satuan_utama,
        ]]]);
        $stokBatchBarang = StokBatchBarang::where('id_barang', $historyBarang->id_barang)
            ->where('tgl_expired', $historyBarang->tgl_expired[0]['tgl'])
            ->first();

        if ($stokBatchBarang) {
            $stokBatchBarang->increment('jumlah', $historyBarang->jumlah_dalam_satuan_utama);
        } else {
            $stokBatchBarang = StokBatchBarang::create([
                'id_barang'   => $historyBarang->id_barang,
                'tgl_expired' => $historyBarang->tgl_expired[0]['tgl'],
                'jumlah'      => $historyBarang->jumlah_dalam_satuan_utama,
                'harga_beli'  => $historyBarang->harga_beli,
            ]);
        }
        // detail faktur barang
        DetailFakturBarang::create([
            'id_history_barang' => $historyBarang->id,
            'id_faktur'         => $this->id_faktur,
            'harga_beli'        => $this->faktur_barang[$index]['harga_beli'], // <- ini harus ada field di $item
        ]);
        $historyBarang->load('barang:id,nama,kode,id_satuan_utama,stok,stok_min');
        // $historyBarang->barang()->increment('stok', $historyBarang->jumlah_dalam_satuan_utama);
        $historyBarang->barang->update(['stok' => $historyBarang->barang->stok + $historyBarang->jumlah_dalam_satuan_utama]);
        return $historyBarang->id;
    }

    public function removeHistoryBarang($id_history_barang)
    {
        $history         = HistoryBarang::with(['detail_faktur', 'barang:id,nama,kode,id_satuan_utama,stok,stok_min', 'barang.opsi_satuan'])->findOrFail($id_history_barang)->append('jumlah_dalam_satuan_utama');
        $tgl_expired     = $history->tgl_expired[0]['tgl'];
        $stokBatchBarang = StokBatchBarang::where('id_barang', $history->id_barang)
            ->where('tgl_expired', $tgl_expired)
            ->first();
        if ($stokBatchBarang) {
            $stokBatchBarang->decrement('jumlah', $history->jumlah_dalam_satuan_utama);
            if ($stokBatchBarang->jumlah == 0) {
                $stokBatchBarang->delete();
            }
        }
        $history->detail_faktur->delete();
        // $history->barang()->decrement('stok', $history->jumlah_dalam_satuan_utama);
        $history->barang->update(['stok' => $history->barang->stok - $history->jumlah_dalam_satuan_utama]);
        $history->delete();
    }

    public function render()
    {
        return view('livewire.faktur.faktur-form.faktur-barang-form');
    }

    public function formattedForm($index, $display = true)
    {
        $this->faktur_barang[$index]['jumlah']      = $display ? FormatHelper::formatNumberToString($this->faktur_barang[$index]['jumlah']) : FormatHelper::formatStringToNumber($this->faktur_barang[$index]['jumlah']);
        $this->faktur_barang[$index]['harga_beli']  = $display ? FormatHelper::formatNumberToString($this->faktur_barang[$index]['harga_beli']) : FormatHelper::formatStringToNumber($this->faktur_barang[$index]['harga_beli']);
        $this->faktur_barang[$index]['tgl_expired'] = $display ? FormatHelper::formatDate($this->faktur_barang[$index]['tgl_expired']) : FormatHelper::formatDateStringToDate($this->faktur_barang[$index]['tgl_expired']);

    }
}
