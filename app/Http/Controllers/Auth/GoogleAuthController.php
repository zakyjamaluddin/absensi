<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman login OAuth Google
     */
    public function redirectToGoogle()
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Konfigurasi Google Client ID & Secret belum dilengkapi di file .env server.');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google setelah otentikasi berhasil
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Otentikasi Google dibatalkan atau terjadi kesalahan: ' . $e->getMessage());
        }

        // 1. Cek apakah user sudah terdaftar berdasarkan google_id
        $user = User::where('google_id', $googleUser->getId())->first();
        if ($user) {
            auth()->login($user, true);
            return redirect()->intended('/admin');
        }

        // 2. Cek apakah user sudah terdaftar berdasarkan alamat email yang sama
        $userByEmail = User::where('email', $googleUser->getEmail())->first();
        if ($userByEmail) {
            $userByEmail->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
            auth()->login($userByEmail, true);
            return redirect()->intended('/admin');
        }

        // 3. Jika pengguna baru, simpan data ke session dan arahkan ke verifikasi nomor induk
        session([
            'google_auth_pending' => [
                'google_id' => $googleUser->getId(),
                'email'     => $googleUser->getEmail(),
                'name'      => $googleUser->getName(),
                'avatar'    => $googleUser->getAvatar(),
            ]
        ]);

        return redirect()->route('auth.google.verify');
    }

    /**
     * Tampilkan form interstitial verifikasi Nomor Induk bagi pengguna baru
     */
    public function showVerificationForm()
    {
        $pending = session('google_auth_pending');
        if (!$pending) {
            return redirect()->route('filament.admin.auth.login');
        }

        return view('auth.google-verify', compact('pending'));
    }

    /**
     * Proses validasi Nomor Induk (NIS/NIP) dan buatkan akun User terhubung
     */
    public function processVerification(Request $request)
    {
        $pending = session('google_auth_pending');
        if (!$pending) {
            return redirect()->route('filament.admin.auth.login');
        }

        $request->validate([
            'nomor_induk' => 'required|string|max:50',
        ], [
            'nomor_induk.required' => 'Nomor Induk wajib diisi untuk verifikasi akun internal.',
        ]);

        $nomorInduk = trim($request->input('nomor_induk'));

        // Cek apakah nomor induk adalah NIS Siswa
        $siswa = Siswa::where('nis', $nomorInduk)->first();
        if ($siswa) {
            // Cek apakah siswa ini sudah memiliki akun yang terhubung
            $existingUser = User::where('userable_type', Siswa::class)
                ->where('userable_id', $siswa->id)
                ->first();

            if ($existingUser) {
                // Tautkan akun yang sudah ada dengan Google ID
                $existingUser->update([
                    'google_id' => $pending['google_id'],
                    'avatar'    => $pending['avatar'],
                ]);
                $user = $existingUser;
            } else {
                $user = User::create([
                    'name'              => $pending['name'] ?: $siswa->nama,
                    'email'             => $pending['email'],
                    'google_id'         => $pending['google_id'],
                    'avatar'            => $pending['avatar'],
                    'password'          => Hash::make(Str::random(24)),
                    'userable_type'     => Siswa::class,
                    'userable_id'       => $siswa->id,
                    'email_verified_at' => now(),
                ]);
            }

            $user->syncRoles(['siswa']);
            session()->forget('google_auth_pending');
            auth()->login($user, true);

            return redirect('/admin');
        }

        // Cek apakah nomor induk adalah NIP Guru
        $guru = Guru::where('nip', $nomorInduk)->first();
        if ($guru) {
            $existingUser = User::where('userable_type', Guru::class)
                ->where('userable_id', $guru->id)
                ->first();

            if ($existingUser) {
                $existingUser->update([
                    'google_id' => $pending['google_id'],
                    'avatar'    => $pending['avatar'],
                ]);
                $user = $existingUser;
            } else {
                $user = User::create([
                    'name'              => $pending['name'] ?: $guru->nama,
                    'email'             => $pending['email'],
                    'google_id'         => $pending['google_id'],
                    'avatar'            => $pending['avatar'],
                    'password'          => Hash::make(Str::random(24)),
                    'userable_type'     => Guru::class,
                    'userable_id'       => $guru->id,
                    'email_verified_at' => now(),
                ]);
            }

            // Tentukan apakah guru ini wali kelas
            if ($guru->kelas()->exists()) {
                $user->syncRoles(['wali_kelas', 'guru']);
            } else {
                $user->syncRoles(['guru']);
            }

            session()->forget('google_auth_pending');
            auth()->login($user, true);

            return redirect('/admin');
        }

        // Jika nomor induk tidak ditemukan di tabel siswa maupun guru
        return back()->withErrors([
            'nomor_induk' => 'Nomor Induk (' . e($nomorInduk) . ') tidak terdaftar di pangkalan data Siswa atau Guru.',
        ])->withInput();
    }
}
