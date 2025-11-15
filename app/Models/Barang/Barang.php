<?php
namespace App\Models\Barang;

use App\Helpers\FormatHelper;
use App\Models\Barang\HargaBarang;
use App\Models\Barang\StokBatchBarang;
use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Models\DataMaster\Satuan;
use App\Models\HistoryBarang;
use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasUlids, HasFactory, ScopeSearchable;
    protected $table    = 'barang';
    protected $fillable = [
        'id',
        'kode',
        'nama',
        'stok',
        'stok_min',
        'stok_real',
        'harga_beli_default',
        'mode_jual_utama',
        'persen_jual_utama',
        'harga_jual_utama',
        'id_satuan_utama',
        'keterangan',
    ];

    // protected $appends = ['satuan_opsi'];
    protected $hidden = [];

    public function raks()
    {
        return $this->belongsToMany(Rak::class, 'rak_barang', 'id_barang', 'id_rak');
    }
    public function kategories()
    {
        return $this->belongsToMany(Kategori::class, 'kategori_barang', 'id_barang', 'id_kategori');
    }

    public function satuan_utama()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_utama');
    }

    public function transaksi_barang()
    {
        return $this->hasMany(HistoryBarang::class, 'id_barang')->where('status', 'TERJUAL');
    }
    public function opsi_satuan()
    {
        return $this->belongsToMany(Satuan::class, 'satuan_barang', 'id_barang', 'id_satuan')
            ->withPivot('rasio_satuan_utama', 'rasio_satuan_opsi')
            ->select(['id', 'nama']);
    }
    public function opsi_harga()
    {
        return $this->hasMany(HargaBarang::class, 'id_barang');
    }
    public function stok_batch_barang()
    {
        return $this->hasMany(StokBatchBarang::class, 'id_barang');
    }

    // public function scopeFilter($query, array $filters)
    // {
    //     $query
    //         ->when($filters['id_kategories'] ?? null, function ($q, $ids) {
    //             foreach ($ids as $id) {
    //                 $q->whereHas('kategories', fn($k) => $k->where('kategori.id', $id));
    //             }
    //         })
    //         ->when($filters['id_raks'] ?? null, function ($q, $ids) {
    //             foreach ($ids as $id) {
    //                 $q->whereHas('raks', fn($r) => $r->where('rak.id', $id));
    //             }
    //         });
    // }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['id_kategories'] ?? null, function ($q, $ids) {
            $q
                ->whereIn('kategori.id', $ids)
                ->havingRaw('COUNT(DISTINCT kategori.id) = ?', [count($ids)]);
        })->when($filters['id_raks'] ?? null, function ($q, $ids) {
            $q
                ->whereIn('rak.id', $ids)
                ->havingRaw('COUNT(DISTINCT rak.id) = ?', [count($ids)]);
        });
    }

    public function getSatuanOpsiAttribute()
    {
        return $this->opsi_satuan
            ->sortByDesc(function ($satuan) {
                return $satuan->pivot->rasio_satuan_utama / $satuan->pivot->rasio_satuan_opsi;
            })
            ->map(function ($satuan) {
                return [
                    'id_satuan' => $satuan->id,
                    'satuan'    => $satuan->nama,
                    'rasio'     => $satuan->pivot->rasio_satuan_utama / $satuan->pivot->rasio_satuan_opsi,
                ];
            })
            ->values();
    }

    public function getAllSatuanAttribute()
    {
        return collect(array_merge([[
            'id_satuan' => $this->id_satuan_utama,
            'satuan'    => $this->satuan_utama->nama,
            'rasio'     => 1,
        ]], $this->satuan_opsi->toArray()));
    }

    public function hargaJual($jumlah_dalam_satuan_utama = null, $id_satuan = null)
    {

        // dd($jumlah_dalam_satuan_utama);
        $id_satuan = $id_satuan ?? $this->id_satuan_utama;
        if (! $jumlah_dalam_satuan_utama) {
            $jumlah_dalam_satuan_utama = $this->calculateJumlahDalamSatuanUtama(1, $id_satuan);
        }
        $opsi_harga = $this->opsi_harga
            ->map(function ($item) {
                $item->append('jumlah_dalam_satuan_utama');
                return $item;
            })
            ->push((object) [
                'jumlah_dalam_satuan_utama' => 1,
                'harga'                     => $this->harga_jual_utama,
                'harga_satuan_utama'        => $this->harga_jual_utama,
            ])
            ->sortBy('jumlah_dalam_satuan_utama')
            ->values();

        // Cari opsi harga dengan jumlah <= input (yang paling besar tapi tidak melebihi)
        $harga_terpilih = $opsi_harga
            ->filter(fn($item) => $item->jumlah_dalam_satuan_utama <= $jumlah_dalam_satuan_utama)->last();
        $harga = $this->calculateJumlahDalamSatuanUtama($harga_terpilih->harga_satuan_utama ?? $this->harga_jual_utama, $id_satuan);

        return [$harga, $harga_terpilih->harga_satuan_utama ?? $this->harga_jual_utama];
    }

    public function calculateJumlahDalamSatuanUtama($jumlah, $id_satuan = null)
    {
        $id_satuan = $id_satuan ?? $this->id_satuan_utama;
        return $this->all_satuan->where('id_satuan', $id_satuan)->first()['rasio'] * $jumlah;
    }

    // 🔹 Ambil harga beli terakhir (dengan prioritas batch aktif)
    public function getHargaBeliAttribute()
    {
        $harga_beli = $this->stok_batch_barang()
            ->where('tgl_expired', '>=', now())
            ->orderBy('tgl_expired')
            ->first()?->harga_beli;
        return $harga_beli ?? $this->harga_beli_default;
    }

    public function konversi_stok($stok = null)
    {
        $satuan = $this->all_satuan->sortByDesc('rasio')->values();
        if (! $stok) {
            $stok = $this->stok;
        }
        $hasil = [];

        foreach ($satuan as $item) {
            $rasio = $item['rasio'];
            if ($rasio <= 0) {
                continue; // skip kalau rasio tidak valid
            }
            $jumlah = floor($stok / $rasio);
            if ($jumlah > 0) {
                $hasil[] = FormatHelper::formatNumberToString($jumlah) . " {$item['satuan']}";
            }

            $stok = fmod($stok, $rasio);

        }
        return implode(' ', $hasil);
    }

}
