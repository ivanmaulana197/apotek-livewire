<?php
namespace App\Livewire\Transaksi;

use App\Helpers\FormatHelper;
use App\Models\Barang\StokBatchBarang;
use App\Models\Transaksi\Transaksi;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TransaksiList extends Component
{
    use WithPagination, CustomDataTable;

    public $title;
    public $filter_date, $shift_transaksi, $big_range_date = false;
    public $id_barang, $id_dokter, $id_member;

    public function mount()
    {
        $this->sortBy = 'transaksi.created_at';
        if (! $this->filter_date) {
            $this->filter_date = [
                'start' => now()->format('Y-m-d'),
                'end'   => now()->format('Y-m-d'),
            ];
        }
        if (! $this->shift_transaksi) {
            $this->shift_transaksi = 'SEMUA';
        }
        if ($this->id_barang) {
            $this->big_range_date  = true;
            $this->shift_transaksi = 'SEMUA';
            $this->filter_date     = [
                'start' => now()->startOfMonth()->format('Y-m-d'),
                'end'   => now()->endOfMonth()->format('Y-m-d'),
            ];
        } elseif ($this->id_dokter || $this->id_member) {
            $this->shift_transaksi = 'SEMUA';
        }
    }

    #[On('update_value_filter_date')]
    public function filterDate($value)
    {
        $this->filter_date = $value;
    }

    public function updatedFilterDate($value)
    {
        if (! Auth::user()->can("transaksi.filter-transaksi")) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        if (! $value) {
            $this->filter_date = [
                'start' => now()->format('Y-m-d'),
                'end'   => now()->format('Y-m-d'),
            ];
        }
        $this->dispatch('update_value_filter_date', value: $this->filter_date);
    }

    // #[On('update_value_filter_date')]
    // public function setFilterDate($value)
    // {
    //     $this->filter_date = $value;
    // }

    public function updatedShiftTransaksi($value)
    {
        if (! Auth::user()->can("transaksi.filter-transaksi")) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        $this->dispatch('update_value_shift_transaksi', value: $value);
    }

    #[On('change-shift-transaksi')]
    public function gantiShiftTransaksi($id)
    {
        if (! Auth::user()->can("transaksi.change-shift-transaksi")) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        $transaksi = Transaksi::select('id', 'shift_transaksi')->find($id);
        $transaksi->update(['shift_transaksi' => $transaksi->shift_transaksi == 'PAGI' ? 'MALAM' : 'PAGI']);
        $this->dispatch('update_transaksi');
    }

    #[On('retur-transaksi')]
    public function retur($id)
    {
        if (! Auth::user()->can('transaksi.retur')) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        try {
            DB::beginTransaction();
            // 1️⃣ Ambil transaksi dan semua data terkait sekaligus (tanpa eager yang berlebih)
            $transaksi = Transaksi::with([
                'transaksi_list_promo:id,id_transaksi',
                'detail_transaksi' => function ($q) {
                    $q->with(['detail_barang.barang:id,nama,kode,stok,id_satuan_utama,stok_min']);
                },
            ])->findOrFail($id);

            // 2️⃣ Loop histori & update stok sekaligus
            foreach ($transaksi->detail_transaksi as $detail_transaksi) {
                $this->update_stok_batch($detail_transaksi->detail_barang);
                // update status dan stok total barang
                $detail_transaksi->detail_barang->update(['status' => 'RETUR']);
                // $detail_transaksi->detail_barang->barang->increment('stok', $detail_transaksi->detail_barang->jumlah_dalam_satuan_utama);
                $detail_transaksi->detail_barang->barang->update(['stok' => $detail_transaksi->detail_barang->barang->stok + $detail_transaksi->detail_barang->jumlah_dalam_satuan_utama]);

                // hapus relasi detail transaksi
                $detail_transaksi->delete();
            }

            // 3️⃣ Hapus promo & update status transaksi
            $transaksi->transaksi_list_promo()->delete();
            $transaksi->update(['status' => 'RETUR']);
            DB::commit();
            $this->dispatch('update_transaksi');
            $this->dispatch('alert', status: 'success', message: 'Berhasil Retur Transaksi');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', status: 'error', message: 'Gagal Retur Transaksi');
            return;
        }

    }

    public function update_stok_batch($history)
    {
        $expiredList = is_array($history->tgl_expired) ? $history->tgl_expired : [];
        if (empty($expiredList)) {
            return;
        }

        // Ambil semua batch expired yang sudah ada untuk id_barang ini sekali query
        $existingBatches = StokBatchBarang::where('id_barang', $history->id_barang)->pluck('id', 'tgl_expired')->toArray();
        $newData         = [];

        foreach ($history->tgl_expired as $item) {
            if (isset($existingBatches[$item['tgl']])) {
                // Update langsung tanpa query select ulang
                StokBatchBarang::where('id', $existingBatches[$item['tgl']])->increment('jumlah', $item['jumlah']);
            } else {
                $newData[] = [
                    'id'          => Str::ulid(),
                    'id_barang'   => $history->id_barang,
                    'tgl_expired' => $item['tgl'],
                    'jumlah'      => $item['jumlah'],
                ];
            }
        }

        if (! empty($newData)) {
            StokBatchBarang::insert($newData);
        }
    }

    public function getDataTransaksi()
    {
        if (Auth::user()->can('transaksi.list-all')) {
            $transaksi_all = true;
        } elseif (Auth::user()->can('transaksi.list-own')) {
            $transaksi_all = false;
        }

        $query = Transaksi::query()
            ->join('metode_pembayaran as mp', 'mp.id', '=', 'transaksi.id_metode_pembayaran')
            ->leftJoin('member as m', 'm.id', '=', 'transaksi.id_member')
            ->leftJoin('pasien as p', 'p.id', '=', 'transaksi.id_pasien')
            ->select('transaksi.*', 'mp.nama as nama_metode_pembayaran', 'm.nama as nama_member', 'p.nama_pasien as nama_pasien')
            ->where('transaksi.status', 'TERJUAL')
            ->when($this->filter_date, function ($query, $item) {
                $query->whereBetween('transaksi.created_at', [
                    "{$item['start']} 00:00:00",
                    "{$item['end']} 23:59:59",
                ]);
            })
            ->when(in_array($this->shift_transaksi, ['PAGI', 'MALAM']), function ($query) {
                $query->where('shift_transaksi', $this->shift_transaksi);
            })
            ->when($this->id_dokter, function ($query) {
                $query->where('id_dokter', $this->id_dokter);
            })
            ->when($this->id_member, function ($query) {
                $query->where('id_member', $this->id_member);
            })
            ->when($this->id_barang, function ($query) {
                $query->whereHas('detail_transaksi_barang', function ($query) {
                    $query->where('id_barang', $this->id_barang);
                });
            })
            ->when(! $transaksi_all, function ($query) {
                $query->where('transaksi.id_user', Auth::user()->id);
            })
            ->search(
                $this->search,
                ['no_transaksi', 'jenis_transaksi', 'shift_transaksi'],
                [
                    'pasien'            => ['nama_pasien'],
                    'metode_pembayaran' => ['nama'],
                    'member'            => ['nama'],
                ]
            )
            ->orderBy($this->sortBy, $this->sortDir)->paginate($this->perPage)
        ;

        // Handle sorting termasuk kolom relasi
        // $query = match ($this->sortBy) {
        // 'metode_pembayaran.nama' => $query->join('metode_pembayaran', 'transaksi.id_metode_pembayaran', '=', 'metode_pembayaran.id')
        //     ->select('transaksi.*')
        //     ->orderBy('metode_pembayaran.nama', $this->sortDir),
        // 'pasien.nama_pasien'     => $query->join('pasien', 'transaksi.id_pasien', '=', 'pasien.id')
        //     ->select('transaksi.*')
        //     ->orderBy('pasien.nama_pasien', $this->sortDir),
        // 'member.nama'            => $query->join('member', 'transaksi.id_member', '=', 'member.id')
        //     ->select('transaksi.*')
        //     ->orderBy('member.nama', $this->sortDir),
        // default => $query->orderBy($this->sortBy, $this->sortDir),
        // };

        return $query;

    }

    public function render()
    {
        if (! Auth::user()->canAny(['transaksi.list-all', 'transaksi.list-own'])) {
            return $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
        $data = $this->getDataTransaksi();

        $data = $this->formatterData($data);
        return view('livewire.transaksi.transaksi-list', [
            'transaksis' => $data,
        ]);
    }
    public function getDataTransaksiWithJoin()
    {
        $data = Transaksi::query()
            ->join('metode_pembayaran as mp', 'mp.id', '=', 'transaksi.id_metode_pembayaran')
            ->leftJoin('member as m', 'm.id', '=', 'transaksi.id_member')
            ->leftJoin('pasien as p', 'p.id', '=', 'transaksi.id_pasien')
            ->join('detail_transaksi_barang as dt', 'dt.id_transaksi', '=', 'transaksi.id')
            ->join('history_barang as hb', 'hb.id', '=', 'dt.id_history_barang')
            ->where('transaksi.status', 'TERJUAL')
            ->where('hb.id_barang', $this->id_barang)
            ->when(in_array($this->shift_transaksi, ['PAGI', 'MALAM']), function ($query) {
                $query->where('shift_transaksi', $this->shift_transaksi);
            })
            ->when($this->id_dokter, function ($query) {
                $query->where('id_dokter', $this->id_dokter);
            })
            ->when($this->id_member, function ($query) {
                $query->where('id_member', $this->id_member);
            })
            ->select(
                'transaksi.id',
                'transaksi.no_transaksi',
                'transaksi.created_at',
                'transaksi.jenis_transaksi',
                'm.nama as nama_member',
                'p.nama_pasien as nama_pasien',
                'transaksi.sub_total',
                'mp.nama as nama_metode_pembayaran',
                'transaksi.potongan_diskon',
                'transaksi.total_bayar',
                'transaksi.pembulatan',
                'transaksi.shift_transaksi',
            )
            ->search(
                $this->search,
                ['no_transaksi', 'jenis_transaksi', 'shift_transaksi'],
                [
                    'pasien'            => ['nama_pasien'],
                    'metode_pembayaran' => ['mp.nama'],
                    'member'            => ['m.nama'],
                ]
            )
            ->orderBy($this->sortBy, $this->sortDir)->paginate($this->perPage);
        dd($data);
        return $data;
    }

    public function formatterData($data)
    {
        $data->each(function ($item) {
            $item->tgl_created = FormatHelper::formatDate($item->created_at, true, true);

            $item->sub_total       = FormatHelper::formatNumberToString($item->sub_total);
            $item->potongan_diskon = FormatHelper::formatNumberToString($item->potongan_diskon);
            $item->total_bayar     = FormatHelper::formatNumberToString($item->total_bayar);
            $item->pembulatan      = FormatHelper::formatNumberToString($item->pembulatan);
            return $item;
        });
        return $data;
    }

}
