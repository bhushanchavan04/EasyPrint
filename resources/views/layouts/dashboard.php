<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($title ?? 'Merchant Console') ?> | EasyPrint</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Plus+Jakarta+Sans:wght@600;700;800&amp;display=swap" rel="stylesheet"/>
    <style>
        @layer base {
            html, body { margin:0; padding:0; }
            body { overscroll-behavior:none; }
            main > :first-child { margin-top:0 !important; }
            main > :last-child { margin-bottom:0 !important; }
        }
        ::-webkit-scrollbar { display:none; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary-container": "#f97316",
                        "on-primary": "#ffffff",
                        "primary": "#9d4300",
                        "primary-fixed": "#ffdbca",
                        "on-primary-fixed": "#341100",
                        "surface": "#f9f9f6",
                        "surface-bright": "#f9f9f6",
                        "surface-dim": "#dadad7",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f4f4f1",
                        "surface-container": "#eeeeeb",
                        "surface-container-high": "#e8e8e5",
                        "surface-container-highest": "#e2e3e0",
                        "on-surface": "#1a1c1a",
                        "on-surface-variant": "#584237",
                        "secondary": "#5d5e65",
                        "secondary-container": "#e2e2eb",
                        "tertiary": "#944a00",
                        "tertiary-container": "#e5812c",
                        "tertiary-fixed": "#ffdcc5",
                        "tertiary-fixed-dim": "#ffb783",
                        "inverse-surface": "#2f312f",
                        "inverse-on-surface": "#f1f1ee",
                        "secondary-fixed-dim": "#c5c6ce"
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    spacing: {
                        "space-xs": "0.25rem",
                        "space-sm": "0.5rem",
                        "space-md": "1rem",
                        "space-lg": "1.5rem",
                        "space-xl": "2.5rem"
                    },
                    fontFamily: {
                        "body-md": ["Inter", "sans-serif"],
                        "body-sm": ["Inter", "sans-serif"],
                        "headline-sm": ["Plus Jakarta Sans", "sans-serif"],
                        "headline-md": ["Plus Jakarta Sans", "sans-serif"],
                        "headline-lg": ["Plus Jakarta Sans", "sans-serif"],
                        "display-xl": ["Plus Jakarta Sans", "sans-serif"],
                        "label-sm": ["Plus Jakarta Sans", "sans-serif"],
                        "label-md": ["Plus Jakarta Sans", "sans-serif"],
                        "label-lg": ["Plus Jakarta Sans", "sans-serif"]
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface min-h-screen">
    <!-- Hidden Chime Audio for New Orders -->
    <audio id="orderAlarmAudio" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <?php 
        $user = \App\Core\Auth::user();
        $curShop = \App\Core\Auth::shop();
        $currentUri = $_SERVER['REQUEST_URI'] ?? '/dashboard';
    ?>

    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-low z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
        <div class="flex flex-col">
            <!-- Brand -->
            <div class="p-space-lg flex items-center gap-space-sm">
                <a href="/dashboard" class="flex items-center gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-primary-container flex items-center justify-center shadow-[inset_0_1px_0_rgba(255,255,255,0.25)]">
                        <span class="material-symbols-outlined text-on-primary text-[24px]">print</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-headline-sm text-headline-sm text-on-surface leading-tight">EasyPrint</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Merchant Console</span>
                    </div>
                </a>
            </div>

            <!-- Active Shop Card -->
            <div class="mx-space-lg mb-space-md p-space-sm bg-surface-container rounded-xl flex flex-col gap-space-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-space-xs truncate">
                        <span class="w-2 h-2 rounded-full bg-primary-container animate-pulse flex-shrink-0"></span>
                        <span class="font-label-md text-label-md text-on-surface font-bold truncate"><?= htmlspecialchars($curShop['name'] ?? 'My Shop') ?></span>
                    </div>
                    <span class="px-space-xs py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-sm text-label-sm flex-shrink-0">0% Fee</span>
                </div>
                <div class="flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                    <span class="truncate">Slug: <span class="font-mono text-primary font-semibold"><?= htmlspecialchars($curShop['slug'] ?? '') ?></span></span>
                    <a href="/print/<?= htmlspecialchars($curShop['slug'] ?? '') ?>" target="_blank" class="text-primary hover:underline flex items-center gap-0.5">
                        <span>Kiosk</span>
                        <span class="material-symbols-outlined text-[12px]">open_in_new</span>
                    </a>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="flex flex-col px-space-md gap-space-xs">
                <!-- Live Orders -->
                <a href="/dashboard" class="flex items-center gap-space-sm px-space-md py-space-sm transition-all rounded-xl <?= ($currentUri === '/dashboard') ? 'bg-primary-fixed text-on-primary-fixed font-bold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' ?>">
                    <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                    <span class="font-label-lg text-label-lg">Live Orders</span>
                </a>

                <!-- Rate Card Matrix -->
                <a href="/dashboard/rates" class="flex items-center gap-space-sm px-space-md py-space-sm transition-all rounded-xl <?= str_starts_with($currentUri, '/dashboard/rates') ? 'bg-primary-fixed text-on-primary-fixed font-bold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' ?>">
                    <span class="material-symbols-outlined text-[20px]">price_change</span>
                    <span class="font-label-lg text-label-lg">Rate Card Matrix</span>
                </a>

                <!-- Machine & Printers -->
                <a href="/dashboard/printers" class="flex items-center gap-space-sm px-space-md py-space-sm transition-all rounded-xl <?= str_starts_with($currentUri, '/dashboard/printers') ? 'bg-primary-fixed text-on-primary-fixed font-bold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' ?>">
                    <span class="material-symbols-outlined text-[20px]">precision_manufacturing</span>
                    <span class="font-label-lg text-label-lg">Machine &amp; Printers</span>
                </a>

                <!-- Shop Profile & UPI -->
                <a href="/dashboard/shop" class="flex items-center gap-space-sm px-space-md py-space-sm transition-all rounded-xl <?= str_starts_with($currentUri, '/dashboard/shop') ? 'bg-primary-fixed text-on-primary-fixed font-bold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' ?>">
                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                    <span class="font-label-lg text-label-lg">Shop Profile &amp; UPI</span>
                </a>

                <!-- Counter Poster (A4) -->
                <a href="/dashboard/poster" target="_blank" class="flex items-center gap-space-sm px-space-md py-space-sm transition-all rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface">
                    <span class="material-symbols-outlined text-[20px]">qr_code_2</span>
                    <span class="font-label-lg text-label-lg">Counter Poster (A4)</span>
                </a>

                <!-- Billing & Paywall -->
                <a href="/dashboard/billing" class="flex items-center gap-space-sm px-space-md py-space-sm transition-all rounded-xl <?= str_starts_with($currentUri, '/dashboard/billing') ? 'bg-primary-fixed text-on-primary-fixed font-bold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' ?>">
                    <span class="material-symbols-outlined text-[20px]">credit_card</span>
                    <span class="font-label-lg text-label-lg">Billing &amp; Paywall</span>
                </a>
            </nav>
        </div>

        <!-- Footer / User / Logout -->
        <div class="p-space-md flex flex-col gap-space-xs bg-surface-container-low border-t border-surface-container">
            <div class="p-space-sm rounded-xl bg-surface-container flex items-center justify-between">
                <div class="flex items-center gap-space-sm min-w-0">
                    <div class="w-8 h-8 rounded-full bg-surface-container-highest flex items-center justify-center flex-shrink-0 font-bold text-xs text-primary">
                        <?= strtoupper(substr($user['name'] ?? 'M', 0, 1)) ?>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-sm text-label-sm text-on-surface font-semibold truncate"><?= htmlspecialchars($user['name'] ?? 'Merchant') ?></span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant text-[11px] truncate"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                    </div>
                </div>
            </div>
            <a href="/logout" class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-error transition-all">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span class="font-label-md text-label-md font-medium">Log Out</span>
            </a>
        </div>
    </aside>

    <!-- Main Workspace Layout with fixed top header and left padding -->
    <div class="pl-72 flex flex-col min-h-screen">
        <!-- Top Fixed Header -->
        <header class="fixed top-0 left-72 right-0 h-16 bg-surface/85 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-space-lg border-b border-surface-container">
            <div class="flex items-center gap-space-md">
                <div class="flex items-center gap-space-xs px-space-sm py-1 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    <span class="material-symbols-outlined text-[14px]">verified</span>
                    <span>REA Node Online</span>
                </div>
                <div class="hidden sm:flex items-center gap-space-xs px-space-sm py-1 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm">
                    <span class="material-symbols-outlined text-[14px] text-primary">shield</span>
                    <span>0% Customer Commission</span>
                </div>
            </div>

            <div class="flex items-center gap-space-md">
                <a href="/dashboard/poster" target="_blank" class="hidden sm:flex items-center gap-space-xs px-space-md py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-all font-label-md text-label-md">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Print Poster</span>
                </a>
                <a href="/print/<?= htmlspecialchars($curShop['slug'] ?? '') ?>" target="_blank" class="flex items-center gap-space-xs px-space-md py-2 rounded-lg bg-primary-container text-on-primary hover:bg-tertiary-container transition-all font-label-md text-label-md shadow-[inset_0_1px_0_rgba(255,255,255,0.25)]">
                    <span class="material-symbols-outlined text-[18px]">devices</span>
                    <span>Open Kiosk</span>
                </a>
                <div class="flex items-center gap-space-sm pl-space-sm border-l border-surface-container">
                    <div class="flex flex-col text-right hidden md:flex">
                        <span class="font-label-md text-label-md text-on-surface leading-snug"><?= htmlspecialchars($user['name'] ?? 'Merchant') ?></span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant"><?= htmlspecialchars($curShop['slug'] ?? '') ?></span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary font-bold text-xs flex items-center justify-center">
                        <?= strtoupper(substr($user['name'] ?? 'M', 0, 1)) ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="mt-16 p-space-lg flex-1 max-w-7xl w-full mx-auto">
            <?= $content ?>
        </main>
    </div>
</body>
</html>