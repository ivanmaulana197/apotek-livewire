<?php
namespace App\Console\Commands;

use App\Helpers\FormatHelper;
use App\Jobs\SendTelegramNotification;
use App\Models\Transaksi\Transaksi;
use Illuminate\Console\Command;

class SendDailyTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:send-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim total transaksi harian ke Telegram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->format('Y-m-d');

        // Hitung total transaksi hari ini
        $totals = Transaksi::whereDate('transaksi.created_at', $today)
            ->join('metode_pembayaran', 'metode_pembayaran.id', '=', 'transaksi.id_metode_pembayaran')
            ->where('status', 'TERJUAL')
            ->selectRaw('
            SUM(total_bayar) as totalTransaksi,
            SUM(CASE WHEN metode_pembayaran.nama = "tunai" THEN total_bayar ELSE 0 END) as totalTunai,
            SUM(CASE WHEN metode_pembayaran.nama != "tunai" THEN total_bayar ELSE 0 END) as totalNonTunai,
            SUM(CASE WHEN shift_transaksi = "PAGI" THEN total_bayar ELSE 0 END) as totalPagi,
            SUM(CASE WHEN shift_transaksi = "MALAM" THEN total_bayar ELSE 0 END) as totalMalam,
            SUM(CASE WHEN jenis_transaksi = "umum" THEN total_bayar ELSE 0 END) as totalUmum,
            SUM(CASE WHEN jenis_transaksi = "membership" THEN total_bayar ELSE 0 END) as totalMembership,
            SUM(CASE WHEN jenis_transaksi = "resep" THEN total_bayar ELSE 0 END) as totalResep
        ')
            ->first();
        $totalTransaksi  = FormatHelper::formatNumberToString($totals->totalTransaksi);
        $totalTunai      = FormatHelper::formatNumberToString($totals->totalTunai);
        $totalNonTunai   = FormatHelper::formatNumberToString($totals->totalNonTunai);
        $totalPagi       = FormatHelper::formatNumberToString($totals->totalPagi);
        $totalMalam      = FormatHelper::formatNumberToString($totals->totalMalam);
        $totalUmum       = FormatHelper::formatNumberToString($totals->totalUmum);
        $totalMembership = FormatHelper::formatNumberToString($totals->totalMembership);
        $totalResep      = FormatHelper::formatNumberToString($totals->totalResep);

        $message = "📊 Laporan Transaksi Hari Ini ($today)\n\n";
        $message .= "Total Transaksi: Rp $totalTransaksi\n";
        $message .= "Tunai: Rp $totalTunai | Non-Tunai: Rp $totalNonTunai\n";
        $message .= "Shift Pagi: Rp $totalPagi | Shift Malam: Rp $totalMalam\n";
        $message .= "Umum: Rp $totalUmum | Membership: Rp $totalMembership | Resep: Rp $totalResep";

        SendTelegramNotification::dispatch($message);
        $this->info('Daily transaction report sent successfully!');
    }
}
