<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Color;

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

    // METODE SAKTI: Ekspor Semua Kelas Sekaligus ke dalam Satu File Excel Ber-Worksheet Banyak
    public function eksporSemuaKelasLama($bulan, $tahun)
    {
        // Ambil semua data kelas
        $kelasQuery = Kelas::orderBy('nama_kelas', 'asc')->get();

        $firstDayOfMonth = Carbon::create($tahun, $bulan, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $namaBulanIndo = $firstDayOfMonth->translatedFormat('F');

        $fileName = "Rekap_Absen_Semua_Kelas_{$namaBulanIndo}_{$tahun}.xls";

        $headers = [
            "Content-Type" => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"{$fileName}\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($kelasQuery, $bulan, $tahun, $daysInMonth, $namaBulanIndo) {
            $output = fopen('php://output', 'w');

            // HEADER XML KHUSUS: Menginstruksikan Excel untuk memecah halaman menjadi Sheet/Tab di bagian bawah
            $html = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
                <!--[if gte mso 9]>
                <xml>
                    <x:ExcelWorkbook>
                        <x:ExcelWorksheets>';

            // DAFTARKAN NAMA TAB SESUAI NAMA KELAS
            foreach ($kelasQuery as $kelas) {
                $html .= '
                            <x:ExcelWorksheet>
                                <x:Name>' . htmlspecialchars($kelas->nama_kelas) . '</x:Name>
                                <x:WorksheetOptions>
                                    <x:ProtectContents>False</x:ProtectContents>
                                </x:WorksheetOptions>
                            </x:ExcelWorksheet>';
            }

            $html .= '
                        </x:ExcelWorksheets>
                    </x:ExcelWorkbook>
                </xml>
                <![endif]-->
                <style>
                    table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11px; margin-bottom: 30px; }
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
            <body>';

            // LOOP DAN RENDER TABEL UNTUK SETIAP KELAS
            foreach ($kelasQuery as $index => $kelas) {
                // Trik pemisah Sheet Excel: gunakan tag break berkemampuan pemisah halaman mso
                if ($index > 0) {
                    $html .= '<br style="page-break-before: always; mso-data-placement:same-cell;" />';
                }

                $siswas = Siswa::where('kelas_id', $kelas->id)->orderBy('nama', 'asc')->get();

                $html .= '
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

                $no = 1;
                foreach ($siswas as $siswa) {
                    $html .= '<tr>';
                    $html .= '<td style="text-align: center;">' . $no++ . '</td>';
                    $html .= '<td style="text-align: center; mso-number-format:\'@\';">' . $siswa->nis . '</td>';
                    $html .= '<td style="padding-left: 5px;">' . $siswa->nama . '</td>';

                    $counterS = 0;
                    $counterI = 0;
                    $counterA = 0;

                    for ($d = 1; $d <= $daysInMonth; $d++) {
                        $currentDateString = sprintf('%s-%s-%02d', $tahun, $bulan, $d);
                        $carbonDate = Carbon::parse($currentDateString);
                        $isFriday = $carbonDate->isFriday();
                        $isHolidayCalendar = HariLibur::where('tanggal', $currentDateString)->exists();

                        if ($isFriday || $isHolidayCalendar) {
                            $html .= '<td class="libur">L</td>';
                        } else {
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

                    $html .= '<td style="text-align: center; font-weight: bold; background-color: #eff6ff;">' . $counterS . '</td>';
                    $html .= '<td style="text-align: center; font-weight: bold; background-color: #f9fafb;">' . $counterI . '</td>';
                    $html .= '<td style="text-align: center; font-weight: bold; background-color: #fef2f2;">' . $counterA . '</td>';
                    $html .= '</tr>';
                }

                $html .= '
                    </tbody>
                </table>';
            }

            $html .= '
            </body>
            </html>';

            echo $html;
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }



    // ============================================================================
// EKSPOR SEMUA KELAS KE SATU FILE EXCEL
// Setiap kelas = 1 worksheet
// ============================================================================

public function eksporSemuaKelas($bulan, $tahun)
{
    /*
    |--------------------------------------------------------------------------
    | 1. AMBIL SEMUA KELAS
    |--------------------------------------------------------------------------
    */

    $kelasQuery = Kelas::with('waliKelas')
        ->orderBy('nama_kelas', 'asc')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 2. INFORMASI PERIODE
    |--------------------------------------------------------------------------
    */

    $firstDayOfMonth = Carbon::create($tahun, $bulan, 1);

    $daysInMonth = $firstDayOfMonth->daysInMonth;

    $namaBulanIndo = $firstDayOfMonth->translatedFormat('F');

    /*
    |--------------------------------------------------------------------------
    | 3. BUAT WORKBOOK EXCEL
    |--------------------------------------------------------------------------
    */

    $spreadsheet = new Spreadsheet();

    /*
    |--------------------------------------------------------------------------
    | 4. HAPUS SHEET DEFAULT
    |--------------------------------------------------------------------------
    |
    | Kita akan membuat sheet sendiri untuk setiap kelas.
    |
    */

    $spreadsheet->removeSheetByIndex(0);

    /*
    |--------------------------------------------------------------------------
    | 5. PRELOAD DATA HARI LIBUR
    |--------------------------------------------------------------------------
    |
    | Daripada melakukan:
    |
    | HariLibur::where(...)->exists()
    |
    | berkali-kali, kita ambil seluruh hari libur dalam bulan sekaligus.
    |
    */

    $tanggalAwal = Carbon::create($tahun, $bulan, 1)
        ->startOfMonth()
        ->toDateString();

    $tanggalAkhir = Carbon::create($tahun, $bulan, 1)
        ->endOfMonth()
        ->toDateString();

    $hariLibur = HariLibur::whereBetween(
        'tanggal',
        [$tanggalAwal, $tanggalAkhir]
    )
        ->pluck('tanggal')
        ->map(function ($tanggal) {
            return Carbon::parse($tanggal)->format('Y-m-d');
        })
        ->flip();

    /*
    |--------------------------------------------------------------------------
    | 6. LOOP SETIAP KELAS
    |--------------------------------------------------------------------------
    */

    foreach ($kelasQuery as $kelas) {

        /*
        |--------------------------------------------------------------------------
        | 7. BUAT SHEET
        |--------------------------------------------------------------------------
        */

        $sheet = $spreadsheet->createSheet();

        /*
        |--------------------------------------------------------------------------
        | 8. NAMA SHEET
        |--------------------------------------------------------------------------
        |
        | Excel mempunyai beberapa aturan:
        |
        | - Maksimal 31 karakter
        | - Tidak boleh mengandung:
        |   \ / ? * [ ] :
        |
        */

        $namaSheet = preg_replace(
            '/[\\\\\/\?\*\[\]\:]/',
            '-',
            $kelas->nama_kelas
        );

        $namaSheet = trim($namaSheet);

        $namaSheet = mb_substr(
            $namaSheet,
            0,
            31
        );

        if (empty($namaSheet)) {
            $namaSheet = 'Kelas';
        }

        /*
        |--------------------------------------------------------------------------
        | 9. PASTIKAN NAMA SHEET UNIK
        |--------------------------------------------------------------------------
        |
        | Misalnya ada:
        |
        | 10-A
        | 10/A
        |
        | Setelah karakter dibersihkan keduanya bisa menjadi:
        |
        | 10-A
        |
        | Maka kita beri suffix jika terjadi duplikasi.
        |
        */

        $namaSheetOriginal = $namaSheet;
        $counterSheet = 1;

        while ($spreadsheet->sheetNameExists($namaSheet)) {

            $suffix = '-' . $counterSheet;

            $namaSheet = mb_substr(
                $namaSheetOriginal,
                0,
                31 - mb_strlen($suffix)
            ) . $suffix;

            $counterSheet++;
        }

        $sheet->setTitle($namaSheet);

        /*
        |--------------------------------------------------------------------------
        | 10. AMBIL SISWA KELAS
        |--------------------------------------------------------------------------
        */

        $siswas = Siswa::where(
            'kelas_id',
            $kelas->id
        )
            ->orderBy('nama', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 11. PRELOAD ABSENSI SEMUA SISWA KELAS
        |--------------------------------------------------------------------------
        |
        | Ini jauh lebih efisien dibanding query Absensi setiap:
        |
        | siswa × tanggal
        |
        */

        $siswaIds = $siswas->pluck('id');

        $absensiMap = [];

        if ($siswaIds->isNotEmpty()) {

            $absensiData = Absensi::whereBetween(
                'tanggal',
                [$tanggalAwal, $tanggalAkhir]
            )
                ->where(
                    'absensable_type',
                    Siswa::class
                )
                ->whereIn(
                    'absensable_id',
                    $siswaIds
                )
                ->get();

            foreach ($absensiData as $absensi) {

                $tanggalAbsensi = Carbon::parse(
                    $absensi->tanggal
                )->format('Y-m-d');

                $key = $absensi->absensable_id . '|' . $tanggalAbsensi;

                $absensiMap[$key] = $absensi;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 12. PERHITUNGAN KOLOM
        |--------------------------------------------------------------------------
        |
        | A = No
        | B = NIS
        | C = Nama
        | D dst = Tanggal
        | setelah tanggal = S I A
        |
        */

        $kolomTanggalAwal = 4;

        $kolomKalkulasiAwal = $kolomTanggalAwal + $daysInMonth;

        $kolomKalkulasiS = $kolomKalkulasiAwal;

        $kolomKalkulasiI = $kolomKalkulasiAwal + 1;

        $kolomKalkulasiA = $kolomKalkulasiAwal + 2;

        $lastColumn = Coordinate::stringFromColumnIndex(
            $kolomKalkulasiA
        );

        /*
        |--------------------------------------------------------------------------
        | 13. JUDUL
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            "A1:{$lastColumn}1"
        );

        $sheet->setCellValue(
            'A1',
            'REKAPITULASI ABSENSI BULANAN'
        );

        $sheet->getStyle(
            "A1:{$lastColumn}1"
        )->applyFromArray([

            'font' => [
                'name' => 'Calibri',
                'size' => 14,
                'bold' => true,
                'color' => [
                    'rgb' => '000000',
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

        ]);

        $sheet->getRowDimension(1)
            ->setRowHeight(25);

        /*
        |--------------------------------------------------------------------------
        | 14. INFORMASI KELAS
        |--------------------------------------------------------------------------
        */

        $waliKelas = optional(
            $kelas->waliKelas
        )->nama ?? '-';

        $sheet->mergeCells(
            "A2:{$lastColumn}2"
        );

        $sheet->setCellValue(
            'A2',
            "Kelas: {$kelas->nama_kelas} | " .
            "Wali Kelas: {$waliKelas} | " .
            "Periode: {$namaBulanIndo} {$tahun}"
        );

        $sheet->getStyle(
            "A2:{$lastColumn}2"
        )->applyFromArray([

            'font' => [
                'name' => 'Calibri',
                'size' => 11,
                'bold' => true,
                'color' => [
                    'rgb' => '000000',
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

        ]);

        $sheet->getRowDimension(2)
            ->setRowHeight(22);

        /*
        |--------------------------------------------------------------------------
        | 15. BARIS KOSONG
        |--------------------------------------------------------------------------
        */

        $sheet->getRowDimension(3)
            ->setRowHeight(18);

        /*
        |--------------------------------------------------------------------------
        | 16. HEADER TABEL
        |--------------------------------------------------------------------------
        */

        // ---------------------------------------------------------------------
        // NO
        // ---------------------------------------------------------------------

        $sheet->setCellValue(
            'A4',
            'No'
        );

        $sheet->mergeCells(
            'A4:A5'
        );

        // ---------------------------------------------------------------------
        // NIS
        // ---------------------------------------------------------------------

        $sheet->setCellValue(
            'B4',
            'NIS'
        );

        $sheet->mergeCells(
            'B4:B5'
        );

        // ---------------------------------------------------------------------
        // NAMA SISWA
        // ---------------------------------------------------------------------

        $sheet->setCellValue(
            'C4',
            'Nama Siswa'
        );

        $sheet->mergeCells(
            'C4:C5'
        );

        // ---------------------------------------------------------------------
        // TANGGAL
        // ---------------------------------------------------------------------

        $tanggalAwalColumn = Coordinate::stringFromColumnIndex(
            $kolomTanggalAwal
        );

        $tanggalAkhirColumn = Coordinate::stringFromColumnIndex(
            $kolomTanggalAwal + $daysInMonth - 1
        );

        $sheet->mergeCells(
            "{$tanggalAwalColumn}4:{$tanggalAkhirColumn}4"
        );

        $sheet->setCellValue(
            "{$tanggalAwalColumn}4",
            'Tanggal'
        );

        // ---------------------------------------------------------------------
        // KALKULASI
        // ---------------------------------------------------------------------

        $kalkulasiAwalColumn = Coordinate::stringFromColumnIndex(
            $kolomKalkulasiAwal
        );

        $kalkulasiAkhirColumn = Coordinate::stringFromColumnIndex(
            $kolomKalkulasiA
        );

        $sheet->mergeCells(
            "{$kalkulasiAwalColumn}4:{$kalkulasiAkhirColumn}4"
        );

        $sheet->setCellValue(
            "{$kalkulasiAwalColumn}4",
            'Kalkulasi'
        );

        /*
        |--------------------------------------------------------------------------
        | 17. HEADER NOMOR TANGGAL
        |--------------------------------------------------------------------------
        */

        for ($d = 1; $d <= $daysInMonth; $d++) {

            $column = Coordinate::stringFromColumnIndex(
                $kolomTanggalAwal + $d - 1
            );

            $sheet->setCellValue(
                "{$column}5",
                $d
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 18. HEADER S / I / A
        |--------------------------------------------------------------------------
        */

        $columnS = Coordinate::stringFromColumnIndex(
            $kolomKalkulasiS
        );

        $columnI = Coordinate::stringFromColumnIndex(
            $kolomKalkulasiI
        );

        $columnA = Coordinate::stringFromColumnIndex(
            $kolomKalkulasiA
        );

        $sheet->setCellValue(
            "{$columnS}5",
            'S'
        );

        $sheet->setCellValue(
            "{$columnI}5",
            'I'
        );

        $sheet->setCellValue(
            "{$columnA}5",
            'A'
        );

        /*
        |--------------------------------------------------------------------------
        | 19. STYLE HEADER UTAMA
        |--------------------------------------------------------------------------
        */

        $headerStyle = [

            'font' => [
                'name' => 'Calibri',
                'size' => 11,
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],

            'fill' => [
                'fillType' =>
                    Fill::FILL_SOLID,

                'startColor' => [
                    'rgb' => '24418C',
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,

                'wrapText' => false,
            ],

            'borders' => [

                'top' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' => '000000',
                    ],
                ],

                'bottom' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' => '000000',
                    ],
                ],

                'left' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' => '000000',
                    ],
                ],

                'right' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' => '000000',
                    ],
                ],

            ],

        ];

        $sheet->getStyle(
            "A4:{$lastColumn}5"
        )->applyFromArray(
            $headerStyle
        );

        /*
        |--------------------------------------------------------------------------
        | 20. WARNA HEADER KALKULASI
        |--------------------------------------------------------------------------
        */

        // S = Biru
        $sheet->getStyle(
            "{$columnS}5"
        )->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            );

        $sheet->getStyle(
            "{$columnS}5"
        )->getFill()
            ->getStartColor()
            ->setRGB('1D4ED8');

        // I = Abu-abu
        $sheet->getStyle(
            "{$columnI}5"
        )->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            );

        $sheet->getStyle(
            "{$columnI}5"
        )->getFill()
            ->getStartColor()
            ->setRGB('374151');

        // A = Merah
        $sheet->getStyle(
            "{$columnA}5"
        )->getFill()
            ->setFillType(
                Fill::FILL_SOLID
            );

        $sheet->getStyle(
            "{$columnA}5"
        )->getFill()
            ->getStartColor()
            ->setRGB('B91C1C');

        /*
        |--------------------------------------------------------------------------
        | 21. DATA SISWA
        |--------------------------------------------------------------------------
        */

        $row = 6;

        $no = 1;

        foreach ($siswas as $siswa) {

            /*
            |--------------------------------------------------------------------------
            | NO
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "A{$row}",
                $no
            );

            /*
            |--------------------------------------------------------------------------
            | NIS
            |--------------------------------------------------------------------------
            |
            | Dipaksa menjadi TEXT supaya:
            |
            | 00123
            |
            | tidak berubah menjadi:
            |
            | 123
            |
            */

            $sheet->setCellValueExplicit(
                "B{$row}",
                (string) $siswa->nis,
                DataType::TYPE_STRING
            );

            /*
            |--------------------------------------------------------------------------
            | NAMA
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "C{$row}",
                $siswa->nama
            );

            /*
            |--------------------------------------------------------------------------
            | COUNTER
            |--------------------------------------------------------------------------
            */

            $counterS = 0;
            $counterI = 0;
            $counterA = 0;

            /*
            |--------------------------------------------------------------------------
            | ABSENSI SETIAP TANGGAL
            |--------------------------------------------------------------------------
            */

            for (
                $d = 1;
                $d <= $daysInMonth;
                $d++
            ) {

                $currentDateString = sprintf(
                    '%04d-%02d-%02d',
                    $tahun,
                    $bulan,
                    $d
                );

                $carbonDate = Carbon::create(
                    $tahun,
                    $bulan,
                    $d
                );

                $column = Coordinate::stringFromColumnIndex(
                    $kolomTanggalAwal + $d - 1
                );

                /*
                |--------------------------------------------------------------------------
                | CEK HARI JUMAT
                |--------------------------------------------------------------------------
                */

                $isFriday = $carbonDate->isFriday();

                /*
                |--------------------------------------------------------------------------
                | CEK HARI LIBUR
                |--------------------------------------------------------------------------
                */

                $isHoliday = isset(
                    $hariLibur[$currentDateString]
                );

                /*
                |--------------------------------------------------------------------------
                | JIKA LIBUR
                |--------------------------------------------------------------------------
                */

                if ($isFriday || $isHoliday) {

                    $sheet->setCellValue(
                        "{$column}{$row}",
                        'L'
                    );

                    $sheet->getStyle(
                        "{$column}{$row}"
                    )->applyFromArray([

                        'font' => [
                            'name' => 'Calibri',
                            'size' => 11,
                            'bold' => true,
                            'color' => [
                                'rgb' => '4B5563',
                            ],
                        ],

                        'fill' => [
                            'fillType' =>
                                Fill::FILL_SOLID,

                            'startColor' => [
                                'rgb' => '9CA3AF',
                            ],
                        ],

                        'alignment' => [
                            'horizontal' =>
                                Alignment::HORIZONTAL_CENTER,

                            'vertical' =>
                                Alignment::VERTICAL_CENTER,
                        ],

                    ]);

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | CARI ABSENSI DARI MAP
                    |--------------------------------------------------------------------------
                    */

                    $key = $siswa->id .
                        '|' .
                        $currentDateString;

                    $absensi = $absensiMap[$key] ?? null;

                    /*
                    |--------------------------------------------------------------------------
                    | ADA ABSENSI
                    |--------------------------------------------------------------------------
                    */

                    if ($absensi) {

                        $status = $absensi->status_masuk;

                        /*
                        |--------------------------------------------------------------------------
                        | HADIR
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $status === 'Tepat Waktu' ||
                            $status === 'Terlambat'
                        ) {

                            $sheet->setCellValue(
                                "{$column}{$row}",
                                'H'
                            );

                            $sheet->getStyle(
                                "{$column}{$row}"
                            )->applyFromArray([

                                'font' => [
                                    'name' => 'Calibri',
                                    'size' => 11,
                                    'bold' => true,
                                    'color' => [
                                        'rgb' => '047857',
                                    ],
                                ],

                                'fill' => [
                                    'fillType' =>
                                        Fill::FILL_SOLID,

                                    'startColor' => [
                                        'rgb' => 'A7F3D0',
                                    ],
                                ],

                                'alignment' => [
                                    'horizontal' =>
                                        Alignment::HORIZONTAL_CENTER,

                                    'vertical' =>
                                        Alignment::VERTICAL_CENTER,
                                ],

                            ]);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | SAKIT
                        |--------------------------------------------------------------------------
                        */

                        elseif ($status === 'Sakit') {

                            $sheet->setCellValue(
                                "{$column}{$row}",
                                'S'
                            );

                            $counterS++;

                            $sheet->getStyle(
                                "{$column}{$row}"
                            )->applyFromArray([

                                'font' => [
                                    'name' => 'Calibri',
                                    'size' => 11,
                                    'bold' => true,
                                    'color' => [
                                        'rgb' => '1D4ED8',
                                    ],
                                ],

                                'fill' => [
                                    'fillType' =>
                                        Fill::FILL_SOLID,

                                    'startColor' => [
                                        'rgb' => 'BFDBFE',
                                    ],
                                ],

                                'alignment' => [
                                    'horizontal' =>
                                        Alignment::HORIZONTAL_CENTER,

                                    'vertical' =>
                                        Alignment::VERTICAL_CENTER,
                                ],

                            ]);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | IZIN
                        |--------------------------------------------------------------------------
                        */

                        elseif ($status === 'Izin') {

                            $sheet->setCellValue(
                                "{$column}{$row}",
                                'I'
                            );

                            $counterI++;

                            $sheet->getStyle(
                                "{$column}{$row}"
                            )->applyFromArray([

                                'font' => [
                                    'name' => 'Calibri',
                                    'size' => 11,
                                    'bold' => true,
                                    'color' => [
                                        'rgb' => '374151',
                                    ],
                                ],

                                'fill' => [
                                    'fillType' =>
                                        Fill::FILL_SOLID,

                                    'startColor' => [
                                        'rgb' => 'F3F4F6',
                                    ],
                                ],

                                'alignment' => [
                                    'horizontal' =>
                                        Alignment::HORIZONTAL_CENTER,

                                    'vertical' =>
                                        Alignment::VERTICAL_CENTER,
                                ],

                            ]);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | ALPA
                        |--------------------------------------------------------------------------
                        */

                        elseif ($status === 'Alpa') {

                            $sheet->setCellValue(
                                "{$column}{$row}",
                                'A'
                            );

                            $counterA++;

                            $sheet->getStyle(
                                "{$column}{$row}"
                            )->applyFromArray([

                                'font' => [
                                    'name' => 'Calibri',
                                    'size' => 11,
                                    'bold' => true,
                                    'color' => [
                                        'rgb' => 'B91C1C',
                                    ],
                                ],

                                'fill' => [
                                    'fillType' =>
                                        Fill::FILL_SOLID,

                                    'startColor' => [
                                        'rgb' => 'FCA5A5',
                                    ],
                                ],

                                'alignment' => [
                                    'horizontal' =>
                                        Alignment::HORIZONTAL_CENTER,

                                    'vertical' =>
                                        Alignment::VERTICAL_CENTER,
                                ],

                            ]);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | STATUS LAIN
                        |--------------------------------------------------------------------------
                        */

                        else {

                            $sheet->setCellValue(
                                "{$column}{$row}",
                                '-'
                            );
                        }

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | TIDAK ADA ABSENSI
                    |--------------------------------------------------------------------------
                    */

                    else {

                        $sheet->setCellValue(
                            "{$column}{$row}",
                            '-'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | RATA TENGAH KOLOM TANGGAL
                    |--------------------------------------------------------------------------
                    */

                    $sheet->getStyle(
                        "{$column}{$row}"
                    )->getAlignment()
                        ->setHorizontal(
                            Alignment::HORIZONTAL_CENTER
                        )
                        ->setVertical(
                            Alignment::VERTICAL_CENTER
                        );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | KALKULASI S
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "{$columnS}{$row}",
                $counterS
            );

            /*
            |--------------------------------------------------------------------------
            | KALKULASI I
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "{$columnI}{$row}",
                $counterI
            );

            /*
            |--------------------------------------------------------------------------
            | KALKULASI A
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "{$columnA}{$row}",
                $counterA
            );

            /*
            |--------------------------------------------------------------------------
            | STYLE BARIS SISWA
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "A{$row}:{$lastColumn}{$row}"
            )->applyFromArray([

                'font' => [
                    'name' => 'Calibri',
                    'size' => 11,
                ],

                'alignment' => [
                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                ],

                'borders' => [

                    'top' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' => '000000',
                        ],
                    ],

                    'bottom' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' => '000000',
                        ],
                    ],

                    'left' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' => '000000',
                        ],
                    ],

                    'right' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' => '000000',
                        ],
                    ],

                ],

            ]);

            /*
            |--------------------------------------------------------------------------
            | NO & NIS RATA TENGAH
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "A{$row}:B{$row}"
            )->getAlignment()
                ->setHorizontal(
                    Alignment::HORIZONTAL_CENTER
                );

            /*
            |--------------------------------------------------------------------------
            | NAMA SISWA
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "C{$row}"
            )->getAlignment()
                ->setHorizontal(
                    Alignment::HORIZONTAL_LEFT
                );

            /*
            |--------------------------------------------------------------------------
            | KALKULASI RATA TENGAH
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "{$columnS}{$row}:{$columnA}{$row}"
            )->getAlignment()
                ->setHorizontal(
                    Alignment::HORIZONTAL_CENTER
                );

            /*
            |--------------------------------------------------------------------------
            | WARNA KOTAK KALKULASI
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "{$columnS}{$row}"
            )->getFill()
                ->setFillType(
                    Fill::FILL_SOLID
                );

            $sheet->getStyle(
                "{$columnS}{$row}"
            )->getFill()
                ->getStartColor()
                ->setRGB('EFF6FF');

            $sheet->getStyle(
                "{$columnI}{$row}"
            )->getFill()
                ->setFillType(
                    Fill::FILL_SOLID
                );

            $sheet->getStyle(
                "{$columnI}{$row}"
            )->getFill()
                ->getStartColor()
                ->setRGB('F9FAFB');

            $sheet->getStyle(
                "{$columnA}{$row}"
            )->getFill()
                ->setFillType(
                    Fill::FILL_SOLID
                );

            $sheet->getStyle(
                "{$columnA}{$row}"
            )->getFill()
                ->getStartColor()
                ->setRGB('FEF2F2');

            /*
            |--------------------------------------------------------------------------
            | TINGGI BARIS
            |--------------------------------------------------------------------------
            */

            $sheet->getRowDimension($row)
                ->setRowHeight(20);

            $no++;
            $row++;
        }

        /*
        |--------------------------------------------------------------------------
        | 22. BARIS TERAKHIR DATA
        |--------------------------------------------------------------------------
        */

        $lastDataRow = max(
            5,
            $row - 1
        );

        /*
        |--------------------------------------------------------------------------
        | 23. BORDER HEADER + DATA
        |--------------------------------------------------------------------------
        |
        | Pastikan seluruh tabel benar-benar memiliki border.
        |
        */

        $sheet->getStyle(
            "A4:{$lastColumn}{$lastDataRow}"
        )->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            )
            ->setColor(
                new Color('000000')
            );

        /*
        |--------------------------------------------------------------------------
        | 24. LEBAR KOLOM
        |--------------------------------------------------------------------------
        */

        // No
        $sheet->getColumnDimension('A')
            ->setWidth(5);

        // NIS
        $sheet->getColumnDimension('B')
            ->setWidth(14);

        // Nama
        $sheet->getColumnDimension('C')
            ->setWidth(28);

        // Tanggal
        for (
            $d = 1;
            $d <= $daysInMonth;
            $d++
        ) {

            $column = Coordinate::stringFromColumnIndex(
                $kolomTanggalAwal + $d - 1
            );

            $sheet->getColumnDimension($column)
                ->setWidth(4);
        }

        // S
        $sheet->getColumnDimension($columnS)
            ->setWidth(5);

        // I
        $sheet->getColumnDimension($columnI)
            ->setWidth(5);

        // A
        $sheet->getColumnDimension($columnA)
            ->setWidth(5);

        /*
        |--------------------------------------------------------------------------
        | 25. FREEZE PANE
        |--------------------------------------------------------------------------
        |
        | Ketika scroll ke bawah:
        |
        | No, NIS, Nama Siswa, dan header tetap terlihat.
        |
        */

        $sheet->freezePane('D6');

        /*
        |--------------------------------------------------------------------------
        | 26. AUTO FILTER
        |--------------------------------------------------------------------------
        */

        // if ($lastDataRow >= 6) {

        //     $sheet->setAutoFilter(
        //         "A5:{$lastColumn}{$lastDataRow}"
        //     );
        // }

        /*
        |--------------------------------------------------------------------------
        | 27. SETTING PRINT
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()
            ->setOrientation(
                PageSetup::ORIENTATION_LANDSCAPE
            );

        $sheet->getPageSetup()
            ->setPaperSize(
                PageSetup::PAPERSIZE_A4
            );

        /*
        |--------------------------------------------------------------------------
        | FIT TO PAGE
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()
            ->setFitToWidth(1);

        $sheet->getPageSetup()
            ->setFitToHeight(0);

        $sheet->getPageSetup()
            ->setFitToPage(true);

        /*
        |--------------------------------------------------------------------------
        | 28. MARGIN PRINT
        |--------------------------------------------------------------------------
        */

        $sheet->getPageMargins()
            ->setTop(0.3);

        $sheet->getPageMargins()
            ->setBottom(0.3);

        $sheet->getPageMargins()
            ->setLeft(0.2);

        $sheet->getPageMargins()
            ->setRight(0.2);

        /*
        |--------------------------------------------------------------------------
        | 29. PRINT AREA
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()
            ->setPrintArea(
                "A1:{$lastColumn}{$lastDataRow}"
            );

        /*
        |--------------------------------------------------------------------------
        | 30. ULANGI HEADER SAAT PRINT
        |--------------------------------------------------------------------------
        |
        | Baris 1-5 akan muncul kembali pada halaman berikutnya.
        |
        */

        $sheet->getPageSetup()
            ->setRowsToRepeatAtTopByStartAndEnd(
                1,
                5
            );

        /*
        |--------------------------------------------------------------------------
        | 31. HORIZONTAL CENTER
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()
            ->setHorizontalCentered(false);

        /*
        |--------------------------------------------------------------------------
        | 32. VERTICAL CENTER
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()
            ->setVerticalCentered(false);

        /*
        |--------------------------------------------------------------------------
        | 33. GRIDLINES
        |--------------------------------------------------------------------------
        |
        | Gridline Excel tetap boleh terlihat di luar tabel.
        |
        */

        $sheet->setShowGridlines(true);
    }

    /*
    |--------------------------------------------------------------------------
    | 34. PASTIKAN ADA SHEET
    |--------------------------------------------------------------------------
    */

    if ($spreadsheet->getSheetCount() === 0) {

        $sheet = $spreadsheet->createSheet();

        $sheet->setTitle('Data');
    }

    /*
    |--------------------------------------------------------------------------
    | 35. AKTIFKAN SHEET PERTAMA
    |--------------------------------------------------------------------------
    */

    $spreadsheet->setActiveSheetIndex(0);

    /*
    |--------------------------------------------------------------------------
    | 36. NAMA FILE
    |--------------------------------------------------------------------------
    */

    $fileName =
        "Rekap_Absen_Semua_Kelas_" .
        "{$namaBulanIndo}_{$tahun}.xlsx";

    /*
    |--------------------------------------------------------------------------
    | 37. WRITER
    |--------------------------------------------------------------------------
    */

    $writer = new Xlsx(
        $spreadsheet
    );

    /*
    |--------------------------------------------------------------------------
    | 38. DOWNLOAD
    |--------------------------------------------------------------------------
    */

    return response()->streamDownload(
        function () use ($writer) {

            $writer->save(
                'php://output'
            );
        },

        $fileName,

        [
            'Content-Type' =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

            'Cache-Control' =>
                'max-age=0',

            'Pragma' =>
                'public',
        ]
    );
}


}
