<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaSetting extends Model
{
    protected $guarded = []; // Tidak ada kolom yang dilindungi, semua bisa diisi massal

    protected function casts(): array
    {
        return [
            'notif_wa_wali_masuk' => 'boolean',
            'notif_wa_wali_pulang' => 'boolean',
            'notif_wa_wali_alpa' => 'boolean',
            'notif_wa_guru_masuk' => 'boolean',
            'notif_wa_guru_pulang' => 'boolean',
            'report_guru_alpa' => 'boolean',
        ];
    }

    public static function getSetting(): ?self
    {
        return static::first();
    }
}
