<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EksporAbsensiController extends Controller
{
    public function ekspor(Kelas $kelas, $bulan, $tahun)
    {
        // 1. Tentukan jumlah hari pada bulan terpilih
        $firstDayOfMonth = Carbon::create($tahun, $bulan, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $namaBulanIndo = $firstDayOfMonth->translatedFormat('F');

        // 2. Ambil seluruh siswa di kelas ini beserta relasi absensinya
        $siswas = Siswa::where('kelas_id', $kelas->id)->orderBy('nama', 'asc')->get();

        // 3. Nama file Excel yang akan diunduh
        $fileName = "Rekap_Absen_" . str_replace(' ', '_', $kelas->nama_kelas) . "_{$namaBulanIndo}_{$tahun}.xls";

        // Headers agar browser mendeteksi file sebagai MS Excel (.xls)
        $headers = [
            "Content-Type" => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"{$fileName}\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // 4. Proses render HTML Table bergaya inline CSS untuk Excel
        $callback = function() use ($kelas, $bulan, $tahun, $daysInMonth, $namaBulanIndo, $siswas) {
            $output = fopen('php://output', 'w');

            // Tulis tag pembuka HTML & CSS untuk excel
            $html = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
                <style>
                    table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11px; }
                    th { border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold; }
                    td { border: 1px solid #000000; vertical-align: middle; }
                    .header-title { font-size: 14px; font-weight: bold; text-align: center; }
                    .libur { background-color: #9ca3af !important; color: #4b5563 !important; text-align: center; font-weight: bold; }
                    .hadir { background-color: #a7f3d0 !important; color: #047857 !important; text-align: center; font-weight: bold; }
                    .sakit { background-color: #bfdbfe !important; color: #1d4ed8 !important; text-align: center; font-weight: bold; }
                    .izin { background-color: #f3f4f6 !important; color: #374151 !important; text-align: center; font-weight: bold; }
                    .alpa { background-color: #fca5a5 !important; color: #b91c1c !important; text-align: center; font-weight: bold; }
                    .kosong { text-align: center; color: #9ca3af; }
                </style>
            </head>
            <body>
                <table>
                    <tr>
                        <td colspan="' . ($daysInMonth + 6) . '" class="header-title">REKAPITULASI ABSENSI BULANAN</td>
                    </tr>
                    <tr>
                        <td colspan="' . ($daysInMonth + 6) . '" style="text-align: center; font-weight: bold;">
                            Kelas: ' . $kelas->nama_kelas . ' | Wali Kelas: ' . ($kelas->waliKelas->nama ?? "-") . ' | Periode: ' . $namaBulanIndo . ' ' . $tahun . '
                        </td>
                    </tr>
                    <tr><td colspan="' . ($daysInMonth + 6) . '"></td></tr>
                    <thead>
                        <tr style="background-color: #1e3a8a; color: #ffffff;">
                            <th rowspan="2" style="width: 30px;">No</th>
                            <th rowspan="2" style="width: 80px;">NIS</th>
                            <th rowspan="2" style="width: 200px;">Nama Siswa</th>
                            <th colspan="' . $daysInMonth . '">Tanggal</th>
                            <th colspan="3" style="background-color: #0f172a; color: #ffffff;">Kalkulasi</th>
                        </tr>
                        <tr style="background-color: #3b82f6; color: #ffffff;">';

                        // Render nomor tanggal 1 s.d 30/31
                        for ($d = 1; $d <= $daysInMonth; $d++) {
                            $html .= '<th style="width: 25px;">' . $d . '</th>';
                        }

            $html .= '
                            <th style="background-color: #1d4ed8; color: #ffffff; width: 30px;">S</th>
                            <th style="background-color: #374151; color: #ffffff; width: 30px;">I</th>
                            <th style="background-color: #b91c1c; color: #ffffff; width: 30px;">A</th>
                        </tr>
                    </thead>
                    <tbody>';

            // Loop untuk setiap baris data Siswa
            $no = 1;
            foreach ($siswas as $siswa) {
                $html .= '<tr>';
                $html .= '<td style="text-align: center;">' . $no++ . '</td>';
                $html .= '<td style="text-align: center; mso-number-format:\'@\';">' . $siswa->nis . '</td>'; // Paksa format NIS sebagai teks di Excel
                $html .= '<td style="padding-left: 5px;">' . $siswa->nama . '</td>';

                $counterS = 0;
                $counterI = 0;
                $counterA = 0;

                // Loop Tanggal 1 s.d Selesai
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $currentDateString = sprintf('%s-%s-%02d', $tahun, $bulan, $d);
                    $carbonDate = Carbon::parse($currentDateString);

                    // A. LOGIKA UTAMA: Jumat Auto Libur
                    $isFriday = $carbonDate->isFriday();

                    // B. Cek apakah ada di Kalender Hari Libur database
                    $isHolidayCalendar = HariLibur::where('tanggal', $currentDateString)->exists();

                    if ($isFriday || $isHolidayCalendar) {
                        $html .= '<td class="libur">L</td>';
                    } else {
                        // Cari log absen siswa pada tanggal ini
                        $absensi = Absensi::where('tanggal', $currentDateString)
                            ->where('absensable_type', Siswa::class)
                            ->where('absensable_id', $siswa->id)
                            ->first();

                        if ($absensi) {
                            $status = $absensi->status_masuk;

                            if ($status === 'Tepat Waktu' || $status === 'Terlambat') {
                                $html .= '<td class="hadir">H</td>';
                            } elseif ($status === 'Sakit') {
                                $html .= '<td class="sakit">S</td>';
                                $counterS++;
                            } elseif ($status === 'Izin') {
                                $html .= '<td class="izin">I</td>';
                                $counterI++;
                            } elseif ($status === 'Alpa') {
                                $html .= '<td class="alpa">A</td>';
                                $counterA++;
                            } else {
                                $html .= '<td class="kosong">-</td>';
                            }
                        } else {
                            $html .= '<td class="kosong">-</td>';
                        }
                    }
                }

                // Tulis kalkulasi total S, I, A di kolom paling kanan
                $html .= '<td style="text-align: center; font-weight: bold; background-color: #eff6ff;">' . $counterS . '</td>';
                $html .= '<td style="text-align: center; font-weight: bold; background-color: #f9fafb;">' . $counterI . '</td>';
                $html .= '<td style="text-align: center; font-weight: bold; background-color: #fef2f2;">' . $counterA . '</td>';
                $html .= '</tr>';
            }

            $html .= '
                    </tbody>
                </table>
            </body>
            </html>';

            // Tulis dan keluarkan data HTML ke stream file
            echo $html;
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
