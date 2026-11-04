<?php

namespace App\Services;

use App\Models\HariLibur;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HariLiburService
{
    /**
     * URL Kalender Resmi Hari Libur Nasional Indonesia dari Google Calendar (iCal)
     */
    protected const GOOGLE_CALENDAR_ICAL_URL = 'https://calendar.google.com/calendar/ical/id.indonesian%23holiday%40group.v.calendar.google.com/public/basic.ics';

    /**
     * Sinkronisasikan hari libur nasional Indonesia ke tabel hari_liburs
     */
    public static function syncNationalHolidays(?int $targetYear = null): int
    {
        try {
            $response = Http::timeout(10)->get(self::GOOGLE_CALENDAR_ICAL_URL);
            if (!$response->successful()) {
                Log::warning('Gagal mengunduh iCal hari libur Google Calendar: ' . $response->status());
                return 0;
            }

            $body = $response->body();
            preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $body, $matches);

            $currentYear = $targetYear ?: (int) date('Y');
            $syncedCount = 0;

            foreach ($matches[1] as $event) {
                preg_match('/DTSTART(?:;VALUE=DATE)?:(\d{8})/', $event, $mDate);
                preg_match('/SUMMARY:(.*?)(?:\r?\n[A-Z]|\r?\nEND)/s', $event, $mSummary);
                preg_match('/DESCRIPTION:(.*?)(?:\r?\n[A-Z]|\r?\nEND)/s', $event, $mDesc);

                if (!empty($mDate[1]) && !empty($mSummary[1])) {
                    $rawDate = $mDate[1];
                    $dateFormatted = substr($rawDate, 0, 4) . '-' . substr($rawDate, 4, 2) . '-' . substr($rawDate, 6, 2);
                    $summary = trim(preg_replace('/\r?\n\s+/', '', $mSummary[1]));
                    $desc = trim(preg_replace('/\r?\n\s+/', '', $mDesc[1] ?? ''));

                    // Filter hanya hari libur resmi dan cuti bersama (bukan perayaan biasa)
                    $isOfficialHoliday = (stripos($desc, 'Hari libur nasional') !== false) ||
                                         (stripos($desc, 'Cuti bersama') !== false) ||
                                         (stripos($summary, 'Cuti Bersama') !== false);

                    if (!$isOfficialHoliday) {
                        continue;
                    }

                    $year = (int) substr($rawDate, 0, 4);
                    // Ambil tahun berjalan s/d tahun depan (+1)
                    if ($year >= $currentYear && $year <= ($currentYear + 1)) {
                        HariLibur::updateOrCreate(
                            ['tanggal' => $dateFormatted],
                            ['keterangan' => $summary]
                        );
                        $syncedCount++;
                    }
                }
            }

            return $syncedCount;
        } catch (\Throwable $e) {
            Log::error('Error saat sinkronisasi Hari Libur Nasional: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Dapatkan daftar hari libur yang jatuh tempo dalam $daysAhead hari ke depan
     */
    public static function getUpcomingHolidays(int $daysAhead = 3): Collection
    {
        $targetDate = Carbon::today()->addDays($daysAhead)->toDateString();

        return HariLibur::whereDate('tanggal', $targetDate)->get();
    }
}
