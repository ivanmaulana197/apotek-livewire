<?php
namespace App\Livewire\Cetak;

use App\Models\Setting;
use App\Models\Transaksi\Transaksi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class StrukPembayaran extends Component
{
    public $transaksi, $setting;
    public $id_transaksi;

    public function mount($id_transaksi)
    {
        // $this->transaksi = Transaksi::with([
        //     'detail_transaksi.detail_barang' => function ($q) {
        //         $q->with(['barang:id,nama', 'satuan:id,nama'])->orderBy('id_barang');
        //     },
        //     'transaksi_list_promo.barang:id,nama',
        // ])
        //     ->findOrFail($id_transaksi);

        // $diskonPromos = $this->transaksi->transaksi_list_promo
        //     ->where('tipe_promo', 'BARANG DISKON')
        //     ->keyBy('id_barang');

        // $this->transaksi->detail_transaksi->map(function ($item, $key) use ($diskonPromos) {
        //     $next               = $this->transaksi->detail_transaksi->get($key + 1)?->detail_barang;
        //     $item->promo_barang = (! $next || $item->detail_barang->id_barang != $next->id_barang)
        //         ? ($diskonPromos[$item->detail_barang->id_barang] ?? null)
        //         : null;
        //     return $item;
        // });

        $this->id_transaksi = $id_transaksi;
        $this->setting      = Setting::first();
    }

    public function getTransaksiDetail()
    {
        $this->transaksi = Transaksi::query()
            ->with(['detail_transaksi' => function ($q) {
                $q->select(
                    'detail_transaksi_barang.*',
                    'history_barang.id_barang',
                    'history_barang.jumlah',
                    'barang.nama as nama_barang',
                    'satuan.nama as nama_satuan',
                )
                    ->join('history_barang', 'history_barang.id', '=', 'detail_transaksi_barang.id_history_barang')
                    ->join('barang', 'barang.id', '=', 'history_barang.id_barang')
                    ->join('satuan', 'satuan.id', '=', 'history_barang.id_satuan')
                    ->orderBy('history_barang.id_barang', 'asc');
            }, 'transaksi_list_promo'])
            ->leftjoin('member', 'member.id', '=', 'transaksi.id_member')
            ->leftjoin('pasien', 'pasien.id', '=', 'transaksi.id_pasien')
            ->leftjoin('dokter', 'dokter.id', '=', 'transaksi.id_dokter')
            ->select('transaksi.*', 'member.nama as nama_member', 'pasien.nama_pasien', 'dokter.nama as nama_dokter')
            ->find($this->id_transaksi);

        if (! Auth::user()->can('cetak.struk-pembayaran-all') && ! (Auth::user()->can('cetak.struk-pembayaran-own') && $this->transaksi->id_user == Auth::user()->id)) {
            return redirect()->back()->with([
                'alert' => [
                    'title'   => 'Tidak Diizinkan',
                    'message' => 'Anda tidak memiliki akses yang diperlukan.',
                    'icon'    => 'error', // Ikon SweetAlert ('success', 'error', 'warning', 'info')
                ],
            ]);
        }

        $diskonPromos = $this->transaksi->transaksi_list_promo
            ->where('tipe_promo', 'BARANG DISKON')
            ->keyBy('id_barang');
        $this->transaksi->detail_transaksi->map(function ($item, $key) use ($diskonPromos) {
            $next               = $this->transaksi->detail_transaksi->get($key + 1)?->detail_barang;
            $item->promo_barang = (! $next || $item->detail_barang->id_barang != $next->id_barang)
                ? ($diskonPromos[$item->detail_barang->id_barang] ?? null)
                : null;
            return $item;
        });

    }

    #[Layout('components.layouts.cetak')]
    public function render()
    {
        $this->getTransaksiDetail();
        return view('livewire.cetak.struk-pembayaran');
    }
}
