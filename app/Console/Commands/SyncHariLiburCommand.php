<?php

namespace App\Console\Commands;

use App\Services\HariLiburService;
use Illuminate\Console\Command;

class SyncHariLiburCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-hari-libur {year? : Tahun yang ingin disinkronkan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan daftar hari libur nasional Indonesia dari kalender resmi ke database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $year = $this->argument('year') ? (int) $this->argument('year') : null;
        $this->info("Menghubungi kalender resmi libur nasional Indonesia...");

        $count = HariLiburService::syncNationalHolidays($year);

        $this->info("Berhasil menyinkronkan {$count} hari libur nasional dan cuti bersama.");

        return Command::SUCCESS;
    }
}
