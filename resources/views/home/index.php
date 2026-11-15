<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($title ?? 'EasyPrint SaaS - #1 Print Automation & QR Kiosk for Cyber Cafes') ?></title>
    
    <!-- Meta SEO -->
    <meta name="description" content="EasyPrint automates your Xerox &amp; Cyber Cafe print counter. Zero WhatsApp hassle, 0% platform commission, direct UPI payments to your bank, and silent Windows auto-printing.">
    <meta name="keywords" content="print automation, xerox shop software, cyber cafe printing, upi print kiosk, silent printer spooler, easyprint saas">

    <!-- Tailwind CSS CDN with plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;family=JetBrains+Mono:wght@400;500;600&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#FFF7ED',
                            100: '#FFEDD5',
                            200: '#FED7AA',
                            300: '#FDBA74',
                            400: '#FB923C',
                            500: '#F97316',
                            600: '#EA580C',
                            700: '#C2410C',
                            800: '#9A3412',
                            900: '#7C2D12'
                        },
                        carbon: {
                            950: '#0F1117',
                            900: '#141721',
                            850: '#1A1E2B',
                            800: '#222736',
                            700: '#32384C',
                            600: '#4B5563'
                        },
                        surface: {
                            base: '#FAF9F6',
                            low: '#F4F4F0',
                            card: '#FFFFFF',
                            border: '#E4E4E7'
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Inter"', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace']
                    },
                    boxShadow: {
                        'warm-sm': '0 1px 2px 0 rgba(15, 17, 23, 0.05)',
                        'warm-md': '0 4px 16px -2px rgba(15, 17, 23, 0.08), 0 2px 4px -2px rgba(15, 17, 23, 0.04)',
                        'warm-lg': '0 12px 32px -4px rgba(15, 17, 23, 0.08), 0 4px 12px -2px rgba(15, 17, 23, 0.04)',
                        'warm-elevated': '0 24px 48px -12px rgba(15, 17, 23, 0.12), 0 0 0 1px rgba(228, 228, 231, 0.8)',
                        'glow-orange': '0 10px 25px -5px rgba(249, 115, 22, 0.35)'
                    }
                }
            }
        };
    </script>
    <style>
        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            background-color: #FAF9F6;
            color: #18181B;
            letter-spacing: -0.011em;
            overflow-x: hidden;
        }
        .tech-grid-lines {
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(15, 17, 23, 0.035) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(15, 17, 23, 0.035) 1px, transparent 1px);
        }
        .light-card-interactive {
            background: #FFFFFF;
            border: 1px solid #E4E4E7;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .light-card-interactive:hover {
            border-color: #FDBA74;
            transform: translateY(-2px);
            box-shadow: 0 16px 32px -10px rgba(249, 115, 22, 0.12), 0 4px 12px -2px rgba(15, 17, 23, 0.04);
        }
        input[type=range] {
            -webkit-appearance: none;
            background: #E4E4E7;
            border-radius: 9999px;
            height: 6px;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 18px;
            width: 18px;
            border-radius: 50%;
            background: #F97316;
            border: 2px solid #FFFFFF;
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.4);
            cursor: pointer;
            transition: transform 0.15s ease;
        }
        input[type=range]::-webkit-slider-thumb:hover {
            transform: scale(1.18);
            background: #EA580C;
        }
    </style>
