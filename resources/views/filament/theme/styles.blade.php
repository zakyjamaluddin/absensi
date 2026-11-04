{{-- Filament Emerald Theme Stylesheet Link --}}
<link
    rel="stylesheet"
    href="{{ asset('css/filament-theme-emerald.css') }}?v={{ file_exists(public_path('css/filament-theme-emerald.css')) ? filemtime(public_path('css/filament-theme-emerald.css')) : time() }}"
    data-navigate-track
/>
