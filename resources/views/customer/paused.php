<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Service Paused | <?= htmlspecialchars($shop['name'] ?? 'EasyPrint') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 500: '#f97316' },
                        surface: { DEFAULT: '#FAF9F6', card: '#FFFFFF', border: '#E8E6DF' },
                        charcoal: { DEFAULT: '#0F1117', muted: '#52525B' }
                    },
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'] }
                }
            }
        };
    </script>
</head>
<body class="bg-surface text-charcoal font-sans min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-surface-card border border-surface-border rounded-3xl p-8 text-center shadow-lg">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 mx-auto flex items-center justify-center mb-5 shadow-sm">
            <span class="material-symbols-outlined text-[32px]">pause_circle</span>
        </div>
        <h1 class="text-2xl font-black text-charcoal mb-2"><?= htmlspecialchars($shop['name'] ?? 'EasyPrint Kiosk') ?></h1>
        <div class="bg-amber-50/60 border border-amber-200 rounded-2xl p-4 my-4 text-amber-900 text-sm leading-relaxed font-medium">
            "This shop's automated print service is currently paused. Please notify the counter staff."
        </div>
        <p class="text-xs text-charcoal-muted mt-5">
            If you are the shopkeeper, please log in to your <a href="/login" class="text-brand-500 font-bold underline">Merchant Console</a> to renew your active platform license.
        </p>
    </div>
</body>
</html>