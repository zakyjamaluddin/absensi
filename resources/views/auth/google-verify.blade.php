<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Nomor Induk - Absaka Digital</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.15) 0%, rgba(248, 250, 252, 1) 70%);
            padding: 1.5rem;
            color: #0f172a;
        }
        .card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 2.25rem 2rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            text-align: center;
        }
        .logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            margin: 0 auto 1.25rem;
        }
        h1 {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.35rem;
        }
        p.subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .user-box {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 9999px;
            object-fit: cover;
            border: 2px solid #10b981;
        }
        .user-info .name {
            font-size: 0.875rem;
            font-weight: 700;
            color: #1e293b;
        }
        .user-info .email {
            font-size: 0.75rem;
            color: #64748b;
        }
        .form-group {
            text-align: left;
            margin-bottom: 1.25rem;
        }
        label {
            display: block;
            font-size: 0.825rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.45rem;
        }
        input[type="text"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.625rem;
            border: 1.5px solid #cbd5e1;
            font-size: 0.95rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
        }
        input[type="text"]:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }
        .error-message {
            font-size: 0.75rem;
            color: #e11d48;
            margin-top: 0.35rem;
            font-weight: 600;
        }
        .helper-text {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.35rem;
            line-height: 1.4;
        }
        .btn-submit {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 0.625rem;
            border: none;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            font-size: 0.925rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transition: all 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }
        .btn-cancel {
            display: inline-block;
            margin-top: 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
        }
        .btn-cancel:hover {
            color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ asset('logo.png') }}" alt="Logo Absaka" class="logo">
        <h1>Verifikasi Akun Internal</h1>
        <p class="subtitle">Karena aplikasi ini adalah sistem internal, mohon hubungkan akun Google Anda dengan Nomor Induk resmi yang terdaftar.</p>

        <div class="user-box">
            <img src="{{ $pending['avatar'] ?: asset('images/default-avatar.svg') }}" alt="Avatar" class="user-avatar" onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
            <div class="user-info">
                <div class="name">{{ $pending['name'] ?: 'Pengguna Google' }}</div>
                <div class="email">{{ $pending['email'] }}</div>
            </div>
        </div>

        <form action="{{ route('auth.google.verify.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nomor_induk">Nomor Induk / Nomor Kartu (NIS / NIP)</label>
                <input
                    type="text"
                    id="nomor_induk"
                    name="nomor_induk"
                    value="{{ old('nomor_induk') }}"
                    placeholder="Contoh: 20260001 (Siswa) atau 19810101 (Guru)"
                    autofocus
                    required
                >
                @error('nomor_induk')
                    <div class="error-message">⚠️ {{ $message }}</div>
                @enderror
                <div class="helper-text">
                    Sistem akan memvalidasi apakah Anda Siswa atau Guru dan otomatis memberikan hak akses peran yang sesuai.
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Verifikasi & Masuk ke Sistem
            </button>
        </form>

        <a href="{{ route('filament.admin.auth.login') }}" class="btn-cancel">
            ← Kembali ke Halaman Login
        </a>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9; font-size: 0.725rem; color: #94a3b8;">
            <a href="{{ route('legal.privacy') }}" target="_blank" style="color: #64748b; text-decoration: underline;">Kebijakan Privasi</a>
            •
            <a href="{{ route('legal.terms') }}" target="_blank" style="color: #64748b; text-decoration: underline;">Ketentuan Layanan</a>
        </div>
    </div>
</body>
</html>