</head>
<body class="selection:bg-brand-500 selection:text-white bg-[#FAF9F6] text-zinc-900">
    <!-- Announcement Bar -->
    <div class="border-b border-orange-200/70 bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50/60 px-4 py-2 text-xs">
        <div class="max-w-7xl mx-auto flex items-center justify-between sm:justify-center gap-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-mono text-[10px] uppercase font-bold bg-brand-500 text-white shadow-xs">Spooler v2.9</span>
                <span class="text-zinc-700 font-medium hidden sm:inline">Windows Silent Spooler Released: Zero commission on prints with direct UPI settlement.</span>
                <span class="text-zinc-700 font-medium sm:hidden">Silent Spooler v2.9 live.</span>
            </div>
            <a class="font-semibold text-brand-600 hover:text-brand-700 transition flex items-center gap-1 group" href="#how-it-works">
                <span>Inspect architecture</span>
                <span class="group-hover:translate-x-0.5 transition-transform">→</span>
            </a>
        </div>
    </div>

    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 w-full bg-[#FAF9F6]/85 backdrop-blur-xl border-b border-zinc-200/80 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand -->
            <a class="flex items-center gap-3 group" href="/">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-amber-600 flex items-center justify-center shadow-glow-orange border border-white/40">
                    <span class="material-symbols-outlined text-white text-[20px]">print</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-lg tracking-tight text-zinc-950 font-['Plus_Jakarta_Sans']">EasyPrint</span>
                    <span class="font-mono text-[10px] text-brand-700 font-semibold uppercase tracking-widest px-2 py-0.5 rounded-full bg-brand-50 border border-brand-200">Kiosk OS</span>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="hidden lg:flex items-center gap-7 text-xs font-semibold text-zinc-600">
                <a class="hover:text-zinc-950 transition-colors" href="#problem-solution">The Dilemma</a>
                <a class="hover:text-zinc-950 transition-colors" href="#how-it-works">15-Sec Flow</a>
                <a class="hover:text-zinc-950 transition-colors" href="#windows-spooler">Hardware Spooler</a>
                <a class="hover:text-zinc-950 transition-colors" href="#calculator">ROI Calculator</a>
                <a class="hover:text-zinc-950 transition-colors" href="#pricing">Subscription</a>
                <a class="hover:text-zinc-950 transition-colors" href="#faq">FAQ</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-300 text-[11px] font-mono font-medium text-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>2,490+ Active Counter Nodes</span>
                </div>

                <?php if ($isLoggedIn): ?>
                    <a href="/dashboard" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-zinc-950 hover:bg-zinc-800 transition shadow-sm flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                <?php else: ?>
                    <a href="/login" class="text-xs font-bold text-zinc-700 hover:text-zinc-950 px-2 py-1">
                        Sign In
                    </a>
                    <a class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-500 hover:bg-brand-600 transition shadow-sm hover:shadow" href="/register">
                        Deploy Counter
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Bespoke Hero Section -->
    <section class="relative pt-16 pb-20 md:pt-24 md:pb-32 overflow-hidden tech-grid-lines bg-[#FAF9F6]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <!-- Micro Tagline Pill -->
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white border border-brand-200 text-xs font-mono text-zinc-700 mb-8 shadow-warm-sm">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-medium text-zinc-800">Zero Commission</span>
                <span class="text-zinc-300">|</span>
                <span class="text-brand-600 font-semibold">Direct Merchant UPI Settlement</span>
            </div>

            <!-- Hero Title -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-zinc-950 tracking-tight max-w-5xl mx-auto leading-[1.08]">
                Zero WhatsApp queues. <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 via-brand-500 to-amber-500">
                    Instant counter prints.
                </span>
            </h1>

            <!-- Hero Subtitle -->
            <p class="mt-6 text-base sm:text-lg text-zinc-600 max-w-3xl mx-auto font-normal leading-relaxed">
                Turn any counter Windows PC into an unattended self-service print terminal. Customers scan your poster QR, pay directly to your personal UPI, and documents spool straight to your printer in seconds.
            </p>

            <!-- Hero Quick Actions -->
            <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-3.5 max-w-md mx-auto">
                <a class="w-full sm:w-auto px-7 py-3.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-brand-500 hover:bg-brand-600 transition-all border border-brand-400 flex items-center justify-center gap-2 shadow-glow-orange hover:shadow-lg" href="/register">
                    <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                    <span>Deploy Shop in 3 Minutes</span>
                </a>
                <a class="w-full sm:w-auto px-7 py-3.5 rounded-xl text-xs sm:text-sm font-semibold text-zinc-800 bg-white hover:bg-zinc-50 border border-zinc-300 hover:border-zinc-400 transition flex items-center justify-center gap-2 shadow-warm-sm" href="/print/<?= htmlspecialchars($demoSlug ?? 'pixaprint') ?>" target="_blank">
                    <span class="material-symbols-outlined text-[18px] text-brand-600">devices</span>
                    <span>Test Customer Kiosk Demo</span>
                </a>
            </div>

            <!-- Tactile Spec Dock -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-y-2.5 gap-x-6 text-xs text-zinc-600 font-mono font-medium">
                <div class="flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-md border border-zinc-200/80 shadow-xs">
                    <span class="text-emerald-600 font-bold">✓</span> 0% Platform Commission
                </div>
                <div class="flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-md border border-zinc-200/80 shadow-xs">
                    <span class="text-emerald-600 font-bold">✓</span> &lt; 2.4s Spool Latency
                </div>
                <div class="flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-md border border-zinc-200/80 shadow-xs">
                    <span class="text-emerald-600 font-bold">✓</span> HP, Canon, Epson, Brother Ready
                </div>
                <div class="flex items-center gap-1.5 bg-white px-2.5 py-1 rounded-md border border-zinc-200/80 shadow-xs">
                    <span class="text-emerald-600 font-bold">✓</span> ₹0 Hardware Cost (Uses Existing PCs)
                </div>
            </div>

            <!-- Hero Interactive Console Artifact -->
            <div class="mt-14 max-w-5xl mx-auto rounded-3xl bg-white border border-zinc-200/80 p-4 sm:p-6 shadow-warm-elevated text-left">
                <!-- Header Subtitle Strip -->
                <div class="flex flex-wrap items-center justify-between gap-3 pb-4 mb-5 border-b border-zinc-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="font-mono text-xs font-bold text-zinc-900">Live Counter Transaction Preview</span>
                        <span class="text-zinc-300">•</span>
                        <span class="text-[11px] font-mono text-zinc-500">Real-time bilateral sync</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] font-mono font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                        <span>Direct UPI Settlement</span>
                        <span>→</span>
                        <span>Silent Win32 Print</span>
                    </div>
                </div>

                <!-- Split Mockup Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    <!-- Left: Customer's Phone Screen (5 cols) -->
                    <div class="lg:col-span-5 bg-[#FAF9F6] rounded-2xl border border-zinc-200 p-4 sm:p-5 flex flex-col justify-between shadow-xs">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-200/80 mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-brand-500 flex items-center justify-center text-[10px] font-bold text-white shadow-xs">EP</div>
                                    <div>
                                        <div class="text-xs font-bold text-zinc-900">Pixaprint Solutions</div>
                                        <div class="text-[10px] text-zinc-500 font-mono">Counter 1 • Self-Service</div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-mono bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-0.5 rounded-full font-bold">0% Cut</span>
                            </div>

                            <div class="rounded-xl bg-white border border-brand-200 p-3 mb-3 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="p-2 rounded-lg bg-brand-50 text-brand-600 font-mono text-xs font-bold border border-brand-100">PDF</div>
                                        <div>
                                            <div class="text-xs font-bold text-zinc-900 truncate max-w-[150px]">College_Project_Final.pdf</div>
                                            <div class="text-[10px] text-zinc-500 font-mono">12 Pages • Double-Sided (B&amp;W)</div>
                                        </div>
                                    </div>
                                    <span class="text-emerald-700 font-bold text-xs">₹18.00</span>
                                </div>
                            </div>

                            <div class="p-3 bg-white rounded-xl border border-zinc-200 text-center space-y-2">
                                <span class="text-[10px] text-zinc-500 font-mono font-bold block uppercase">Direct Merchant UPI QR</span>
                                <div class="w-24 h-24 bg-zinc-100 rounded-lg mx-auto flex items-center justify-center p-1 border">
                                    <span class="material-symbols-outlined text-[64px] text-zinc-800">qr_code_2</span>
                                </div>
                                <span class="text-xs font-extrabold text-brand-600">Scan via GPay / PhonePe</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-zinc-200 text-center">
                            <span class="text-[10px] font-mono text-emerald-700 font-bold">✓ Direct to Shopkeeper Bank (₹0 platform cut)</span>
                        </div>
                    </div>

                    <!-- Right: Merchant Windows Console (7 cols) -->
                    <div class="lg:col-span-7 bg-white rounded-2xl border border-zinc-200 p-4 sm:p-5 flex flex-col justify-between shadow-xs">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-xs font-bold text-zinc-900 font-mono">Windows Counter Spooler (Agent Active)</span>
                                </div>
                                <span class="text-[10px] font-mono text-zinc-500">spoolsv-agent.exe</span>
                            </div>

                            <div class="p-3 rounded-xl bg-zinc-50 border border-zinc-200 mb-3">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-brand-600 text-[18px]">print</span>
                                        <span class="text-zinc-900 font-semibold text-xs">HP LaserJet Pro MFP M126nw (USB001)</span>
                                    </div>
                                    <span class="text-brand-600 text-[10px] font-bold bg-brand-50 px-2 py-0.5 rounded border border-brand-200">Spooling Order MP-9042</span>
                                </div>
                                <div class="w-full bg-zinc-200 rounded-full h-1.5 overflow-hidden mb-1">
                                    <div class="bg-brand-500 h-1.5 rounded-full" style="width: 75%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] text-zinc-500 font-medium">
                                    <span>Duplex Feed: Active</span>
                                    <span>Spool Speed: 1.2s/pg</span>
                                </div>
                            </div>

                            <div class="bg-zinc-50 rounded-xl p-3 text-[11px] border border-zinc-200 space-y-1.5 text-zinc-600 font-mono">
                                <div class="text-zinc-500">[11:42:01] Customer QR scanned (Counter Kiosk)</div>
                                <div class="text-emerald-700 font-medium">[11:42:09] Direct UPI payment verified: ₹18.00 (PhonePe UTR: 489201948)</div>
                                <div class="text-brand-600 font-medium">[11:42:10] Silent Win32 spool dispatched to HP LaserJet M126nw</div>
                                <div class="text-zinc-800 font-medium flex items-center gap-1.5">
                                    <span>🔔 [11:42:11] Audio chime alert triggered</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-zinc-200 flex items-center justify-between text-[10px] text-zinc-500 font-mono">
                            <span>Status: Online &amp; Listening</span>
                            <span class="text-emerald-700 font-semibold">Zero-touch auto print</span>
                        </div>
                    </div>
                </div>

                <!-- Metrics Strip -->
                <div class="mt-5 pt-4 border-t border-zinc-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-center font-mono">
                    <div class="bg-[#FAF9F6] p-2.5 rounded-xl border border-zinc-200/80">
                        <div class="text-[10px] text-zinc-500 font-medium uppercase">Today's Counter Revenue</div>
                        <div class="text-base font-extrabold text-zinc-950 mt-0.5">₹1,840.00</div>
                        <div class="text-[9px] text-emerald-700 font-semibold">100% to shop UPI</div>
                    </div>
                    <div class="bg-[#FAF9F6] p-2.5 rounded-xl border border-zinc-200/80">
                        <div class="text-[10px] text-zinc-500 font-medium uppercase">Jobs Auto-Spooled</div>
                        <div class="text-base font-extrabold text-brand-600 mt-0.5">42 Jobs</div>
                        <div class="text-[9px] text-zinc-500">0 paper jams reported</div>
                    </div>
                    <div class="bg-[#FAF9F6] p-2.5 rounded-xl border border-zinc-200/80">
                        <div class="text-[10px] text-zinc-500 font-medium uppercase">Staff Minutes Saved</div>
                        <div class="text-base font-extrabold text-emerald-700 mt-0.5">126 mins</div>
                        <div class="text-[9px] text-zinc-500">Zero manual file handling</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Narrative: Dilemma vs Autopilot -->
    <section class="py-24 border-t border-zinc-200/80 bg-[#F4F4F0]" id="problem-solution">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-[11px] font-mono font-bold uppercase tracking-widest text-brand-700 bg-brand-100/70 px-3 py-1 rounded-full border border-brand-200">Problem vs Architecture</span>
                <h2 class="text-3xl sm:text-5xl font-extrabold text-zinc-950 mt-4 tracking-tight">
                    The Death of WhatsApp Counter Printing
                </h2>
                <p class="text-zinc-600 text-sm sm:text-base mt-3">
                    Manual file transfers on WhatsApp compromise security, slow down your line, and cost you hundreds of rupees in lost unpaid jobs every single week.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left: The WhatsApp Chaos -->
                <div class="rounded-3xl bg-white border border-red-200 p-8 relative shadow-warm-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-9 h-9 rounded-xl bg-red-50 border border-red-200 flex items-center justify-center text-red-600 font-mono font-bold text-base">✕</div>
                        <div>
                            <h3 class="text-base font-bold text-zinc-950">The WhatsApp Manual Way</h3>
                            <p class="text-xs text-red-600 font-mono font-semibold">Chaos, malware, and counter arguments</p>
                        </div>
                    </div>
                    <ul class="space-y-4 text-xs sm:text-sm text-zinc-600">
                        <li class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-red-500 mt-2 shrink-0"></span>
                            <div>
                                <strong class="text-zinc-900 block font-semibold">Personal phone number leak:</strong>
                                Dozens of strangers text your personal mobile number all day, spamming after shop hours.
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-red-500 mt-2 shrink-0"></span>
                            <div>
                                <strong class="text-zinc-900 block font-semibold">Malware from unknown files:</strong>
                                Downloading raw executables, infected Word docs, and customer USB drives ruins shop PCs.
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-red-500 mt-2 shrink-0"></span>
                            <div>
                                <strong class="text-zinc-900 block font-semibold">Unpaid sheets &amp; change friction:</strong>
                                Printing before collecting money results in "I don't have change" or customers walking out unpaid.
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Right: EasyPrint Autopilot -->
                <div class="rounded-3xl bg-white border-2 border-brand-400 p-8 relative shadow-warm-md">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-9 h-9 rounded-xl bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-600 font-mono font-bold text-base">✓</div>
                        <div>
                            <h3 class="text-base font-bold text-zinc-950">The EasyPrint Autopilot Way</h3>
                            <p class="text-xs text-brand-600 font-mono font-semibold">Zero touch, zero commission, self-service</p>
                        </div>
                    </div>
                    <ul class="space-y-4 text-xs sm:text-sm text-zinc-600">
                        <li class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-brand-500 mt-2 shrink-0"></span>
                            <div>
                                <strong class="text-zinc-900 block font-semibold">Zero contact sharing:</strong>
                                Customer points their phone camera at your counter poster QR. Opens directly in browser.
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-brand-500 mt-2 shrink-0"></span>
                            <div>
                                <strong class="text-zinc-900 block font-semibold">Auto-sandbox &amp; GDPR shredding:</strong>
                                Files are sanitized, rendered, and automatically scrubbed permanently from RAM after print.
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-2 h-2 rounded-full bg-brand-500 mt-2 shrink-0"></span>
                            <div>
                                <strong class="text-zinc-900 block font-semibold">100% direct prepaid UPI:</strong>
                                The printer will not spool a single page until payment lands verified in your own bank account.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- 15-Sec Flow -->
    <section class="py-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" id="how-it-works">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-[11px] font-mono font-bold uppercase tracking-widest text-brand-700 bg-brand-100/70 px-3 py-1 rounded-full border border-brand-200">15-Second Flow</span>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-zinc-950 mt-4 tracking-tight">
                From QR Scan to Paper Output
            </h2>
            <p class="text-zinc-600 text-sm sm:text-base mt-3">
                No apps for customers to download. Works instantly on any iPhone or Android camera.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="light-card-interactive rounded-2xl p-6 flex flex-col justify-between shadow-warm-sm">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <span class="font-mono text-xs font-bold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-200">STEP 01</span>
                        <span class="text-zinc-400 font-mono text-[11px] font-semibold">t + 0s</span>
                    </div>
                    <h3 class="text-base font-bold text-zinc-950 mb-2">Customer Scans QR</h3>
                    <p class="text-xs text-zinc-600 leading-relaxed">
                        Points mobile camera at your A4 counter poster. Kiosk loads instantly in browser without requiring an app download.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 font-mono text-[11px] text-zinc-600 flex items-center gap-1.5 font-medium">
                    <span class="text-emerald-600 font-bold">✓</span> Instant Zero-Install Web
                </div>
            </div>

            <div class="light-card-interactive rounded-2xl p-6 flex flex-col justify-between shadow-warm-sm">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <span class="font-mono text-xs font-bold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-200">STEP 02</span>
                        <span class="text-zinc-400 font-mono text-[11px] font-semibold">t + 5s</span>
                    </div>
                    <h3 class="text-base font-bold text-zinc-950 mb-2">Upload &amp; Configure</h3>
                    <p class="text-xs text-zinc-600 leading-relaxed">
                        Customer attaches PDF, DOCX, or Image. Pure-PHP engine calculates pages, let them pick B&amp;W or Color, duplex, and copies.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 font-mono text-[11px] text-zinc-600 flex items-center gap-1.5 font-medium">
                    <span class="text-emerald-600 font-bold">✓</span> Real-Time Fare Matrix
                </div>
            </div>

            <div class="light-card-interactive rounded-2xl p-6 flex flex-col justify-between shadow-warm-sm">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <span class="font-mono text-xs font-bold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-200">STEP 03</span>
                        <span class="text-zinc-400 font-mono text-[11px] font-semibold">t + 10s</span>
                    </div>
                    <h3 class="text-base font-bold text-zinc-950 mb-2">Direct UPI Settlement</h3>
                    <p class="text-xs text-zinc-600 leading-relaxed">
                        Customer taps GPay, PhonePe, or Paytm. Exact amount settles instantly to your personal UPI ID with zero platform fee.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 font-mono text-[11px] text-zinc-600 flex items-center gap-1.5 font-medium">
                    <span class="text-emerald-600 font-bold">₹</span> 0% Platform Commission
                </div>
            </div>

            <div class="light-card-interactive rounded-2xl p-6 flex flex-col justify-between shadow-warm-sm">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <span class="font-mono text-xs font-bold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-200">STEP 04</span>
                        <span class="text-zinc-400 font-mono text-[11px] font-semibold">t + 15s</span>
                    </div>
                    <h3 class="text-base font-bold text-zinc-950 mb-2">Silent Win32 Print</h3>
                    <p class="text-xs text-zinc-600 leading-relaxed">
                        Windows spooler receives print event, sends raw script to the physical printer, and audio chime alerts shopkeeper.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 font-mono text-[11px] text-zinc-600 flex items-center gap-1.5 font-medium">
                    <span class="text-emerald-600 font-bold">✓</span> Zero Staff Keystrokes
                </div>
            </div>
        </div>
    </section>

    <!-- Hardware Spooler Engineering Section -->
    <section class="py-24 border-t border-zinc-200/80 bg-[#F4F4F0]" id="windows-spooler">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white border border-brand-200 text-xs font-mono font-semibold text-brand-700 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                        <span>Native Desktop Client</span>
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-extrabold text-zinc-950 tracking-tight leading-tight">
                        Enterprise Spooler. <br/>
                        Compatible With Any Printer You Already Own.
                    </h2>
                    <p class="text-zinc-600 text-sm sm:text-base leading-relaxed">
                        No expensive proprietary touchscreen hardware. EasyPrint runs as a lightweight Windows background executable (<code class="text-brand-700 font-mono text-xs bg-brand-50 px-1.5 py-0.5 rounded border border-brand-200">EasyPrint.exe</code>) on Windows 10 or 11. It hooks directly into the OS print spooler subsystem via native Win32 drivers.
                    </p>
                    <div class="grid grid-cols-3 gap-2.5 pt-2 font-mono text-xs">
                        <div class="p-3 rounded-xl bg-white border border-zinc-200 flex items-center gap-2 shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-zinc-800 font-medium">HP LaserJet</span>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-zinc-200 flex items-center gap-2 shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-zinc-800 font-medium">Canon LBP</span>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-zinc-200 flex items-center gap-2 shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-zinc-800 font-medium">Epson EcoTank</span>
                        </div>
                    </div>
                    <div class="pt-2 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <a class="px-6 py-3 rounded-xl text-xs font-bold text-white bg-brand-500 hover:bg-brand-600 border border-brand-400 transition flex items-center gap-2 shadow-glow-orange" href="/downloads/EasyPrint.exe">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            <span>Download EasyPrint (.exe)</span>
                        </a>
                        <span class="text-zinc-500 text-xs font-mono">35.6 MB • x64 Native • Zero dependencies</span>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="rounded-3xl bg-zinc-950 border border-zinc-800 shadow-2xl overflow-hidden font-mono text-xs">
                        <div class="bg-zinc-900 px-4 py-3 border-b border-zinc-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="font-bold text-white text-xs">EasyPrint Agent Tray v2.9</span>
                            </div>
                            <span class="text-zinc-400 text-[11px]">RAM: 28MB | CPU: 0.1%</span>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="p-3.5 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-between">
                                <div>
                                    <div class="text-[11px] text-zinc-400">SHOP IDENTIFIER</div>
                                    <div class="text-white font-bold text-xs mt-0.5">PIXAPRINT-DEMO-01</div>
                                </div>
                                <span class="text-[10px] text-emerald-400 bg-emerald-950/80 border border-emerald-500/30 px-2.5 py-0.5 rounded-full font-bold">AUTHENTICATED</span>
                            </div>
                            <div class="bg-zinc-900/90 rounded-xl p-3.5 text-[10px] text-zinc-400 space-y-1.5 border border-zinc-800">
                                <div>[SYSTEM] Local Win32 print queue listener started.</div>
                                <div class="text-brand-300 font-semibold">[SPOOL] Received Job: "Project_Report.pdf"</div>
                                <div class="text-emerald-400">[EXEC] Dispatched RAW data stream to HP LaserJet. Finished in 1.84s.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ROI Calculator -->
    <section class="py-24 border-t border-zinc-200/80 bg-[#FAF9F6]" id="calculator">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[11px] font-mono font-bold uppercase tracking-widest text-emerald-800 bg-emerald-100/70 px-3 py-1 rounded-full border border-emerald-200">Operational ROI Model</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-zinc-950 mt-4 tracking-tight">
                    Estimate Your Shop's Monthly Return
                </h2>
            </div>

            <div class="rounded-3xl bg-white border border-zinc-200 p-8 shadow-warm-elevated">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-bold text-zinc-900">Average Walk-in Customers / Day</label>
                                <span class="font-mono text-brand-700 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-200 text-[11px] font-bold" id="customersValue">75 customers/day</span>
                            </div>
                            <input class="w-full cursor-pointer accent-brand-500" id="customersRange" max="250" min="10" step="5" type="range" value="75"/>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-bold text-zinc-900">Average Pages per Order</label>
                                <span class="font-mono text-brand-700 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-200 text-[11px] font-bold" id="pagesValue">6 pages</span>
                            </div>
                            <input class="w-full cursor-pointer accent-brand-500" id="pagesRange" max="25" min="1" step="1" type="range" value="6"/>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-bold text-zinc-900">Average Print Price / Page</label>
                                <span class="font-mono text-brand-700 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-200 text-[11px] font-bold" id="priceValue">₹3.00 / page</span>
                            </div>
                            <input class="w-full cursor-pointer accent-brand-500" id="priceRange" max="10" min="1.5" step="0.5" type="range" value="3.0"/>
                        </div>
                    </div>

                    <div class="lg:col-span-5 rounded-2xl bg-zinc-950 text-white p-7 text-center shadow-warm-md">
                        <div class="text-[11px] font-mono uppercase tracking-wider text-zinc-400 font-semibold">Monthly Printing Turnover</div>
                        <div class="text-4xl font-extrabold text-white mt-2 mb-1 tracking-tight" id="monthlyRevenue">₹40,500</div>
                        <div class="text-[11px] font-mono text-emerald-400 font-semibold">100% yours • ₹0 commission deducted</div>
                        <div class="mt-6 pt-6 border-t border-zinc-800 grid grid-cols-2 gap-4">
                            <div class="text-left">
                                <div class="text-[10px] font-mono uppercase text-zinc-400 font-semibold">Counter Time Saved</div>
                                <div class="text-xl font-bold text-white mt-1" id="timeSaved">56 Hours</div>
                                <div class="text-[10px] text-zinc-400 font-mono">No WhatsApp browsing</div>
                            </div>
                            <div class="text-left">
                                <div class="text-[10px] font-mono uppercase text-zinc-400 font-semibold">Pages Spooled</div>
                                <div class="text-xl font-bold text-brand-400 mt-1" id="monthlyPages">13,500 Pgs</div>
                                <div class="text-[10px] text-zinc-400 font-mono">Auto executed</div>
                            </div>
                        </div>
                        <a class="mt-6 block w-full py-3 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-xs transition shadow-glow-orange text-center" href="/register">
                            Setup Counter Spooler →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-24 border-t border-zinc-200/80 bg-[#F4F4F0]" id="pricing">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[11px] font-mono font-bold uppercase tracking-widest text-brand-700 bg-brand-100/70 px-3 py-1 rounded-full border border-brand-200">Fair Subscription</span>
                <h2 class="text-3xl sm:text-5xl font-extrabold text-zinc-950 mt-4 tracking-tight">
                    One Flat Fee. Keep 100% of Print Earnings.
                </h2>
                <p class="text-zinc-600 text-sm sm:text-base mt-3">
                    No per-print commission. Direct UPI to your personal VPA.
                </p>
                <!-- Monthly / Annual Toggle -->
                <div class="mt-8 flex items-center justify-center gap-3">
                    <span class="text-xs font-bold text-zinc-900" id="labelMonthly">Monthly</span>
                    <button aria-label="Toggle Billing Cycle" class="w-12 h-6 rounded-full bg-zinc-300 p-0.5 flex items-center transition" id="billingToggle" type="button">
                        <span class="w-5 h-5 rounded-full bg-brand-500 transition transform shadow-xs" id="toggleKnob"></span>
                    </button>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-semibold text-zinc-500" id="labelAnnual">Annual</span>
                        <span class="text-[10px] font-mono font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">SAVE 20%</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                <!-- Tier 1 -->
                <div class="rounded-3xl bg-white border border-zinc-200 p-8 flex flex-col justify-between shadow-warm-sm">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-zinc-950">Monthly Basic</h3>
                            <span class="text-[10px] font-mono bg-zinc-100 text-zinc-600 px-2 py-0.5 rounded-md border border-zinc-200 font-semibold">Single PC</span>
                        </div>
                        <p class="text-zinc-500 text-xs mb-6">
                            Ideal for neighborhood stationery shops with one active printer counter.
                        </p>
                        <div class="flex items-baseline gap-1 mb-6">
                            <span class="text-3xl font-extrabold text-zinc-950 price-display-monthly">₹99</span>
                            <span class="text-3xl font-extrabold text-zinc-950 price-display-annual hidden">₹79</span>
                            <span class="text-xs text-zinc-400 font-mono">/ month</span>
                        </div>
                        <ul class="space-y-3 text-xs text-zinc-600 pb-6 border-b border-zinc-100">
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> 1 Connected Windows Machine</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> Up to 1,500 Pages / Month</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> 0% Platform Commission</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> Direct Merchant UPI Payments</li>
                        </ul>
                    </div>
                    <a class="mt-6 block text-center py-3 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-bold text-xs transition border border-zinc-200" href="/register">
                        Get Started
                    </a>
                </div>

                <!-- Tier 2 -->
                <div class="rounded-3xl bg-white border-2 border-brand-500 p-8 flex flex-col justify-between relative shadow-warm-lg ring-4 ring-brand-500/10">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-brand-500 text-white text-[10px] font-mono font-bold uppercase px-3 py-1 rounded-full shadow-sm tracking-wider">
                        Most Popular for High-Footfall Shops
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-4 mt-2">
                            <h3 class="text-lg font-bold text-zinc-950">Pro Unlimited</h3>
                            <span class="text-[10px] font-mono bg-brand-50 text-brand-700 px-2 py-0.5 rounded-md border border-brand-200 font-bold">Unthrottled</span>
                        </div>
                        <p class="text-zinc-500 text-xs mb-6">
                            For high-traffic photocopy centers, cyber cafes, and college kiosks.
                        </p>
                        <div class="flex items-baseline gap-1 mb-6">
                            <span class="text-3xl font-extrabold text-zinc-950 price-display-monthly">₹499</span>
                            <span class="text-3xl font-extrabold text-zinc-950 price-display-annual hidden">₹399</span>
                            <span class="text-xs text-zinc-400 font-mono">/ month</span>
                        </div>
                        <ul class="space-y-3 text-xs text-zinc-700 pb-6 border-b border-zinc-100">
                            <li class="flex items-center gap-2 font-medium text-brand-700"><span class="font-mono font-bold">✓</span> Unlimited Print Jobs &amp; Pages</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> Connect Up to 5 Physical Printers</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> Windows Silent Auto-Spooler (.exe)</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> Instant Audio Chimes on Payment</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> Dynamic Rate Card Matrix</li>
                        </ul>
                    </div>
                    <a class="mt-6 block text-center py-3 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-xs transition shadow-glow-orange" href="/register">
                        Start 14-Day Free Trial →
                    </a>
                </div>

                <!-- Tier 3 -->
                <div class="rounded-3xl bg-white border border-zinc-200 p-8 flex flex-col justify-between shadow-warm-sm">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-zinc-950">Annual Enterprise</h3>
                            <span class="text-[10px] font-mono bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded-md border border-emerald-200 font-bold">365 Days</span>
                        </div>
                        <p class="text-zinc-500 text-xs mb-6">
                            For multi-printer hubs, college campus print rooms, and shop clusters.
                        </p>
                        <div class="flex items-baseline gap-1 mb-6">
                            <span class="text-3xl font-extrabold text-zinc-950">₹3,999</span>
                            <span class="text-xs text-zinc-400 font-mono">/ year</span>
                        </div>
                        <ul class="space-y-3 text-xs text-zinc-600 pb-6 border-b border-zinc-100">
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> Up to 10 Connected Printers</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> Unlimited Prints &amp; Priority Queue</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> 2 Months Free Included</li>
                            <li class="flex items-center gap-2"><span class="text-emerald-600 font-mono font-bold">✓</span> Priority WhatsApp Tech Support</li>
                        </ul>
                    </div>
                    <a class="mt-6 block text-center py-3 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition" href="/register">
                        Select Enterprise
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion -->
    <section class="py-24 border-t border-zinc-200/80 bg-[#FAF9F6]" id="faq">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-[11px] font-mono font-bold uppercase tracking-widest text-brand-700 bg-brand-100/70 px-3 py-1 rounded-full border border-brand-200">Operational Clarity</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-zinc-950 mt-4 tracking-tight">
                    Frequently Asked Questions
                </h2>
            </div>

            <div class="space-y-3.5">
                <details class="group rounded-2xl bg-white border border-zinc-200 p-5 shadow-xs [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-zinc-900 text-sm">
                        <span>Do I need to buy a new printer or specific hardware?</span>
                        <span class="ml-4 transition group-open:rotate-180 text-brand-600 font-mono text-base font-bold">↓</span>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3">
                        No special hardware is required. Any standard Windows 10 or 11 PC paired with your existing USB, Wi-Fi, or LAN printers (HP LaserJet, Canon imageCLASS, Epson EcoTank, Brother, Ricoh) will work right away.
                    </p>
                </details>

                <details class="group rounded-2xl bg-white border border-zinc-200 p-5 shadow-xs [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-zinc-900 text-sm">
                        <span>Does EasyPrint deduct any cut from customer print payments?</span>
                        <span class="ml-4 transition group-open:rotate-180 text-brand-600 font-mono text-base font-bold">↓</span>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3">
                        Zero percent (0%). Customers pay directly to your personal UPI QR code (GPay, PhonePe, Paytm, BHIM). 100% of the customer's print charges settle directly in your own bank account immediately.
                    </p>
                </details>

                <details class="group rounded-2xl bg-white border border-zinc-200 p-5 shadow-xs [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-zinc-900 text-sm">
                        <span>How are customer confidential documents protected?</span>
                        <span class="ml-4 transition group-open:rotate-180 text-brand-600 font-mono text-base font-bold">↓</span>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-zinc-600 leading-relaxed border-t border-zinc-100 pt-3">
                        Security is built-in. Uploaded files are held in temporary storage and deleted automatically within 24 hours. No manual file management required.
                    </p>
                </details>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-zinc-200 text-zinc-600 py-12 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-brand-500 flex items-center justify-center text-white font-bold text-xs shadow-xs">EP</div>
                    <span class="text-base font-bold text-zinc-950 tracking-tight">EasyPrint SaaS</span>
                </div>
                <div class="text-[11px] font-mono text-zinc-500">
                    © 2026 EasyPrint Technologies. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- Interactive Scripts -->
    <script>
        // Interactive Calculator Logic
        (function initEstimator() {
            const customersRange = document.getElementById('customersRange');
            const pagesRange = document.getElementById('pagesRange');
            const priceRange = document.getElementById('priceRange');

            const customersVal = document.getElementById('customersValue');
            const pagesVal = document.getElementById('pagesValue');
            const priceVal = document.getElementById('priceValue');

            const monthlyRev = document.getElementById('monthlyRevenue');
            const timeSaved = document.getElementById('timeSaved');
            const monthlyPages = document.getElementById('monthlyPages');

            function updateCalculations() {
                if (!customersRange || !pagesRange || !priceRange) return;
                const customers = parseInt(customersRange.value, 10);
                const pages = parseInt(pagesRange.value, 10);
                const price = parseFloat(priceRange.value);

                customersVal.textContent = `${customers} customers/day`;
                pagesVal.textContent = `${pages} pages`;
                priceVal.textContent = `₹${price.toFixed(2)} / page`;

                const totalMonthlyPages = customers * pages * 30;
                const totalRevenue = totalMonthlyPages * price;
                const totalHoursSaved = Math.round((customers * 1.5 * 30) / 60);

                monthlyRev.textContent = `₹${totalRevenue.toLocaleString('en-IN')}`;
                timeSaved.textContent = `${totalHoursSaved} Hours`;
                monthlyPages.textContent = `${totalMonthlyPages.toLocaleString('en-IN')} Pgs`;
            }

            if (customersRange && pagesRange && priceRange) {
                customersRange.addEventListener('input', updateCalculations);
                pagesRange.addEventListener('input', updateCalculations);
                priceRange.addEventListener('input', updateCalculations);
                updateCalculations();
            }
        })();

        // Billing Cycle Toggle Logic
        (function initBilling() {
            const toggle = document.getElementById('billingToggle');
            const knob = document.getElementById('toggleKnob');
            const labelMonthly = document.getElementById('labelMonthly');
            const labelAnnual = document.getElementById('labelAnnual');
            const monthlyPrices = document.querySelectorAll('.price-display-monthly');
            const annualPrices = document.querySelectorAll('.price-display-annual');

            let isAnnual = false;

            if (toggle && knob) {
                toggle.addEventListener('click', () => {
                    isAnnual = !isAnnual;
                    if (isAnnual) {
                        knob.classList.add('translate-x-6');
                        labelAnnual.classList.remove('text-zinc-500');
                        labelAnnual.classList.add('text-zinc-950', 'font-bold');
                        labelMonthly.classList.remove('text-zinc-950', 'font-bold');
                        labelMonthly.classList.add('text-zinc-500');

                        monthlyPrices.forEach(el => el.classList.add('hidden'));
                        annualPrices.forEach(el => el.classList.remove('hidden'));
                    } else {
                        knob.classList.remove('translate-x-6');
                        labelMonthly.classList.remove('text-zinc-500');
                        labelMonthly.classList.add('text-zinc-950', 'font-bold');
                        labelAnnual.classList.remove('text-zinc-950', 'font-bold');
                        labelAnnual.classList.add('text-zinc-500');

                        monthlyPrices.forEach(el => el.classList.remove('hidden'));
                        annualPrices.forEach(el => el.classList.add('hidden'));
                    }
                });
            }
        })();
    </script>
</body>
</html>