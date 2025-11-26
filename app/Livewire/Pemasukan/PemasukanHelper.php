<?php
namespace App\Livewire\Pemasukan;

use App\Models\DataMaster\Member;
use App\Models\Promo\PromoBarang;
use Carbon\Carbon;

trait PemasukanHelper
{
    public function checkModeShift()
    {
        $currentHour = Carbon::now()->hour;
        return $currentHour < 14 ? 'PAGI' : 'MALAM';
    }

    public function hargaJualResep($hargaLama, $persen_resep)
    {
        return $hargaLama + ($hargaLama * $persen_resep) / 100;
    }

    public function getTotalJumlahDalamSatuanUtama($id_barang)
    {
        return $this->pesanan_kasir->barang_pesanan_kasir->where('id_barang', $id_barang)->sum('jumlah_dalam_satuan_utama');
    }

    public function getListPromoBarang($id_barang, $total_jumlah, $id_member = null)
    {
        $id_profesi = null;
        if ($id_member) {
            $member     = Member::find($id_member);
            $id_profesi = $member->id_profesi;
        }
        $promoBarang = PromoBarang::query()
            ->with('barang.opsi_satuan:id,nama')
            ->where('id_barang', $id_barang)
            ->whereHas('promo', function ($q) use ($id_profesi) {
                $q->aktif()
                    ->when($id_profesi, function ($q) use ($id_profesi) {
                        $q->where(function ($query) use ($id_profesi) {
                            $query->whereDoesntHave('profesies') // promo umum
                                ->orWhereHas('profesies', fn($q) => $q->where('id_profesi', $id_profesi));
                        });
                    }, function ($q) {
                        // Jika user tidak punya profesi, ambil hanya promo umum
                        $q->whereDoesntHave('profesies');
                    });
            })->get()
            ->filter(function ($barangPromo) use ($total_jumlah) {
                // Ambil hanya promo yang memenuhi syarat
                return $barangPromo->SyaratQtyDalamSatuanUtama <= $total_jumlah;
            })
            ->pluck('id')
            ->toArray();
        return $promoBarang;
    }

}
