<?php
namespace App\Livewire\Transaksi;

use App\Helpers\FormatHelper;
use App\Models\Barang\StokBatchBarang;
use App\Models\Transaksi\Transaksi;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TransaksiList extends Component
{
    use WithPagination, CustomDataTable;

    public $filter_date, $shift_transaksi;
    public $id_barang;

    public function mount()
    {
        $this->sortBy = 'transaksi.created_at';
        if ($this->id_barang) {
            $this->shift_transaksi = 'SEMUA';
            $this->filter_date     = [
                'start' => now()->startOfMonth()->format('Y-m-d'),
                'end'   => now()->endOfMonth()->format('Y-m-d'),
            ];
        }
    }

    public function updatedFilterDate($value)
    {
        if (! $value) {
            $this->filter_date = [
                'start' => now()->format('Y-m-d'),
                'end'   => now()->format('Y-m-d'),
            ];
        }
        $this->dispatch('update_value_filter_date', value: $this->filter_date);
    }

    public function updatedShiftTransaksi($value)
    {
        $this->dispatch('update_value_shift_transaksi', value: $value);
    }

    #[On('change-shift-transaksi')]
    public function gantiShiftTransaksi($id)
    {
        $transaksi = Transaksi::select('id', 'shift_transaksi')->find($id);
        $transaksi->update(['shift_transaksi' => $transaksi->shift_transaksi == 'PAGI' ? 'MALAM' : 'PAGI']);
        $this->dispatch('update_transaksi');
    }

    #[On('retur-transaksi')]
    public function retur($id)
    {
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
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: $e->getMessage());
            throw $e;
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

        $query = Transaksi::query()
            ->with([
                'metode_pembayaran:id,nama',
                'pasien:id,nama_pasien',
                'member:id,nama',
            ])
            ->where('transaksi.status', 'TERJUAL')
            ->when($this->filter_date, function ($query, $item) {
                $query->whereBetween('created_at', [
                    "{$item['start']} 00:00:00",
                    "{$item['end']} 23:59:59",
                ]);
            })
            ->when(in_array($this->shift_transaksi, ['PAGI', 'MALAM']), function ($query) {
                $query->where('shift_transaksi', $this->shift_transaksi);
            })
            ->search(
                $this->search,
                ['no_transaksi', 'jenis_transaksi', 'shift_transaksi'],
                [
                    'pasien'            => ['nama_pasien'],
                    'metode_pembayaran' => ['nama'],
                    'member'            => ['nama'],
                ]
            );

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
        $data = $this->getDataTransaksi();
        if ($this->id_barang) {
            $data = $data->whereHas('detail_transaksi_barang', function ($query) {
                $query->where('id_barang', $this->id_barang);
            });
        }

        $data = $data->orderBy($this->sortBy, $this->sortDir)->paginate($this->perPage);

        $data = $this->formatterData($data);
        return view('livewire.transaksi.transaksi-list', [
            'transaksis' => $data,
        ]);
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
