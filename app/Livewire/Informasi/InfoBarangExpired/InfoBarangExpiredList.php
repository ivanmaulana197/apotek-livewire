<?php
namespace App\Livewire\Informasi\InfoBarangExpired;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use App\Models\Barang\StokBatchBarang;
use App\Models\HistoryBarang;
use App\Traits\CustomDataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class InfoBarangExpiredList extends Component
{
    use WithPagination, CustomDataTable;
    use WithoutUrlPagination;
    public $jumlah_dilaporkan = [];
    public $status            = [];
    public $new_tgl_expired   = [];

    public $ceklistStokBarang = [];
    public $new_tgl_expired_ceklist, $status_ceklist;
    public $minimum_alert;
    public $id_barang;

    public function mount($minimum_alert = false)
    {
        $this->defaultSortBy  = 'tgl_expired';
        $this->sortBy         = 'tgl_expired';
        $this->defaultSortDir = 'asc';
        $this->sortDir        = 'asc';
        if ($minimum_alert) {
            $this->minimum_alert = $minimum_alert;
        } else {
            $this->minimum_alert = Carbon::now()->addMonths(3);
        }
    }

    public function submit($id_stok_barang)
    {
        try {
            // dd($this->jumlah_dilaporkan, $this->status, $this->new_tgl_expired);
            $stokBatchBarang = StokBatchBarang::with('barang:id,id_satuan_utama')->find($id_stok_barang);
            $this->validate(
                [
                    'jumlah_dilaporkan.' . $id_stok_barang => 'required|numeric|max:' . $stokBatchBarang->jumlah . '|gt:0',
                    'status.' . $id_stok_barang            => 'required',
                ],
                ['required' => 'Kolom Ini Wajib Diisi', 'max' => 'Melebihi jumlah yang ada', 'gt' => 'Harus lebih besar dari 0'],
            );

            $status            = $this->status[$id_stok_barang];
            $jumlah_dilaporkan = $this->jumlah_dilaporkan[$id_stok_barang];
            // dd($jumlah_dilaporkan, $status);
            DB::beginTransaction();
            if ($status != 'HAPUS INFO') {
                if ($status == 'EXPIRED GANTI / RETUR' || $status == 'RUSAK GANTI') {
                    $this->validate(['new_tgl_expired.' . $id_stok_barang => 'required|date|after_or_equal:today'], ['required' => 'Kolom Ini Wajib Diisi']);
                    if ($this->new_tgl_expired[$id_stok_barang] === $stokBatchBarang->tgl_expired) {
                        $this->addError('new_tgl_expired.' . $id_stok_barang, 'Tanggal ini sama dengan tanggal sebelumnya. Silakan pilih tanggal lain.');
                        return;
                    }
                    $existingBatch = StokBatchBarang::where('id_barang', $stokBatchBarang->id_barang)->where('tgl_expired', $this->new_tgl_expired[$id_stok_barang])->first();
                    if ($existingBatch) {
                        $existingBatch->increment('jumlah', $jumlah_dilaporkan);
                    } else {
                        StokBatchBarang::create([
                            'id_barang'   => $stokBatchBarang->id_barang,
                            'jumlah'      => $jumlah_dilaporkan,
                            'tgl_expired' => $this->new_tgl_expired[$id_stok_barang],
                            'harga_beli'  => $stokBatchBarang->harga_beli,
                        ]);
                    }
                    $tgl_expired = ['tgl' => $this->new_tgl_expired[$id_stok_barang], 'jumlah' => $jumlah_dilaporkan];
                } else {
                    // Barang::find($stokBatchBarang->id_barang)->decrement('stok', $jumlah_dilaporkan);
                    $barang = Barang::select(['id', 'nama', 'kode', 'id_satuan_utama', 'stok', 'stok_min'])->find($stokBatchBarang->id_barang);
                    $barang->update(['stok' => $barang->stok - $jumlah_dilaporkan]);

                    $tgl_expired = ['tgl' => $stokBatchBarang->tgl_expired, 'jumlah' => $jumlah_dilaporkan];
                }
                HistoryBarang::create([
                    'id_barang'   => $stokBatchBarang->id_barang,
                    'jumlah'      => $jumlah_dilaporkan,
                    'id_satuan'   => $stokBatchBarang->barang->id_satuan_utama,
                    'tgl_expired' => $tgl_expired,
                    'status'      => $status,
                    'id_user'     => Auth::user()->id,
                ]);

            }

            $stokBatchBarang->decrement('jumlah', $jumlah_dilaporkan);
            if ($stokBatchBarang->jumlah == 0) {
                $stokBatchBarang->delete();
            }

            $this->dispatch('alert', status: 'success', message: 'Berhasil Mengupdate Info Data Barang');
            $this->resetFormList($id_stok_barang);
            DB::commit();
            // return $this->redirect(route('info.barang-expired'), true);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function resetFormList($id_stok_barang)
    {
        unset($this->jumlah_dilaporkan[$id_stok_barang]);
        unset($this->status[$id_stok_barang]);
        $this->dispatch('clear-select-status_' . $id_stok_barang);
        unset($this->new_tgl_expired[$id_stok_barang]);
    }

    // public function triggerCeklistInfoBarang()
    // {
    //     try {
    //         // dd($this->ceklistInfoBarang);
    //         $this->validate(['status_ceklist' => 'required|in:HAPUS INFO,EXPIRED BUANG,EXPIRED GANTI / RETUR,EXPIRED RETUR,RUSAK BUANG,RUSAK GANTI,HILANG']);
    //         if ($this->status_ceklist == 'EXPIRED GANTI / RETUR' || $this->status_ceklist == 'RUSAK GANTI') {
    //             $this->validate(['tgl_expired_ceklist' => 'required|date|after_or_equal:today']);
    //         }
    //         foreach ($this->ceklistInfoBarang as $id_info_barang) {
    //             $info_barang                              = BarangInfo::select('jumlah')->find($id_info_barang);
    //             $this->jumlah_dilaporkan[$id_info_barang] = FormatHelper::formatNumberToString($info_barang->jumlah);
    //             $this->status[$id_info_barang]            = $this->status_ceklist;
    //             if ($this->status_ceklist == 'EXPIRED GANTI / RETUR' || $this->status_ceklist == 'RUSAK GANTI') {
    //                 $this->tgl_expired[$id_info_barang] = $this->tgl_expired_ceklist;
    //             }
    //             $this->submit($id_info_barang);
    //         }

    //         $this->resetFormCeklist();
    //     } catch (\Exception $e) {
    //         foreach ($this->ceklistInfoBarang as $id_info_barang) {
    //             $this->resetFormList($id_info_barang);
    //         }
    //         throw $e;
    //     }
    // }

    public function triggerCeklistStokBarang()
    {
        try {
            // dd($this->ceklistStokBarang);
            $this->validate(['status_ceklist' => 'required|in:HAPUS INFO,EXPIRED BUANG,EXPIRED GANTI / RETUR,EXPIRED RETUR,RUSAK BUANG,RUSAK GANTI,HILANG']);
            if ($this->status_ceklist == 'EXPIRED GANTI / RETUR' || $this->status_ceklist == 'RUSAK GANTI') {
                $this->new_tgl_expired_ceklist = FormatHelper::formatDateStringToDate($this->new_tgl_expired_ceklist);
                $this->validate(['new_tgl_expired_ceklist' => 'required|date|after_or_equal:today']);
            }
            DB::beginTransaction();
            $stokBatchBarang     = StokBatchBarang::with('barang:id,id_satuan_utama')->select(['id', 'id_barang', 'jumlah', 'tgl_expired'])->whereIn('id', $this->ceklistStokBarang);
            $createHistoryBarang = [];
            if ($this->status_ceklist != 'HAPUS INFO') {
                $stokBatchBarangGet = $stokBatchBarang->get();
                foreach ($stokBatchBarangGet as $item) {
                    if ($this->status_ceklist == 'EXPIRED GANTI / RETUR' || $this->status_ceklist == 'RUSAK GANTI') {
                        if ($this->new_tgl_expired_ceklist == FormatHelper::formatDateStringToDate($item->tgl_expired)) {
                            $this->addError('new_tgl_expired_ceklist', 'Tanggal ini sama dengan tanggal sebelumnya. Silakan pilih tanggal lain.');
                            $this->new_tgl_expired_ceklist = FormatHelper::formatDate($this->new_tgl_expired_ceklist);
                            return;
                        }
                        $existingBatch = StokBatchBarang::where('id_barang', $item->id_barang)->where('tgl_expired', $this->new_tgl_expired_ceklist)->first();
                        $jumlah        = $item->jumlah;
                        if ($existingBatch) {
                            $existingBatch->increment('jumlah', $item->jumlah);
                            $item->delete();
                        } else {
                            $item->update(['tgl_expired' => $this->new_tgl_expired_ceklist]);
                        }
                        $tgl_expired = ['tgl' => $this->new_tgl_expired_ceklist, 'jumlah' => $jumlah];
                    } else {
                        // Barang::find($item->id_barang)->decrement('stok', $item->jumlah);
                        $barang = Barang::select(['id', 'nama', 'kode', 'id_satuan_utama', 'stok', 'stok_min'])->find($item->id_barang);
                        $barang->update(['stok' => $barang->stok - $item->jumlah]);

                        $tgl_expired = ['tgl' => $item->tgl_expired, 'jumlah' => $item->jumlah];
                    }
                    $createHistoryBarang[] = [
                        'id'          => Str::ulid(),
                        'id_barang'   => $item->id_barang,
                        'jumlah'      => $item->jumlah,
                        'id_satuan'   => $item->barang->id_satuan_utama,
                        'tgl_expired' => $tgl_expired,
                        'status'      => $this->status_ceklist,
                        'id_user'     => Auth::user()->id,
                    ];
                }
                HistoryBarang::insert($createHistoryBarang);
            }
            $stokBatchBarang->delete();
            DB::commit();
            $this->resetFormCeklist();
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($this->ceklistStokBarang as $id_stok_barang) {
                $this->resetFormList($id_stok_barang);
            }
            $this->new_tgl_expired_ceklist = FormatHelper::formatDate($this->new_tgl_expired_ceklist);
            throw $e;
        }
    }

    private function resetFormCeklist()
    {
        $this->ceklistStokBarang = [];
        $this->status_ceklist    = null;
        $this->dispatch('clear-new_tgl_expired_ceklist');
        $this->dispatch('clear-select-status_ceklist');
    }

    public function hapusInfo($id_stok_barang)
    {
        StokBatchBarang::find($id_stok_barang)->delete();
        $this->dispatch('alert', status: 'success', message: 'Berhasil Menghapus Info Data Barang');
    }

    private function getQuery()
    {
        $today  = Carbon::today();
        $plus30 = $today->copy()->addDays(30);
        // $plus90 = $today->copy()->addDays(90);
        return StokBatchBarang::query()
        // ->with(['barang:id,nama,id_satuan_utama', 'barang.satuan_utama:id,nama'])
            ->select([
                'stok_batch_barang.id',
                'stok_batch_barang.id_barang',
                'stok_batch_barang.tgl_expired',
                'stok_batch_barang.jumlah',
                'barang.nama as nama_barang',
                'satuan.nama as nama_satuan',
                DB::raw("
            CASE
                WHEN tgl_expired <= '{$today->format('Y-m-d')} 23:59:59' THEN 'bg-red-300 dark:bg-red-600/40'
                WHEN tgl_expired > '{$today->format('Y-m-d')} 23:59:59' AND tgl_expired <='{$plus30->format('Y-m-d')} 23:59:59' THEN 'bg-amber-200/60 dark:bg-amber-200/50'
                WHEN tgl_expired > '{$plus30->format('Y-m-d')} 23:59:59' AND  tgl_expired <='{$this->minimum_alert->format('Y-m-d')} 23:59:59' THEN 'bg-green-300 dark:bg-green-600/40'
                ELSE ''
            END as warna
        "),
            ])
            ->join('barang', 'stok_batch_barang.id_barang', '=', 'barang.id')
            ->join('satuan', 'barang.id_satuan_utama', '=', 'satuan.id')
            ->where('tgl_expired', '<=', $this->minimum_alert)
        // ->search($this->search, ['tgl_expired'], ['barang.satuan_utama' => ['satuan.nama']], ['barang' => ['barang.nama']]);
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('barang.nama', 'like', "%{$this->search}%")
                        ->orWhere('satuan.nama', 'like', "%{$this->search}%")
                        ->orWhereDate('tgl_expired', 'like', "%{$this->search}%");
                });
            });
    }

    public function render()
    {
        $data = $this->getQuery();
        if ($this->id_barang) {
            $data = $data->where('id_barang', $this->id_barang);
        }
        $data = $data->orderBy($this->sortBy, $this->sortDir)->paginate($this->perPage);

        return view('livewire.informasi.info-barang-expired.info-barang-expired-list', ['stok_batch_barang' => $data]);
    }
    public function placeholder()
    {
        return view('components.placeholder.loading');
    }
}
