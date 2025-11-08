<?php
namespace App\Livewire\StokOpname;

use App\Models\Barang\Barang;
use App\Models\StokOpnameHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class StokOpnameIndex extends Component
{
    public function newStokOpname()
    {
        Barang::query()->update(['stok_real' => null]);
        $this->dispatch('update-stok-real');
        $this->dispatch('alert', status: 'success', message: 'Berhasil Menghapus Seluruh Jumlah Stok Toko');
    }

    #[On('sinkronisasi-stok')]
    public function sinkronisasiStok()
    {
        $stokOpnameHistory = [];
        $created_at        = now();
        try {
            DB::beginTransaction();
            Barang::whereNotNull('stok_real')
                ->get()
                ->each(function ($barang) use ($created_at, &$stokOpnameHistory) {
                    $selisih  = $barang->stok_real - $barang->stok;
                    $kerugian = $selisih * $barang->harga_beli;

                    // Simpan ke histori
                    $stokOpnameHistory[] = [
                        'id'         => Str::ulid(),
                        'id_barang'  => $barang->id,
                        'stok'       => $barang->stok,
                        'stok_real'  => $barang->stok_real,
                        'kerugian'   => $kerugian,
                        'created_at' => $created_at,
                        'updated_at' => $created_at,
                    ];
                    $barang->update([
                        'stok'      => $barang->stok_real,
                        'stok_real' => null,
                    ]);
                });
            StokOpnameHistory::insert($stokOpnameHistory);
            DB::commit();
            $this->dispatch('update-stok-real');
            $this->dispatch('alert', status: 'success', message: 'Berhasil Menghapus Seluruh Jumlah Stok Toko');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', status: 'error', message: $e->getMessage());
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.stok-opname.stok-opname-index');
    }

}
