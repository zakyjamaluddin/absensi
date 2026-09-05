<?php

namespace App\Services;

use App\Models\WaSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Memformat nomor telepon Indonesia agar sesuai instruksi Sidobe (+628xxx)
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone); // Buang karakter selain angka

        if (str_starts_with($phone, '08')) {
            return '+628' . substr($phone, 2);
        }

        if (str_starts_with($phone, '8')) {
            return '+628' . substr($phone, 1);
        }

        if (str_starts_with($phone, '628')) {
            return '+' . $phone;
        }

        return '+' . $phone; // Fallback jika sudah benar
    }

    /**
     * Mengirim pesan teks menggunakan API Resmi Sidobe (JSON Format)
     */
    public static function send(string $phone, string $message): array
    {
        $setting = WaSetting::first();
        if (!$setting || empty($setting->token)) {
            return [
                'success' => false,
                'message' => 'Gagal: Token API Sidobe belum diisi di database.'
            ];
        }

        $formattedPhone = self::formatPhone($phone);

        try {
            // KIRIM REQUEST SESUAI DOKUMENTASI RESMI SIDOBE
            $response = Http::withHeaders([
                'X-Secret-Key' => $setting->token,
                'Content-Type' => 'application/json',
            ])->post('https://api.sidobe.com/wa/v1/send-message', [
                'phone'   => $formattedPhone,
                'message' => $message,
            ]);

            // Jika Respon Sukses (Status Code 200 - 299)
            if ($response->successful()) {
                Log::info("WhatsApp sukses dikirim ke {$formattedPhone}");
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil terkirim.'
                ];
            }

            // Jika Gagal, tangkap pesan error asli dari server Sidobe
            $errorBody = $response->body();
            $jsonDecoded = $response->json();
            $errorMessage = $jsonDecoded['message'] ?? $jsonDecoded['error'] ?? $errorBody;

            Log::error("API Sidobe Error: " . $errorBody);

            return [
                'success' => false,
                'message' => 'Respon Sidobe: ' . substr($errorMessage, 0, 150)
            ];

        } catch (\Exception $e) {
            Log::error("Koneksi Sidobe Gagal: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Koneksi Gagal: ' . $e->getMessage()
            ];
        }
    }
}
