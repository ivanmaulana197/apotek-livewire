<?php
namespace App\Livewire\DataMaster\Pasien;

use App\Models\DataMaster\Pasien;
use App\Traits\CustomDataTable;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PasienList extends Component
{
    use WithPagination, CustomDataTable;

    public $id_dokter, $filter_date;

    public function mount()
    {
        if ($this->id_dokter) {
            $this->sortBy  = $this->defaultSortBy  = 'total_bayar';
            $this->sortDir = $this->defaultSortDir = 'desc';
        } else {
            $this->sortBy  = $this->defaultSortBy  = 'nama_pasien';
            $this->sortDir = $this->defaultSortDir = 'asc';
        }
    }

    #[On('update_value_filter_date')]
    public function filterDate($value)
    {
        $this->filter_date = $value;
    }

    public function getData()
    {
        // $data_pasien = Pasien::query()
        //     ->when($this->id_dokter, function ($query) {
        //         $query->whereHas('transaksi', function ($query) {
        //             $query->where('id_dokter', $this->id_dokter);
        //         });
        //     })
        //     ->paginate($this->perPage);

        if ($this->id_dokter) {
            // $data_pasien = DB::table('pasien')
            //     ->join('transaksi', 'transaksi.id_pasien', '=', 'pasien.id')
            //     ->when($this->id_dokter, function ($query) {
            //         $query->where('status', 'TERJUAL')
            //             ->where('transaksi.id_dokter', $this->id_dokter);
            //     })
            //     ->select(
            //         'pasien.*',
            //         DB::raw('sum(transaksi.total_bayar) as total_bayar')
            //     )
            //     ->whereBetween('transaksi.created_at', [
            //         "{$this->filter_date['start']} 00:00:00",
            //         "{$this->filter_date['end']} 23:59:59",
            //     ])
            //     ->where('nama_pasien', 'like', "%{$this->search}%")
            //     ->groupBy('pasien.id')
            //     ->orderBy($this->sortBy, $this->sortDir)
            //     ->distinct()
            //     ->paginate($this->perPage);

            $data_pasien = Pasien::query()
                ->when($this->id_dokter, function ($query) {
                    $query->whereHas('transaksi', function ($query) {
                        $query->where('status', 'TERJUAL')
                            ->where('id_dokter', $this->id_dokter)
                            ->whereBetween('transaksi.created_at', [
                                "{$this->filter_date['start']} 00:00:00",
                                "{$this->filter_date['end']} 23:59:59",
                            ])
                        ;
                    });
                })
                ->withSum(['transaksi as total_bayar' => function ($query) {
                    $query->where('status', 'TERJUAL')
                        ->where('id_dokter', $this->id_dokter)
                        ->whereBetween('created_at', [
                            $this->filter_date['start'] . ' 00:00:00',
                            $this->filter_date['end'] . ' 23:59:59',
                        ]);
                }], 'total_bayar')
                ->where('nama_pasien', 'like', "%{$this->search}%")

                ->orderBy($this->sortBy, $this->sortDir)
                ->paginate($this->perPage)

            ;
        } else {
            $data_pasien = Pasien::query()->paginate($this->perPage);
        }

        return $data_pasien;
    }

    public function render()
    {
        $pasiens = $this->getData();
        return view('livewire.data-master.pasien.pasien-list', compact('pasiens'));
    }
}
