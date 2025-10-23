<?php
namespace App\Models\Barang;

use App\Models\Faktur\DetailFakturBarang;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBatchBarang extends Model
{
    use HasUlids, HasFactory;
    protected $table    = 'stok_batch_barang';
    protected $fillable = [
        'id_barang',
        'jumlah',  //sudah dalam satuan utama
        'tgl_expired',
        'harga_beli',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function faktur_barang()
    {
        return $this->hasMany(DetailFakturBarang::class, 'id_stok_batch_barang');
    }

    /**
     * Konversi jumlah ke bentuk kata (misal "1 tablet 4 box").
     */
    public function formatJumlah()
    {
        $barang = $this->barang;

        if (! $barang) {
            return $this->jumlah;
        }

        // Ambil satuan utama
        $satuanUtama = $barang->satuan_utama?->nama ?? 'Satuan';

                                             // Ambil semua rasio satuan opsi
        $satuanOpsi = $barang->satuan_opsi() // relasi many-to-many ke Satuan dengan pivot
            ->withPivot(['rasio_satuan_utama', 'rasio_satuan_opsi'])
            ->get()
            ->map(fn($s) => [
                'nama'  => $s->nama,                                                     // langsung dari model Satuan
                'rasio' => $s->pivot->rasio_satuan_utama / $s->pivot->rasio_satuan_opsi, // rasio ke satuan utama
            ])
            ->sortByDesc('rasio'); // urutkan dari terbesar ke terkecil

        $jumlah = $this->jumlah; // jumlah dalam satuan utama
        $hasil  = [];

        // Konversi ke setiap satuan opsi
        foreach ($satuanOpsi as $opsi) {
            $qty    = floor($jumlah / $opsi['rasio']);
            $jumlah = fmod($jumlah, $opsi['rasio']); // sisa

            if ($qty > 0) {
                $hasil[] = $qty . ' ' . $opsi['nama'];
            }
        }

        // Sisa terakhir dalam satuan utama
        if ($jumlah > 0) {
            $hasil[] = $jumlah . ' ' . $satuanUtama;
        }

        return implode(' ', $hasil);
    }

}
