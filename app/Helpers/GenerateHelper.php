<?php

namespace App\Helpers;

use App\Models\Faktur\Faktur;
use App\Models\Kasir\PesananKasir;
use App\Models\Transaksi\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GenerateHelper
{
    public static function generateKwitansiId(){
         // Ambil tanggal hari ini dalam format YYYYMMDD
         $today = Carbon::today()->format('Ymd');  // contoh: 20241221

         // Ambil urutan terakhir untuk tanggal yang sama
         $lastInvoice = Faktur::whereDate('created_at', Carbon::today())
                         ->orderByDesc('no_faktur')
                         ->first();
        // dd($lastInvoice);
         // Jika tidak ada data untuk hari ini, mulai dari 001
         if (!$lastInvoice) {
             return $today . '001';
         }

         // Ambil angka urutan terakhir dan tambahkan 1
         $lastId = substr($lastInvoice->no_faktur, -3); // Ambil 3 karakter terakhir
         $newId = str_pad((int)$lastId + 1, 3, '0', STR_PAD_LEFT);  // Tambahkan 1 dan pastikan panjangnya 3 digit

         // Gabungkan dengan tanggal dan nomor urut baru
         return $today . $newId;
    }

     public static function generateNoPesananKasir(){

        // 1. Ambil tanggal dalam format `dmY`
        $date = date('dmY');

        //tambahkan id user offsite
        // $kasir = Auth::user()->id;
        // 3. Query untuk mencari ID transaksi terbesar
        $lastTransaksi = PesananKasir::whereDate('created_at', Carbon::today())
        ->orderByDesc('no_pesanan_kasir')->pluck('no_pesanan_kasir')
        ->first();

        // 4. Ambil nomor urut transaksi sebelumnya
        $maxIdNumber = $lastTransaksi ? (int) substr($lastTransaksi, 10, 4) : 0;

        // 5. Tambahkan 1 ke nomor urut untuk ID baru
        $kode_tr = $maxIdNumber + 1;

        // 6. Buat ID baru dengan nomor urut 4 digit
        return 'PK'.$date . sprintf('%04s', $kode_tr);

    }

    public static function generateTransaksiId(){

        // 1. Ambil tanggal dalam format `dmY`
        $date = date('dmY');

        //tambahkan id user offsite
        // $kasir = Auth::user()->id;
        // 3. Query untuk mencari ID transaksi terbesar
        $lastTransaksi = Transaksi::whereDate('created_at', Carbon::today())
        ->orderByDesc('no_transaksi')->pluck('no_transaksi')
        ->first();

        // 4. Ambil nomor urut transaksi sebelumnya
        $maxIdNumber = $lastTransaksi ? (int) substr($lastTransaksi, 10, 4) : 0;

        // 5. Tambahkan 1 ke nomor urut untuk ID baru
        $kode_tr = $maxIdNumber + 1;

        // 6. Buat ID baru dengan nomor urut 4 digit
        // return $date . $kasir . sprintf('%04s', $kode_tr);
        return 'TR'.$date . sprintf('%04s', $kode_tr);

    }

    // public static function generateNoPesananKasir(){

    //     // 1. Ambil tanggal dalam format `dmY`
    //     $date = date('dmY');

    //     //tambahkan id user offsite
    //     // $kasir = Auth::user()->id;
    //     // 3. Query untuk mencari ID transaksi terbesar
    //     $lastTransaksi = PesananKasir::whereDate('created_at', Carbon::today())
    //     ->orderByDesc('no_pesanan_kasir')->pluck('no_pesanan_kasir')
    //     ->first();


    //     // 4. Ambil nomor urut transaksi sebelumnya
    //     // 4. Ambil nomor urut transaksi sebelumnya
    //     $maxIdNumber = $lastTransaksi ? (int) substr($lastTransaksi, 10, 4) : 0;

    //     // 5. Tambahkan 1 ke nomor urut untuk ID baru
    //     $kode_tr = $maxIdNumber + 1;

    //     // 6. Buat ID baru dengan nomor urut 4 digit
    //     return 'PK'.$date . sprintf('%04s', $kode_tr);

    // }
}