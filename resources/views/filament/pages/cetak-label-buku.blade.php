<div style="padding: 1rem; display: flex; flex-direction: column; align-items: center; gap: 1.5rem;">
    <!-- KARTU LABEL BUKU (SIAP CETAK) -->
    <div id="print-area-{{ $buku->id }}" style="
        width: 320px;
        padding: 1.25rem;
        background: #ffffff;
        border: 2px dashed #10b981;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        text-align: center;
        font-family: ui-sans-serif, system-ui, sans-serif;
        color: #1f2937;
    ">
        <!-- HEADER KOP PERPUSTAKAAN -->
        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #047857; margin-bottom: 0.25rem;">
            Perpustakaan Digital
        </div>
        <div style="font-size: 0.875rem; font-weight: 800; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 0.75rem;">
            LABEL KOLEKSI BUKU
        </div>

        <!-- QR CODE GENERATOR -->
        <div style="display: flex; justify-content: center; margin-bottom: 0.75rem;">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(1)->generate($buku->kode_buku) !!}
        </div>

        <!-- KODE BUKU -->
        <div style="
            display: inline-block;
            background: #f3f4f6;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-family: monospace;
            font-size: 1rem;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        ">
            {{ $buku->kode_buku }}
        </div>

        <!-- INFO DETAIL BUKU -->
        <div style="font-size: 0.925rem; font-weight: 700; color: #111827; line-height: 1.3; margin-bottom: 0.25rem;">
            {{ $buku->judul }}
        </div>
        @if($buku->penulis)
            <div style="font-size: 0.75rem; color: #4b5563; margin-bottom: 0.25rem;">
                Penulis: {{ $buku->penulis }}
            </div>
        @endif

        <div style="display: flex; justify-content: center; gap: 0.5rem; font-size: 0.75rem; margin-top: 0.5rem;">
            @if($buku->kategori)
                <span style="background: #e0e7ff; color: #3730a3; padding: 0.15rem 0.5rem; border-radius: 0.25rem; font-weight: 600;">
                    {{ $buku->kategori }}
                </span>
            @endif
            @if($buku->rak)
                <span style="background: #fef3c7; color: #92400e; padding: 0.15rem 0.5rem; border-radius: 0.25rem; font-weight: 600;">
                    Rak: {{ $buku->rak }}
                </span>
            @endif
        </div>
    </div>

    <!-- TOMBOL AKSI CETAK -->
    <div style="display: flex; gap: 0.75rem; justify-content: center;">
        <button
            type="button"
            onclick="cetakLabel('print-area-{{ $buku->id }}')"
            style="
                display: flex;
                align-items: center;
                gap: 0.5rem;
                background-color: #10b981;
                color: white;
                font-weight: 600;
                font-size: 0.875rem;
                padding: 0.625rem 1.25rem;
                border-radius: 0.5rem;
                border: none;
                cursor: pointer;
                transition: background-color 0.2s;
            "
        >
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Label Sekarang
        </button>
    </div>

    <script>
        function cetakLabel(elementId) {
            const printContent = document.getElementById(elementId).outerHTML;
            const originalContents = document.body.innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Cetak Label Buku</title>');
            printWindow.document.write('<style>body { display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; font-family: sans-serif; }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(function() {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
    </script>
</div>
