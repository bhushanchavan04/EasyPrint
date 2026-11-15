# EasyPrint SaaS — Complete UI/UX Redesign Specification & Code Blueprint

> **Notice for Stitch / Frontend Redesign Agent:**  
> This file contains the complete UI architecture, design tokens, component specifications, user flows, and actual UI code for the entire **EasyPrint SaaS** application. Use this document to redesign, modernize, and generate updated frontend code, mockups, or component libraries.

---

## 📑 Table of Contents
1. [Product Overview & Design Goals](#1-product-overview--design-goals)
2. [Design System & Global Tokens](#2-design-system--global-tokens)
3. [Global Layout Shells & Master Templates](#3-global-layout-shells--master-templates)
   - Layout 1: Public App Shell (`layouts/app.php`)
   - Layout 2: Customer Mobile Kiosk Shell (`layouts/customer.php`)
   - Layout 3: Merchant Dashboard Shell (`layouts/dashboard.php`)
4. [Public & Marketing Screens](#4-public--marketing-screens)
   - Screen 1: SaaS Marketing Landing Page (`home/index.php`)
   - Screen 2: Merchant Sign In (`auth/login.php`)
   - Screen 3: Merchant Shop Registration (`auth/register.php`)
5. [Customer Self-Service Kiosk (Mobile-First Web App)](#5-customer-self-service-kiosk-mobile-first-web-app)
   - Screen 4: 4-Step Customer Print Kiosk (`customer/portal.php`)
   - Screen 5: Kiosk Paused / Paywall Screen (`customer/paused.php`)
6. [Merchant Console & Management Dashboard](#6-merchant-console--management-dashboard)
   - Screen 6: Live Orders Stream & Metrics (`dashboard/index.php`)
   - Screen 7: Dynamic Rate Card Matrix (`dashboard/rates.php`)
   - Screen 8: Machine Agents & Hardware Printers (`dashboard/printers.php`)
   - Screen 9: Shop Profile & Direct UPI Settings (`dashboard/shop.php`)
   - Screen 10: Billing & Subscription Paywall (`dashboard/billing.php`)
   - Screen 11: Printable A4 Shop Counter Poster (`dashboard/poster.php`)
7. [Stitch Redesign Instructions & Prompts](#7-stitch-redesign-instructions--prompts)

---

## 1. Product Overview & Design Goals

### 🎯 What is EasyPrint SaaS?
EasyPrint is a print counter automation SaaS for Xerox shops, photocopy centers, and cyber cafes.
- **End Customers**: Scan a counter QR code, upload PDF/Word/images, choose copies & print options, pay directly to the shopkeeper's personal UPI (Google Pay, PhonePe, Paytm), and watch documents print automatically.
- **Merchants (Shopkeepers)**: Connect their existing Windows PC via `EasyPrint.exe` (silent background spooler), set custom paper rates, track incoming orders with real-time audio alarms, and retain **100% of customer printing revenue with 0% platform commission**.
- **Platform Owner (You)**: Monetizes via flat monthly/annual SaaS subscription plans (₹99/mo Basic, ₹499/mo Pro, ₹3,999/yr Enterprise) enforced by a hard paywall.

### 🎨 Key Redesign Objectives for Stitch:
1. **World-Class SaaS Aesthetic**: Elevate the design to modern enterprise standards (inspired by Stripe, Linear, Raycast, and Vercel).
2. **Hyper-Intuitive Customer Kiosk**: Mobile-first, zero-login 4-step wizard that any walk-in customer (regardless of tech literacy) can navigate in under 15 seconds.
3. **High-Efficiency Merchant Console**: Glanceable live dashboard with high-contrast order status cards, real-time audio chimes, tactile hardware toggles, and clear metric cards.
4. **Print-Ready Counter Posters**: Crisp, beautifully formatted vector-grade A4 posters with prominent QR codes and payment brand trust badges.

---

## 2. Design System & Global Tokens

### 🎨 Color Palette Tokens
| Token | Hex Code | Description & Usage |
|---|---|---|
| `bg-primary` | `#030712` (Slate-950) | Deep canvas obsidian background |
| `bg-surface` | `#0f172a` (Slate-900) | Card, modal, and drawer backgrounds |
| `bg-elevated` | `#1e293b` (Slate-800) | Secondary panels, inputs, hover states |
| `border-subtle`| `rgba(51, 65, 85, 0.6)` | Card and section borders |
| `brand-indigo`| `#6366f1` / `#4f46e5` | Primary action buttons, brand accents, active tabs |
| `accent-cyan` | `#06b6d4` / `#22d3ee` | Interactive badges, spooler indicators, secondary glow |
| `status-emerald`| `#10b981` / `#059669`| 0% fee badges, paid order status, online spooler indicator |
| `status-amber`  | `#f59e0b` / `#d97706`| Awaiting payment, warning badges, pending queue |
| `status-rose`   | `#ef4444` / `#dc2626`| Hard paywall banner, errors, delete/cancel actions |
| `text-primary`  | `#f8fafc` (Slate-50)  | Headings, primary titles, emphasized labels |
| `text-secondary`| `#94a3b8` (Slate-400) | Body text, captions, secondary descriptions |
| `text-muted`    | `#64748b` (Slate-500) | Timestamps, helper hints, disabled states |

### 🔤 Typography Tokens
- **Primary Sans Font**: `'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif`
- **Monospace Code/PIN Font**: `'JetBrains Mono', 'Fira Code', monospace`
- **Scale**:
  - Display: `48px` to `72px` (`font-extrabold`)
  - H1 / Section Header: `32px` to `40px` (`font-extrabold`)
  - H2 / Card Header: `20px` to `24px` (`font-bold`)
  - Body Regular: `14px` (`font-medium`)
  - Small / Badges: `11px` to `12px` (`font-semibold`)
  - Micro / Metas: `9px` to `10px` (`font-bold`, uppercase tracking)

### 🧱 Component Standards
- **Buttons**: `rounded-xl`, `font-bold`, transition duration 150ms, tactile active press state (`active:scale-[0.98]`).
- **Cards**: Glassmorphic (`backdrop-blur-xl bg-slate-900/80 border border-slate-800`).
- **Inputs**: `rounded-xl bg-slate-800/80 border border-slate-700 focus:ring-2 focus:ring-indigo-500 text-white placeholder-slate-500`.
- **Badges / Status Pills**: Rounded full (`px-2.5 py-0.5 rounded-full text-xs font-bold border`).

---

## 3. Global Layout Shells & Master Templates

### Layout 1: Public Base Layout (`resources/views/layouts/app.php`)
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'EasyPrint - Print Automation SaaS') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen text-slate-100 antialiased selection:bg-indigo-500 selection:text-white">
    <?= $content ?>
</body>
</html>
```

---

### Layout 2: Customer Mobile Kiosk Shell (`resources/views/layouts/customer.php`)
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($shop['name'] ?? 'EasyPrint Kiosk') ?> | Instant Self-Service Print</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        .step-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white">
    <!-- Top Header -->
    <header class="w-full bg-slate-800/80 backdrop-blur-md border-b border-slate-700/60 sticky top-0 z-50">
        <div class="max-w-md mx-auto px-4 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <?php if (!empty($shop['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($shop['logo_url']) ?>" alt="Logo" class="w-9 h-9 rounded-full object-cover border border-indigo-500/40">
                <?php else: ?>
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-bold shadow-md shadow-indigo-500/20">
                        <i class="fa-solid fa-print text-sm"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="text-base font-bold text-white tracking-tight leading-none"><?= htmlspecialchars($shop['name']) ?></h1>
                    <span class="text-xs text-emerald-400 flex items-center mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse mr-1.5"></span>
                        Automated Cloud Printer Online
                    </span>
                </div>
            </div>
            <div class="bg-slate-700/60 px-2.5 py-1 rounded-full text-xs font-medium text-slate-300 border border-slate-600/40 flex items-center">
                <i class="fa-solid fa-bolt text-amber-400 text-[10px] mr-1.5"></i>
                0% Fee
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-md w-full mx-auto p-4">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="w-full text-center py-4 border-t border-slate-800/80 text-xs text-slate-500">
        Powered by <span class="font-semibold text-slate-400">EasyPrint</span> Automated Kiosk
    </footer>
</body>
</html>
```

---

### Layout 3: Merchant Dashboard Shell (`resources/views/layouts/dashboard.php`)
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Merchant Console') ?> | EasyPrint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex antialiased">
    <!-- Hidden Chime for New Orders -->
    <audio id="orderAlarmAudio" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-950 border-r border-slate-800 flex flex-col justify-between hidden md:flex">
        <div>
            <!-- Brand -->
            <div class="h-16 px-6 flex items-center border-b border-slate-800/80">
                <a href="/dashboard" class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-cyan-400 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                        <i class="fa-solid fa-print text-sm"></i>
                    </div>
                    <span class="text-lg font-extrabold tracking-tight text-white">Easy<span class="text-indigo-400">Print</span></span>
                </a>
            </div>

            <!-- Shop Info Card -->
            <?php $curShop = \App\Core\Auth::shop(); ?>
            <div class="p-4 mx-3 my-3 bg-slate-900/80 rounded-xl border border-slate-800">
                <div class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Active Shop</div>
                <div class="font-bold text-slate-200 truncate mt-0.5"><?= htmlspecialchars($curShop['name'] ?? 'My Shop') ?></div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-800 text-[11px]">
                    <span class="text-slate-400">Slug: <span class="text-indigo-400 font-mono"><?= htmlspecialchars($curShop['slug'] ?? '') ?></span></span>
                    <a href="/print/<?= htmlspecialchars($curShop['slug'] ?? '') ?>" target="_blank" class="text-cyan-400 hover:underline flex items-center" title="Open Customer Kiosk">
                        Kiosk <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-[9px]"></i>
                    </a>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="px-3 space-y-1.5 text-sm">
                <?php $currentUri = $_SERVER['REQUEST_URI'] ?? '/dashboard'; ?>
                <a href="/dashboard" class="flex items-center px-3.5 py-2.5 rounded-lg font-medium transition <?= $currentUri === '/dashboard' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' ?>">
                    <i class="fa-solid fa-layer-group w-5 mr-2.5 text-center"></i> Live Orders
                </a>
                <a href="/dashboard/rates" class="flex items-center px-3.5 py-2.5 rounded-lg font-medium transition <?= str_starts_with($currentUri, '/dashboard/rates') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' ?>">
                    <i class="fa-solid fa-tags w-5 mr-2.5 text-center"></i> Rate Card Matrix
                </a>
                <a href="/dashboard/printers" class="flex items-center px-3.5 py-2.5 rounded-lg font-medium transition <?= str_starts_with($currentUri, '/dashboard/printers') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' ?>">
                    <i class="fa-solid fa-desktop w-5 mr-2.5 text-center"></i> Machine & Printers
                </a>
                <a href="/dashboard/shop" class="flex items-center px-3.5 py-2.5 rounded-lg font-medium transition <?= str_starts_with($currentUri, '/dashboard/shop') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' ?>">
                    <i class="fa-solid fa-store w-5 mr-2.5 text-center"></i> Shop Profile & UPI
                </a>
                <a href="/dashboard/poster" target="_blank" class="flex items-center px-3.5 py-2.5 rounded-lg font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-900 transition">
                    <i class="fa-solid fa-qrcode w-5 mr-2.5 text-center"></i> Counter Poster (A4)
                </a>
                <a href="/dashboard/billing" class="flex items-center px-3.5 py-2.5 rounded-lg font-medium transition <?= str_starts_with($currentUri, '/dashboard/billing') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' ?>">
                    <i class="fa-solid fa-credit-card w-5 mr-2.5 text-center"></i> Billing & Paywall
                </a>
            </nav>
        </div>

        <!-- User / Logout -->
        <div class="p-4 border-t border-slate-800/80">
            <?php $user = \App\Core\Auth::user(); ?>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-300">
                        <?= strtoupper(substr($user['name'] ?? 'M', 0, 1)) ?>
                    </div>
                    <div class="truncate text-xs">
                        <div class="font-medium text-slate-200 truncate"><?= htmlspecialchars($user['name'] ?? 'Merchant') ?></div>
                        <div class="text-slate-500 truncate"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                    </div>
                </div>
                <a href="/logout" class="text-slate-400 hover:text-red-400 text-sm p-1.5 rounded transition" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Workspace -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Topbar Mobile -->
        <header class="h-16 bg-slate-950 border-b border-slate-800 px-4 md:px-8 flex items-center justify-between sticky top-0 z-40">
            <div class="flex items-center space-x-3">
                <div class="md:hidden">
                    <a href="/dashboard" class="text-indigo-400 font-bold text-lg">EasyPrint</a>
                </div>
                <div class="hidden sm:flex items-center space-x-2 text-xs">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-emerald-950 text-emerald-400 border border-emerald-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse mr-1.5"></span>
                        REA Node Online
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-indigo-950 text-indigo-300 border border-indigo-800/60">
                        <i class="fa-solid fa-shield-halved text-[10px] mr-1"></i>
                        0% Commission
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="/print/<?= htmlspecialchars($curShop['slug'] ?? '') ?>" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg border border-slate-700 transition flex items-center">
                    <i class="fa-solid fa-qrcode mr-1.5"></i> Open Kiosk
                </a>
                <a href="/dashboard/poster" target="_blank" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center">
                    <i class="fa-solid fa-print mr-1.5"></i> Print Poster
                </a>
            </div>
        </header>

        <!-- Dashboard View Content -->
        <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto">
            <?= $content ?>
        </main>
    </div>
</body>
</html>
```

---

## 4. Public & Marketing Screens

### Screen 1: SaaS Marketing Landing Page (`resources/views/home/index.php`)
- **Route**: `GET /`
- **Controller**: `HomeController::index()`
- **Components Included**:
  1. Sticky Glassmorphic Navbar with Auth status toggle (Sign In vs Go to Dashboard).
  2. Hero Banner with animated value pill badge (`0% Platform Commission • Direct UPI`).
  3. High-Converting Dual-Pane Product Mockup: Left Phone Kiosk Preview + Right Windows Live Spooler Feed.
  4. Social Proof Metrics Strip (2,500+ Shops, 0% Cut, <3s Spooling).
  5. The WhatsApp Nightmare vs The EasyPrint Autopilot Way (Side-by-side pain vs solution card).
  6. 4-Step How It Works Section (Scan QR &rarr; Upload & Configure &rarr; Direct UPI &rarr; Silent Auto-Print).
  7. Bento Grid Feature Tiles (0% Commission, Windows Spooler, Dynamic Rates, Real-time Audio Chimes, GDPR Shredder, A4 Poster).
  8. Windows Hardware Compatibility Showcase (HP, Canon, Epson, Brother, Ricoh) + Terminal Output + Direct `EasyPrint.exe` Download.
  9. Interactive Profit & Time Saved ROI Calculator (Sliders for daily customers, pages, rates).
  10. Transparent Pricing Grid with Monthly / Annual (Save 20%) Switcher.
  11. Interactive FAQ Accordion.
  12. Final High-Impact CTA Banner & 5-Column Footer.

---

### Screen 2: Merchant Sign In (`resources/views/auth/login.php`)
- **Route**: `GET /login`, `POST /login`
- **Controller**: `AuthController::showLogin()`, `AuthController::login()`
- **Form Fields**: `email`, `password`, `_csrf`
- **UI Structure & Code**:
```html
<div class="min-h-screen flex items-center justify-center p-4 bg-slate-950 text-slate-100">
    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl">
        <div class="text-center mb-8">
            <div class="inline-flex w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-500 to-cyan-400 items-center justify-center text-white mb-3 shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-print text-xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">Merchant Portal</h1>
            <p class="text-sm text-slate-400 mt-1">Sign in to control your EasyPrint automation kiosk</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-5 p-3.5 bg-red-950/80 border border-red-800/80 rounded-xl text-red-200 text-sm flex items-center">
                <i class="fa-solid fa-circle-exclamation mr-2 text-red-400"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" class="space-y-4">
            <?= $csrfField ?>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required placeholder="owner@cybercafe.com"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition duration-150 shadow-lg shadow-indigo-600/30 text-sm flex items-center justify-center">
                <span>Sign In to Dashboard</span>
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800/80 text-center text-xs text-slate-400">
            Don't have a shop account yet? 
            <a href="/register" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">Register Shop</a>
        </div>
    </div>
</div>
```

---

### Screen 3: Merchant Shop Registration (`resources/views/auth/register.php`)
- **Route**: `GET /register`, `POST /register`
- **Controller**: `AuthController::showRegister()`, `AuthController::register()`
- **Form Fields**: `name`, `phone`, `email`, `password`, `shop_name`, `upi_id`, `upi_name`, `_csrf`
- **UI Structure & Code**:
```html
<div class="min-h-screen flex items-center justify-center p-4 bg-slate-950 text-slate-100">
    <div class="max-w-lg w-full bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl my-6">
        <div class="text-center mb-6">
            <div class="inline-flex w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-500 to-cyan-400 items-center justify-center text-white mb-3 shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-store text-xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">Register Shop</h1>
            <p class="text-sm text-slate-400 mt-1">Set up automated self-service printing in 2 minutes</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-5 p-3.5 bg-red-950/80 border border-red-800/80 rounded-xl text-red-200 text-sm flex items-center">
                <i class="fa-solid fa-circle-exclamation mr-2 text-red-400"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST" class="space-y-4">
            <?= $csrfField ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Owner Full Name</label>
                    <input type="text" name="name" required placeholder="Ramesh Sharma"
                           class="w-full px-3.5 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Phone (WhatsApp)</label>
                    <input type="text" name="phone" placeholder="+91 98765 43210"
                           class="w-full px-3.5 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email Address (Login ID)</label>
                <input type="email" name="email" required placeholder="ramesh@cybercafe.com"
                       class="w-full px-3.5 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-3.5 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>

            <div class="border-t border-slate-800 pt-4 mt-2">
                <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">Shop & Direct UPI Details (0% Commission)</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Shop Business Name</label>
                        <input type="text" name="shop_name" required placeholder="Pixaprint Xerox & Cyber Cafe"
                               class="w-full px-3.5 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Personal UPI ID (VPA)</label>
                            <input type="text" name="upi_id" required placeholder="ramesh@okhdfcbank"
                                   class="w-full px-3.5 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono">
                            <span class="text-[10px] text-slate-500 mt-1 block">Your customer print payments go directly here.</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">UPI Account Holder Name</label>
                            <input type="text" name="upi_name" placeholder="Ramesh Xerox"
                                   class="w-full px-3.5 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3 mt-4 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition duration-150 shadow-lg shadow-indigo-600/30 text-sm flex items-center justify-center">
                <span>Create Shop & Open Paywall</span>
                <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800/80 text-center text-xs text-slate-400">
            Already have an account? 
            <a href="/login" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">Sign In</a>
        </div>
    </div>
</div>
```

---

## 5. Customer Self-Service Kiosk (Mobile-First Web App)

### Screen 4: 4-Step Customer Print Kiosk (`resources/views/customer/portal.php`)
- **Route**: `GET /print/{shopSlug}`
- **Controller**: `CustomerPortalController::show()`
- **Layout**: `layouts/customer.php`
- **Client Script**: `public/js/customer-portal.js`
- **API Endpoints Used**:
  - `POST /api/print/{shopSlug}/upload` (File dropzone & auto page counter)
  - `POST /api/print/{shopSlug}/order` (Creates REA order)
  - `POST /api/orders/{orderNumber}/pay` (Submits UPI transaction UTR)
  - `GET /api/orders/{orderNumber}/status` (Polls print spooling status)

#### UI Architecture of the 4 Steps:
1. **Step Indicator Progress Bar**: Visual stepper with numbered pills (1: Upload, 2: Settings, 3: Pay, 4: Print).
2. **Step 1 (Upload)**:
   - Drag & drop dashed upload area for PDF, DOCX, PNG, JPG.
   - Privacy guarantee badge: *"Files automatically purged after 24 hours"*.
   - Animated loading spinner with page-count parser.
3. **Step 2 (Settings & Live Preview)**:
   - Live document preview container (Embedded PDF iframe / image preview).
   - Document info pill: File name, file size, detected page count.
   - Copies selector counter (`-` / `+`).
   - Color Mode Toggle: **Black & White** vs. **Full Color** (shows pricing differences).
   - Paper Sides Toggle: **Single Sided** vs. **Back-to-Back (Double)**.
   - Paper Size Selector: **A4**, **A3**, **Legal**.
   - Page Range Selector: **All Pages** or custom comma/dash ranges (e.g., `1-5, 8`).
   - Dynamic Price Summary footer: Shows Total Pages & Calculated Total (₹).
4. **Step 3 (Payment)**:
   - Dynamic UPI QR Code generated via `upi://pay?pa=...&pn=...&am=...`.
   - Native UPI Intent Deep-link buttons for mobile customers: **Google Pay**, **PhonePe**, **Paytm**, **BHIM**.
   - Optional Cash at Counter button (if shop enables `accept_cash`).
   - 12-digit UPI Reference / UTR input field + "Confirm Payment" button.
5. **Step 4 (Print Status & Spooling)**:
   - Real-time animated progress ring / spinner.
   - Status indicators: `AWAITING PAYMENT` &rarr; `PAYMENT VERIFIED` &rarr; `SPOOLING TO PRINTER` &rarr; `COMPLETED`.
   - Audio chime triggers on completion + haptic vibration (`navigator.vibrate([100, 50, 100])`).

---

### Screen 5: Kiosk Paused / Paywall Screen (`resources/views/customer/paused.php`)
- **Route Trigger**: Rendered when the shopkeeper's subscription has expired.
- **UI Structure & Code**:
```html
<div class="min-h-[70vh] flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-800/90 border border-slate-700/80 rounded-2xl p-8 text-center shadow-2xl">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 mx-auto flex items-center justify-center mb-4">
            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
        </div>
        <h2 class="text-xl font-bold text-white mb-2">Service Temporarily Paused</h2>
        <p class="text-sm text-slate-300 leading-relaxed mb-6">
            This shop's automated print service is currently paused for account maintenance. Please notify the counter staff.
        </p>
        <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-800 text-xs text-slate-400">
            Shop: <span class="font-semibold text-slate-200"><?= htmlspecialchars($shop['name'] ?? 'EasyPrint') ?></span>
        </div>
    </div>
</div>
```

---

## 6. Merchant Console & Management Dashboard

### Screen 6: Live Orders Stream & Metrics (`resources/views/dashboard/index.php`)
- **Route**: `GET /dashboard`
- **Controller**: `DashboardController::index()`
- **Layout**: `layouts/dashboard.php`
- **Components Included**:
  1. **Top Metric Stats Cards (4 Cards)**:
     - Today's Print Pages (Total physical sheets spooled).
     - Today's Revenue (₹ Total collected directly to UPI, 0% platform cut retained).
     - Active Print Queue (Orders awaiting payment or currently spooling).
     - Hardware Spooler Status (Active default printer name, green pulsing dot).
  2. **Stream Control Bar**:
     - Auto-refresh indicator (3-second poll).
     - Sound Alarm Mute / Unmute Toggle (`#toggleAudioBtn`).
  3. **Live Orders Feed**:
     - Order Cards with Order Number (`MP-YYYYMMDD-XXXX`), Document Name, Copies, B&W/Color, UTR Reference, Total Amount (₹), and Timestamp.
     - Status badges: `AWAITING PAYMENT` (Amber), `PRINTING` (Indigo pulse), `COMPLETED` (Emerald).
     - Quick Action Button: **"Mark Done"** button for completed manual handoffs.

---

### Screen 7: Dynamic Rate Card Matrix (`resources/views/dashboard/rates.php`)
- **Route**: `GET /dashboard/rates`, `POST /dashboard/rates`
- **Controller**: `DashboardController::rates()`, `DashboardController::updateRates()`
- **Components Included**:
  1. **A4 Section**: Single B&W, Double B&W, Single Color, Double Color.
  2. **A3 Section**: Single B&W, Double B&W, Single Color, Double Color.
  3. **Legal Section**: Single B&W, Double B&W.
  4. Currency prepended inputs (`₹`) with 0.5 step adjustments.
  5. Live simulation preview showing customer price calculation for 10 double-sided pages.

---

### Screen 8: Machine Agents & Hardware Printers (`resources/views/dashboard/printers.php`)
- **Route**: `GET /dashboard/printers`, `POST /dashboard/printers/generate-pin`, `POST /dashboard/printers/set-default`
- **Controller**: `DashboardController::printers()`, `DashboardController::generatePairingPin()`
- **Components Included**:
  1. **Top Action Bar**: "Pair New Windows PC" button + "Download EasyPrint (.exe)" link.
  2. **Active 6-Digit Pairing Code Banner**: Large monospace display (`839210`) with a 1-click **"Copy PIN"** clipboard button and 15-minute expiration countdown.
  3. **Detected Physical Printers List**: Discovered by `EasyPrint.exe` (HP LaserJet, Canon, Epson) with spool names, status tags, and **"Set as Default"** spooler buttons.
  4. **Connected Windows Agent PCs**: List of paired counter computers with online pulsing dots and last-ping timestamps.

---

### Screen 9: Shop Profile & Direct UPI Settings (`resources/views/dashboard/shop.php`)
- **Route**: `GET /dashboard/shop`, `POST /dashboard/shop`
- **Controller**: `DashboardController::shop()`, `DashboardController::updateShop()`
- **Components Included**:
  1. Business Name & Custom Shop Slug (`/print/{slug}`).
  2. Personal Merchant UPI ID (`VPA`) & Account Holder Display Name.
  3. Brand Logo Upload Dropzone.
  4. Feature Toggles:
     - **Accept Cash at Counter** (Allow walk-in customers to pay cash instead of UPI).
     - **Auto-Print Paid Orders** (Automatically submit to spooler without manual approval).

---

### Screen 10: Billing & Subscription Paywall (`resources/views/dashboard/billing.php`)
- **Route**: `GET /dashboard/billing`, `GET /dashboard/billing/subscribe`
- **Controller**: `BillingController::index()`, `BillingController::subscribe()`
- **Integration**: Razorpay JavaScript SDK (`checkout.razorpay.com/v1/checkout.js`).
- **Components Included**:
  1. **Hard Paywall Alert Banner**: Shown when subscription expires or is in trial. Blocks navigation until plan is purchased.
  2. **Current Plan Card**: Plan name, status (ACTIVE vs EXPIRED), expiration date.
  3. **Subscription Plans Grid**:
     - **Monthly Basic** (₹99.00 / 30 Days) — 1 Printer, 1,500 prints/mo.
     - **Pro Unlimited** (₹499.00 / 30 Days) — 5 Printers, Unlimited prints, Auto-spooler, Audio alerts.
     - **Annual Enterprise** (₹3,999.00 / 365 Days) — 10 Printers, Unlimited prints, 2 Months Free.
  4. **1-Click Checkout Button**: Triggers simulated dev checkout or live Razorpay modal.

---

### Screen 11: Printable A4 Shop Counter Poster (`resources/views/dashboard/poster.php`)
- **Route**: `GET /dashboard/poster`, `GET /poster/{shopSlug}`
- **Controller**: `PosterController::generate()`
- **CSS**: Includes print media queries (`@media print { @page { size: A4 portrait; margin: 0; } }`).
- **Components Included**:
  1. Print Action Toolbar: "Print A4 Poster" & "Close" (hidden on actual paper printout).
  2. Shop Logo & Shop Name Banner.
  3. Subtitle: *"Scan to Print Documents Instantly • 100% Automated"*.
  4. High-Resolution QR Code (Generated for `https://yourdomain/print/{slug}`).
  5. 3-Step Visual Icons: 1. Scan QR &rarr; 2. Upload Document &rarr; 3. Pay via UPI & Collect Prints.
  6. Payment Badges: Google Pay, PhonePe, Paytm, BHIM UPI.
  7. Footer Note: *"Direct Bank-to-Bank Payment • No WhatsApp Contact Sharing"*.

---

## 7. Stitch Redesign Instructions & Prompts

When feeding this file to **Stitch** (or any AI UI generation engine), use the following prompt:

```text
PROMPT FOR STITCH:
You are an elite Product Designer and Design Systems Architect. 
Redesign the complete user interface for "EasyPrint SaaS" using the specifications and code provided in Design.md.

Design Goals:
1. Transform the application into an ultra-modern, high-converting SaaS product matching the visual standard of Linear.app, Stripe, and Vercel.
2. Elevate the Mobile Customer Kiosk into an app-like, haptic-rich, zero-friction self-service experience for walk-in print customers.
3. Modernize the Merchant Console with obsidian dark-mode aesthetics, rich glassmorphic cards, crisp typography, and glanceable live metrics.
4. Maintain all existing functional requirements:
   - 4-Step Customer Kiosk flow (Upload, Configure, Direct UPI QR, Live Spooler Status).
   - Live Order Stream with audio alarm toggle and status badges.
   - 6-digit one-click copy pairing PIN banner for the Windows desktop agent.
   - Dynamic Rate Card Matrix for A4, A3, Legal paper.
   - Print-ready A4 Counter Poster layout.
5. Provide clean, modular, and responsive HTML + Tailwind CSS code for each screen.
```

