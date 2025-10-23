<?php
namespace App\Models\Barang;

use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Models\DataMaster\Satuan;
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

    protected $appends = ['satuan_opsi', 'harga_beli'];
    protected $hidden  = [];

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
    public function opsi_satuan()
    {
        return $this->belongsToMany(Satuan::class, 'satuan_barang', 'id_barang', 'id_satuan')->withPivot('rasio_satuan_utama', 'rasio_satuan_opsi');
    }
    public function opsi_harga()
    {
        return $this->hasMany(HargaBarang::class, 'id_barang');
    }
    public function stok_batch_barang()
    {
        return $this->hasMany(StokBatchBarang::class, 'id_barang');
    }

    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['id_kategories'] ?? null, function ($query, $id_kategori) {
                $query->whereHas(
                    'kategories',
                    function ($q) use ($id_kategori) {
                        $q->whereIn('kategori.id', $id_kategori);
                    },
                    '=',
                    count($id_kategori),
                ); //menampilkan kategori yang harus terpilih
            })
            ->when($filters['id_raks'] ?? null, function ($query, $id_rak) {
                $query->whereHas(
                    'raks',
                    function ($q) use ($id_rak) {
                        $q->whereIn('rak.id', $id_rak);
                    },
                    '=',
                    count($id_rak),
                ); //menampilkan rak yang harus terpilih
            });
    }

    public function getSatuanOpsiAttribute()
    {
        return $this->opsi_satuan->map(function ($satuan) {
            return [
                'id_satuan' => $satuan->id,
                'satuan'    => $satuan->nama,
                'rasio'     => $satuan->pivot->rasio_satuan_utama / $satuan->pivot->rasio_satuan_opsi,
            ];
        });
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

    public function getFormattedStokAttribute()
    {
        $stokUtama      = $this->stok;         // Stok dalam satuan utama
        $satuanUtama    = $this->satuan_utama; // Satuan utama
        $satuanOpsional = $this->opsi_satuan()->whereRaw('rasio_satuan_utama / rasio_satuan_opsi < 1')->orderByRaw('rasio_satuan_utama / rasio_satuan_opsi DESC')->get();
        // dd($satuanOpsional);
        $result = [];
        if ($satuanOpsional->isEmpty()) {
            $stokUtama = floor($stokUtama);
            return "{$stokUtama} {$satuanUtama->nama}";
        }
        $jumlahSatuanUtama = floor($stokUtama); // Jumlah stok dalam satuan utama tanpa koma

        $result[] = "{$jumlahSatuanUtama} {$satuanUtama->nama}";
                                                                 // $result[] = [
                                                                 //     'jumlah' => $jumlahSatuanUtama,
                                                                 //     'satuan' => $satuanUtama->nama,
                                                                 // ];
        $jumlahSatuanOpsional = $stokUtama - $jumlahSatuanUtama; // ambil decimalnya

        if ($jumlahSatuanOpsional > 0) {
            $remainingStok = $jumlahSatuanOpsional; //sisa dari satuan utama
            foreach ($satuanOpsional as $opsiSatuan) {
                $rasioSatuanUtama = $opsiSatuan->pivot->rasio_satuan_utama;

                $rasioSatuanOpsional = $opsiSatuan->pivot->rasio_opsi_satuan;
                $jumlahSatuan        = floor($remainingStok / ($rasioSatuanUtama / $rasioSatuanOpsional));
                $remainingStok -= ($jumlahSatuan * $rasioSatuanUtama) / $rasioSatuanOpsional;
                $result[] = "{$jumlahSatuan} {$opsiSatuan->nama}";
                if ($remainingStok <= 0) {
                    break;
                }
            }
        }

        return implode(' ', $result);
    }

}
