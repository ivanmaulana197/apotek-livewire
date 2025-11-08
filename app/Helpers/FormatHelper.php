<?php
namespace App\Helpers;

use Carbon\Carbon;

class FormatHelper
{
    public static function formatStringToNumber($number)
    {
        if ($number === null || $number === '') {
            return null;
        } else {
            // Hapus titik sebagai pemisah ribuan
            $number = str_replace('.', '', $number);

            // Ganti koma sebagai pemisah desimal menjadi titik
            $number = str_replace(',', '.', $number);

            // Jika angka mengandung desimal, kembalikan sebagai float
            if (strpos($number, '.')) {
                return (float) $number;
            }

            // Jika tidak ada desimal, kembalikan sebagai integer
            return (int) $number;
        }
    }

    // public static function formatNumberToString($number)
    // {
    //     if ($number === null) {
    //         return null;
    //     } else {
    //         // Pisahkan integer dan desimal
    //         $parts = explode('.', number_format($number, 2, '.', ''));

    //         // Format bagian integer dengan titik sebagai pemisah ribuan
    //         $integerPart = number_format($parts[0], 0, '', '.');

    //         // Jika ada desimal, ubah titik menjadi koma
    //         if (isset($parts[1]) && (float) $parts[1] > 0) {
    //             return $integerPart . ',' . $parts[1];
    //         }

    //         // Jika tidak ada desimal, kembalikan hanya bagian integer
    //         return $integerPart;
    //     }
    // }

    public static function formatNumberToString($number)
    {
        if ($number === null) {
            return null;
        }

        // Ubah ke string agar tidak kehilangan presisi desimal
        $numberStr = (string) $number;

        // Pisahkan integer dan desimal
        $parts = explode('.', $numberStr);

        // Format bagian integer (pakai titik untuk ribuan)
        $integerPart = number_format((int) $parts[0], 0, '', '.');

        // Jika ada desimal
        if (isset($parts[1]) && $parts[1] !== '') {
            // Hilangkan nol di akhir desimal
            $decimalPart = rtrim($parts[1], '0');

            // Jika setelah di-trim masih ada angka desimal
            if ($decimalPart !== '') {
                return $integerPart . ',' . $decimalPart;
            }
        }

        // Jika tidak ada desimal atau desimalnya cuma nol semua
        return $integerPart;
    }

    public static function formatDate($tgl, $show_time = false, $with_second = false, $mode = 'full')
    {
        if (! $tgl) {
            return null;
        }

        try {
            $carbonDate = Carbon::parse($tgl);

            $bulan = [
                1  => 'Januari',
                2  => 'Februari',
                3  => 'Maret',
                4  => 'April',
                5  => 'Mei',
                6  => 'Juni',
                7  => 'Juli',
                8  => 'Agustus',
                9  => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];

            $tanggal   = $carbonDate->day;
            $bulanIndo = $bulan[$carbonDate->month];
            $tahun     = $carbonDate->year;

            // Tentukan hasil berdasarkan mode
            switch ($mode) {
                case 'bulan':
                    $formattedDate = $bulanIndo;
                    break;
                case 'tahun':
                    $formattedDate = $tahun;
                    break;
                default: // 'full'
                    $formattedDate = "$tanggal $bulanIndo $tahun";
            }

            if ($show_time && $mode === 'full') {
                $jam           = $carbonDate->format($with_second ? 'H:i:s' : 'H:i');
                $formattedDate = "$jam, $formattedDate";
            }

            return $formattedDate;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function formatDateStringToDate($tanggal, $format = 'Y-m-d')
    {
        if (! $tanggal) {
            return null;
        }
        $bulanIndo = [
            'Januari'   => 'January',
            'Februari'  => 'February',
            'Maret'     => 'March',
            'April'     => 'April',
            'Mei'       => 'May',
            'Juni'      => 'June',
            'Juli'      => 'July',
            'Agustus'   => 'August',
            'September' => 'September',
            'Oktober'   => 'October',
            'November'  => 'November',
            'Desember'  => 'December',
        ];

        foreach ($bulanIndo as $indo => $en) {
            if (str_contains($tanggal, $indo)) {
                $tanggal = str_replace($indo, $en, $tanggal);
                break;
            }
        }

        try {
            return Carbon::parse($tanggal)->format($format);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function formatTomSelect($data, $value, $text)
    {
        return collect($data)->map(function ($item) use ($text, $value) {
            return [
                'text'  => $item[$text],
                'value' => $item[$value],
            ];
        });
    }

}
