<?php
namespace App\Observers;

use App\Jobs\SendTelegramNotification;
use App\Models\Barang\Barang;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

// use App\Models\Barang;

class BarangObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Barang "created" event.
     */
    public function created(Barang $barang): void
    {
        //
    }

    /**
     * Handle the Barang "updated" event.
     */
    public function updated(Barang $barang): void
    {
        // dd($barang);
        if ($barang->wasChanged('stok')) {

            // $old = $barang->getOriginal('stok');
            $new         = $barang->stok;
            $nama_satuan = $barang->satuan_utama->nama;
            if ($new <= 0) {
                $message = "⚠️ <b>Stok Barang HABIS!</b>\n"
                    . "Nama: <b>{$barang->nama}</b>\n"
                    . "Sisa Stok: {$new} / {$nama_satuan}\n"
                    . "Stok Minimal: {$barang->stok_min} / {$nama_satuan}\n\n"
                    . "SEGERA MEMESAN BARANG BARU\n";
                // . "Perubahan: {$old} ➜ {$new}";

                SendTelegramNotification::dispatch($message);
                return;
            } elseif ($new <= $barang->stok_min) {
                $message = "⚠️ <b>Stok Barang Menipis!</b>\n"
                    . "Nama: <b>{$barang->nama}</b>\n"
                    . "Kode: <code>{$barang->kode}</code>\n"
                    . "Sisa Stok: {$new} / {$nama_satuan}\n"
                    . "Stok Minimal: {$barang->stok_min} / {$nama_satuan}\n\n"
                    . "SEGERA MEMESAN BARANG BARU\n";
                // . "Perubahan: {$old} ➜ {$new}";

                SendTelegramNotification::dispatch($message);
            }
        }
    }

    /**
     * Handle the Barang "deleted" event.
     */
    public function deleted(Barang $barang): void
    {
        //
    }

    /**
     * Handle the Barang "restored" event.
     */
    public function restored(Barang $barang): void
    {
        //
    }

    /**
     * Handle the Barang "force deleted" event.
     */
    public function forceDeleted(Barang $barang): void
    {
        //
    }
}
