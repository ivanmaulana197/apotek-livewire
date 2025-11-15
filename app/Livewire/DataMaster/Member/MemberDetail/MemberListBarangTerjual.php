<?php
namespace App\Livewire\DataMaster\Member\MemberDetail;

use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MemberListBarangTerjual extends Component
{
    use WithPagination, CustomDataTable;
    public $id_member;
    public $filter_date;

    public function mount()
    {
        $this->sortBy  = $this->defaultSortBy  = 'total_terjual';
        $this->sortDir = $this->defaultSortDir = 'desc';
    }

    #[On('update_value_filter_date')]
    public function filterDate($value)
    {
        $this->filter_date = $value;
    }

    public function getData()
    {
        $barang_terlaris = DB::table('detail_transaksi_barang as dtb')
            ->join('transaksi as t', 't.id', '=', 'dtb.id_transaksi')
            ->join('history_barang as hb', 'hb.id', '=', 'dtb.id_history_barang')
            ->join('barang as b', 'b.id', '=', 'hb.id_barang')
            ->leftJoin('satuan_barang as sb', function ($join) {
                $join->on('sb.id_barang', '=', 'b.id')
                    ->on('sb.id_satuan', '=', 'hb.id_satuan');
            })
            ->join('satuan as s_utama', 's_utama.id', '=', 'b.id_satuan_utama')
            ->where('t.status', 'TERJUAL')
            ->where('t.id_member', $this->id_member)
            ->whereBetween('t.created_at', [
                "{$this->filter_date['start']} 00:00:00",
                "{$this->filter_date['end']} 23:59:59",
            ])
            ->select('b.id', 'b.nama', DB::raw('SUM(hb.jumlah * COALESCE(sb.rasio_satuan_opsi / sb.rasio_satuan_utama, 1)) as total_terjual'), 's_utama.nama as satuan_utama')
            ->where('b.nama', 'like', '%' . $this->search . '%')
            ->groupBy('b.id', 'b.nama')
        // ->orderByDesc('total_terjual')
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return $barang_terlaris;
    }

    public function render()
    {
        $barang_terlaris = $this->getData();
        return view('livewire.data-master.member.member-detail.member-list-barang-terjual', compact('barang_terlaris'));
    }
}
