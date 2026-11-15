<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title><?= htmlspecialchars($shop['name'] ?? 'EasyPrint Kiosk') ?> | Instant Self-Service Print</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c'
                        },
                        surface: {
                            DEFAULT: '#FAF9F6',
                            card: '#FFFFFF',
                            border: '#E8E6DF',
                            subtle: '#F4F3EE'
                        },
                        charcoal: {
                            DEFAULT: '#0F1117',
                            muted: '#52525B',
                            soft: '#71717A'
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .step-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="bg-surface text-charcoal font-sans min-h-screen flex flex-col antialiased selection:bg-brand-100 selection:text-brand-700">
    <!-- Top Header -->
    <header class="bg-surface-card border-b border-surface-border sticky top-0 z-30 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <!-- Left: Logo & Terminal Identity -->
            <div class="flex items-center gap-3">
                <?php if (!empty($shop['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($shop['logo_url']) ?>" alt="Logo" class="w-9 h-9 rounded-xl object-cover border border-surface-border">
                <?php else: ?>
                    <div class="w-9 h-9 rounded-xl bg-brand-500 text-white flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-base tracking-tight text-charcoal"><?= htmlspecialchars($shop['name'] ?? 'EasyPrint Kiosk') ?></span>
                    </div>
                    <span class="text-[11px] text-emerald-700 font-medium flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Printer Online &amp; Ready
                    </span>
                </div>
            </div>

            <!-- Right: Trust Badges -->
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold">
                    <span class="material-symbols-outlined text-[14px]">bolt</span>
                    <span>0% Fee</span>
                </div>
                <div class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-subtle border border-surface-border text-charcoal-muted text-xs">
                    <span class="material-symbols-outlined text-[14px] text-emerald-600">verified_user</span>
                    <span>Auto-Purged 24h</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl w-full mx-auto p-4 sm:p-6">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="w-full text-center py-5 border-t border-surface-border text-xs text-charcoal-soft bg-surface-card">
        Powered by <strong class="text-charcoal font-bold">EasyPrint</strong> Automated Self-Service Kiosk
    </footer>
</body>
</html>