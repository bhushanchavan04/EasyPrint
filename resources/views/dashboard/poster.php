<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>A4 Counter Poster - <?= htmlspecialchars($shop['name'] ?? 'EasyPrint') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary-container": "#f97316",
                        "primary": "#9d4300",
                        "primary-fixed": "#ffdbca",
                        "on-primary-fixed": "#341100",
                        "surface": "#f9f9f6",
                        "on-surface": "#1a1c1a",
                        "on-surface-variant": "#584237"
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background: white !important;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .poster-container {
                box-shadow: none !important;
                border: none !important;
                height: 100vh !important;
                width: 100vw !important;
                max-width: 100% !important;
                padding: 14mm !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-zinc-200 min-h-screen py-8 flex flex-col items-center justify-center selection:bg-orange-100">
    <!-- Action Controls (Hidden when printed) -->
    <div class="no-print max-w-xl w-full mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-lg border border-zinc-300">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[24px]">print</span>
            </div>
            <div>
                <h2 class="font-bold text-zinc-900 text-sm">Printable A4 Counter Poster</h2>
                <p class="text-xs text-zinc-500">Paste near your xerox cash counter or entrance glass.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Print A4 Poster</span>
            </button>
            <button onclick="window.close()" class="px-3 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-semibold rounded-xl text-xs transition">
                Close
            </button>
        </div>
    </div>

    <!-- Printable A4 Sheet Frame -->
    <div class="poster-container bg-white w-[210mm] min-h-[297mm] p-12 shadow-2xl rounded-2xl border border-zinc-300 flex flex-col justify-between text-center relative overflow-hidden">
        <!-- Background Ambient Accents -->
        <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-orange-50/70 -z-0"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-amber-50/70 -z-0"></div>

        <!-- Top Header & Brand -->
        <div class="relative z-10 pt-2">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-orange-50 border border-orange-200 text-orange-700 text-xs font-bold tracking-wide uppercase mb-4">
                <span class="material-symbols-outlined text-[16px]">bolt</span>
                <span>Instant Self-Service Cloud Printing</span>
            </div>

            <h1 class="text-4xl sm:text-5xl font-black text-zinc-900 tracking-tight leading-tight">
                Scan to Print in <span class="text-orange-600 underline decoration-orange-400 decoration-4">10 Seconds</span>
            </h1>

            <p class="text-base font-semibold text-zinc-600 mt-3 max-w-md mx-auto">
                No WhatsApp, No Email, No Waiting in Line! Just Scan, Upload &amp; Collect your prints directly from this counter.
            </p>
        </div>

        <!-- Center QR Code Hero Showcase -->
        <div class="relative z-10 my-4 flex flex-col items-center">
            <div class="p-6 bg-white rounded-3xl border-4 border-zinc-900 shadow-2xl max-w-xs mx-auto">
                <img src="<?= $qrCodeUrl ?>" alt="Scan to Print QR Code" class="w-64 h-64 mx-auto rounded-xl object-contain">
                <div class="mt-4 pt-3 border-t-2 border-zinc-100 flex items-center justify-center gap-2 text-zinc-800 font-bold text-xs">
                    <span class="material-symbols-outlined text-orange-600 text-[18px]">photo_camera</span>
                    <span>Open Mobile Camera or Google Lens</span>
                </div>
            </div>

            <!-- Shop Info Callout -->
            <div class="mt-4 text-center">
                <h2 class="text-2xl font-black text-zinc-900"><?= htmlspecialchars($shop['name']) ?></h2>
                <p class="text-xs font-mono font-bold text-orange-600 mt-0.5"><?= htmlspecialchars($customerPrintUrl ?? ('/print/' . ($shop['slug'] ?? ''))) ?></p>
            </div>
        </div>

        <!-- 3-Step Simple Infographic -->
        <div class="relative z-10 grid grid-cols-3 gap-4 border-t-2 border-b-2 border-zinc-100 py-6">
            <div class="text-center">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 mx-auto flex items-center justify-center text-lg font-black mb-2 shadow-sm">
                    1
                </div>
                <h3 class="text-xs font-extrabold text-zinc-900 uppercase">Scan QR</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">With phone camera</p>
            </div>

            <div class="text-center">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 mx-auto flex items-center justify-center text-lg font-black mb-2 shadow-sm">
                    2
                </div>
                <h3 class="text-xs font-extrabold text-zinc-900 uppercase">Upload &amp; Pay</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Direct 0% UPI fee</p>
            </div>

            <div class="text-center">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 mx-auto flex items-center justify-center text-lg font-black mb-2 shadow-sm">
                    3
                </div>
                <h3 class="text-xs font-extrabold text-zinc-900 uppercase">Collect</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Instant auto-spool</p>
            </div>
        </div>

        <!-- Bottom Footer Badges -->
        <div class="relative z-10 pt-2 flex items-center justify-between text-xs text-zinc-500 font-medium">
            <div class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                <span>100% Private: Auto-purged in 24 hours</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span>Powered by</span>
                <span class="font-extrabold text-zinc-900">EasyPrint</span>
            </div>
        </div>
    </div>
</body>
</html>