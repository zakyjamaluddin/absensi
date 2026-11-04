<div style="margin-top: 1.25rem;">
    <!-- Divider atau masuk dengan -->
    <div style="
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    ">
        <div style="flex: 1; height: 1px; background: #e2e8f0;" class="dark:!bg-slate-700"></div>
        <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;" class="dark:!text-slate-400">
            atau masuk dengan
        </span>
        <div style="flex: 1; height: 1px; background: #e2e8f0;" class="dark:!bg-slate-700"></div>
    </div>

    <!-- Tombol Masuk dengan Google -->
    <a
        href="{{ route('auth.google') }}"
        data-navigate-ignore
        style="
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.7rem 1rem;
            border-radius: 0.625rem;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            color: #1e293b;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
        "
        class="dark:!bg-slate-800 dark:!border-slate-700 dark:!text-white hover:border-slate-400 dark:hover:!border-slate-600 hover:shadow-md"
        onmouseover="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f8fafc';"
        onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#ffffff';"
    >
        <!-- Google Multi-color Icon SVG -->
        <svg style="width: 1.15rem; height: 1.15rem;" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
        </svg>
        <span>Lanjutkan dengan Akun Google</span>
    </a>

    <!-- Tautan Legalitas: Kebijakan Privasi & Ketentuan Layanan -->
    <div style="margin-top: 1.15rem; text-align: center; font-size: 0.725rem; color: #64748b; line-height: 1.4;" class="dark:!text-slate-400">
        Dengan masuk, Anda menyetujui
        <a href="{{ route('legal.terms') }}" target="_blank" style="color: #10b981; font-weight: 600; text-decoration: underline;">Ketentuan Layanan</a>
        dan
        <a href="{{ route('legal.privacy') }}" target="_blank" style="color: #10b981; font-weight: 600; text-decoration: underline;">Kebijakan Privasi</a>
        Absaka.
    </div>
</div>
