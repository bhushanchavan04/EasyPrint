<!-- Metric & Telemetry KPI Grid -->
<section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-md mb-space-lg">
    <!-- Metric 1: Printed Pages -->
    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="flex items-start justify-between">
            <div class="flex flex-col">
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Today's Printed Pages</span>
                <span class="font-display-xl text-display-xl text-on-surface leading-none mt-2" id="statTodayPrints"><?= number_format($metrics['today_prints'] ?? 0) ?></span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-on-surface-variant group-hover:bg-primary-fixed group-hover:text-on-primary-fixed transition-colors">
                <span class="material-symbols-outlined text-[24px]">description</span>
            </div>
        </div>
        <div class="flex items-center gap-space-xs mt-space-md pt-space-xs text-on-surface-variant">
            <span class="inline-flex items-center font-label-sm text-label-sm text-primary font-bold">
                <span class="material-symbols-outlined text-[16px]">print</span>
            </span>
            <span class="font-body-sm text-body-sm">Physical sheets spooled</span>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-primary-container to-tertiary-container opacity-90"></div>
    </div>

    <!-- Metric 2: Gross Revenue -->
    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="flex items-start justify-between">
            <div class="flex flex-col">
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Today's Gross Revenue</span>
                <div class="flex items-baseline gap-1 mt-2">
                    <span class="font-display-xl text-display-xl text-on-surface leading-none" id="statTodayRev">₹<?= number_format($metrics['today_revenue'] ?? 0, 2) ?></span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-on-primary-fixed group-hover:bg-primary-container group-hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-[24px]">payments</span>
            </div>
        </div>
        <div class="flex items-center gap-space-xs mt-space-md pt-space-xs">
            <span class="px-space-xs py-0.5 rounded bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold">100% Retained</span>
            <span class="font-body-sm text-body-sm text-on-surface-variant">0% gateway platform fee</span>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-primary to-primary-container opacity-90"></div>
    </div>

    <!-- Metric 3: Active Spool Queue -->
    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="flex items-start justify-between">
            <div class="flex flex-col">
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Active Print Queue</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="font-display-xl text-display-xl text-on-surface leading-none" id="statPendingOrders"><?= number_format($metrics['pending_orders'] ?? 0) ?></span>
                    <span class="font-label-md text-label-md text-on-surface-variant font-medium">Orders</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-on-surface-variant group-hover:bg-tertiary-fixed group-hover:text-on-tertiary-fixed transition-colors">
                <span class="material-symbols-outlined text-[24px]">hourglass_top</span>
            </div>
        </div>
        <div class="flex items-center gap-space-xs mt-space-md pt-space-xs">
            <span class="w-2 h-2 rounded-full bg-primary-container"></span>
            <span class="font-body-sm text-body-sm text-on-surface">Live auto-spool stream</span>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-tertiary-container to-secondary opacity-60"></div>
    </div>

    <!-- Metric 4: Hardware Spooler Status -->
    <div class="bg-inverse-surface text-inverse-on-surface rounded-xl p-space-lg shadow-md flex flex-col justify-between hover:shadow-lg transition-shadow relative overflow-hidden group">
        <div class="flex items-start justify-between">
            <div class="flex flex-col">
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary-fixed-dim">Hardware Spooler</span>
                <span class="font-headline-sm text-headline-sm font-bold text-on-secondary mt-2 truncate max-w-[180px]" title="<?= htmlspecialchars($defaultPrinter['printer_name'] ?? 'HP LaserJet Pro MFP M126nw') ?>">
                    <?= htmlspecialchars($defaultPrinter['printer_name'] ?? 'HP LaserJet') ?>
                </span>
                <span class="font-body-sm text-body-sm text-secondary-fixed-dim text-[11px] mt-0.5">Windows Connector Agent</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-on-secondary-fixed flex items-center justify-center text-primary-container shadow-inner">
                <span class="material-symbols-outlined text-[24px]">print</span>
            </div>
        </div>
        <div class="flex items-center justify-between mt-space-md pt-space-xs">
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-primary-container animate-pulse"></span>
                <span class="font-label-sm text-label-sm font-semibold text-inverse-on-surface">Ready &amp; Spooling</span>
            </div>
            <span class="font-label-sm text-label-sm text-secondary-fixed-dim">Online</span>
        </div>
        <div class="absolute top-0 right-0 left-0 h-0.5 bg-gradient-to-r from-primary-container to-tertiary-container"></div>
    </div>
</section>

<!-- Main Grid: Stream & Diagnostic Sidebar -->
<div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
    <!-- LEFT COLUMN: Live Print Order Stream (8 cols) -->
    <section class="xl:col-span-8 flex flex-col gap-space-md">
        <!-- Section Command Header -->
        <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
            <div class="flex flex-col">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary-container text-[24px]">stream</span>
                    <h2 class="font-headline-md text-headline-md text-on-surface">Live Print Order Stream</h2>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                    Real-time incoming customer jobs with automated Win32 spooling &amp; audio chimes.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Alarm Switch Toggle -->
                <button class="flex items-center gap-space-xs px-space-md py-2 rounded-xl bg-primary-container text-on-primary font-label-md text-label-md shadow-[inset_0_1px_0_rgba(255,255,255,0.25)] hover:bg-tertiary-container transition-all active:translate-y-px" id="toggleAudioBtn" type="button">
                    <span class="material-symbols-outlined text-[18px]" id="audioIcon">volume_up</span>
                    <span id="alarmToggleText">Sound Alarm: Active</span>
                </button>
                <div class="flex items-center gap-1 px-space-sm py-2 rounded-lg bg-surface-container text-on-surface-variant font-label-sm text-label-sm">
                    <span class="material-symbols-outlined text-[14px] animate-spin">refresh</span>
                    <span>3s</span>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="flex flex-wrap items-center justify-between gap-space-sm px-space-xs">
            <div class="flex flex-wrap items-center gap-space-xs" id="filterContainer">
                <button class="filter-btn active px-space-md py-1.5 rounded-lg bg-on-surface text-surface font-label-md text-label-md transition-all shadow-sm" data-filter="all" type="button" onclick="filterOrders('all', this)">
                    All Orders (<?= count($orders) ?>)
                </button>
                <button class="filter-btn px-space-md py-1.5 rounded-lg bg-surface-container-lowest text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-label-md text-label-md transition-all shadow-sm" data-filter="COMPLETED" type="button" onclick="filterOrders('COMPLETED', this)">
                    Completed
                </button>
                <button class="filter-btn px-space-md py-1.5 rounded-lg bg-surface-container-lowest text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-label-md text-label-md transition-all shadow-sm" data-filter="AWAITING_PAYMENT" type="button" onclick="filterOrders('AWAITING_PAYMENT', this)">
                    Awaiting Payment
                </button>
            </div>
            <div class="flex items-center gap-space-xs text-on-surface-variant font-label-sm text-label-sm">
                <span>Sort:</span>
                <span class="font-bold text-on-surface">Most Recent First</span>
            </div>
        </div>

        <!-- Live Job Cards Stream -->
        <div class="flex flex-col gap-space-md" id="ordersContainer">
            <?php if (empty($orders)): ?>
                <div class="p-12 text-center bg-surface-container-lowest rounded-xl border border-surface-container shadow-sm">
                    <span class="material-symbols-outlined text-surface-dim text-[48px] mb-3 block">inbox</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">No print orders received yet</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Customers can scan your counter poster QR code to start printing instantly.</p>
                    <a href="/dashboard/poster" target="_blank" class="inline-flex items-center gap-2 px-space-lg py-2.5 mt-4 bg-primary-container text-on-primary font-label-md text-label-md font-bold rounded-xl shadow-sm transition hover:bg-tertiary-container">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        <span>Download Shop Counter Poster</span>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $o): ?>
                    <article class="order-item bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-all relative overflow-hidden" data-status="<?= $o['status'] ?>" data-order-id="<?= $o['id'] ?>">
                        <?php if ($o['status'] === 'AWAITING_PAYMENT'): ?>
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary-container"></div>
                        <?php elseif ($o['status'] === 'COMPLETED'): ?>
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-500"></div>
                        <?php else: ?>
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary"></div>
                        <?php endif; ?>

                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
                            <div class="flex items-start gap-space-md min-w-0">
                                <div class="w-12 h-12 rounded-xl <?= $o['status'] === 'COMPLETED' ? 'bg-surface-container text-primary' : 'bg-primary-fixed text-on-primary-fixed' ?> flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[24px]">
                                        <?= $o['status'] === 'COMPLETED' ? 'task_alt' : ($o['status'] === 'AWAITING_PAYMENT' ? 'account_balance_wallet' : 'print') ?>
                                    </span>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <div class="flex flex-wrap items-center gap-space-xs">
                                        <span class="font-headline-sm text-headline-sm font-bold text-on-surface tracking-tight"><?= htmlspecialchars($o['order_number']) ?></span>
                                        <?php if ($o['status'] === 'COMPLETED'): ?>
                                            <span class="px-space-xs py-0.5 rounded-full bg-surface-container text-on-surface font-label-sm text-label-sm font-semibold flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Printed &amp; Spooled
                                            </span>
                                        <?php elseif ($o['status'] === 'PRINTING'): ?>
                                            <span class="px-space-xs py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold animate-pulse">
                                                Printing...
                                            </span>
                                        <?php elseif (($o['payment_method'] ?? '') === 'CASH_AT_COUNTER'): ?>
                                            <span class="px-space-xs py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 font-label-sm text-label-sm font-bold flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px]">payments</span> Cash at Counter
                                            </span>
                                        <?php else: ?>
                                            <span class="px-space-xs py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold">
                                                Awaiting Payment
                                            </span>
                                        <?php endif; ?>
                                        <span class="text-on-surface-variant font-body-sm text-body-sm">• <?= date('h:i A, d M', strtotime($o['created_at'])) ?></span>
                                    </div>

                                    <div class="flex items-center gap-space-xs mt-1.5 text-on-surface">
                                        <span class="material-symbols-outlined text-[18px] text-primary shrink-0">description</span>
                                        <span class="font-label-lg text-label-lg font-semibold truncate"><?= htmlspecialchars($o['file_name'] ?? 'document.pdf') ?></span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-space-sm mt-space-xs text-on-surface-variant font-body-sm text-body-sm">
                                        <span class="flex items-center gap-1 font-label-md text-label-md text-on-surface">
                                            <span class="material-symbols-outlined text-[14px]">content_copy</span> <?= $o['copies'] ?? 1 ?> Copy
                                        </span>
                                        <span>•</span>
                                        <span class="px-2 py-0.5 rounded bg-surface-container font-label-sm text-label-sm text-on-surface"><?= $o['color_mode'] ?? 'B&W' ?></span>
                                        <span>•</span>
                                        <span><?= $o['sides'] ?? 'SINGLE' ?></span>
                                        <?php if (!empty($o['transaction_reference'])): ?>
                                            <span>•</span>
                                            <span class="font-mono text-primary font-bold">UTR: <?= htmlspecialchars($o['transaction_reference']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Price and Action -->
                            <div class="flex lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-space-sm shrink-0 pt-space-xs lg:pt-0 border-t lg:border-t-0 border-surface-container">
                                <div class="flex flex-col lg:text-right">
                                    <div class="flex items-center gap-1">
                                        <span class="font-headline-md text-headline-md font-extrabold text-on-surface">₹<?= number_format($o['total_amount'], 2) ?></span>
                                        <?php if ($o['status'] === 'COMPLETED'): ?>
                                            <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">0% Platform Fee</span>
                                </div>

                                <div class="flex items-center gap-space-xs">
                                    <?php if ($o['status'] === 'AWAITING_PAYMENT'): ?>
                                        <button onclick="approveAndPrint('<?= $o['id'] ?>')" class="flex items-center gap-1.5 px-space-md py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-label-md text-label-md font-bold shadow-sm transition-all active:translate-y-px">
                                            <span class="material-symbols-outlined text-[16px]">print</span>
                                            <span><?= (($o['payment_method'] ?? '') === 'CASH_AT_COUNTER') ? 'Accept Cash &amp; Print' : 'Approve &amp; Print' ?></span>
                                        </button>
                                        <button onclick="markOrderCompleted('<?= $o['id'] ?>')" class="flex items-center gap-1 px-space-sm py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-sm text-label-sm transition-all">
                                            <span>Mark Done</span>
                                        </button>
                                    <?php elseif ($o['status'] === 'PRINTING'): ?>
                                        <button onclick="markOrderCompleted('<?= $o['id'] ?>')" class="flex items-center gap-1 px-space-md py-2 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary font-label-md text-label-md shadow-[inset_0_1px_0_rgba(255,255,255,0.25)] transition-all active:translate-y-px">
                                            <span class="material-symbols-outlined text-[16px]">check</span>
                                            <span>Mark Done</span>
                                        </button>
                                    <?php else: ?>
                                        <span class="px-3 py-1.5 rounded-lg bg-surface-container text-emerald-700 font-label-md text-label-md font-semibold flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[16px]">done_all</span> Done
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- RIGHT COLUMN: Diagnostics & Widgets (4 cols) -->
    <aside class="xl:col-span-4 flex flex-col gap-space-lg">
        <!-- Spooler Hardware Diagnostics -->
        <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col gap-space-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary-container text-[22px]">precision_manufacturing</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Spooler Hardware</h3>
                </div>
                <a href="/dashboard/printers" class="font-label-sm text-label-sm text-primary font-bold hover:underline">Manage</a>
            </div>

            <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-space-xs min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary-container animate-pulse shrink-0"></span>
                        <span class="font-label-md text-label-md text-on-surface truncate font-bold"><?= htmlspecialchars($defaultPrinter['printer_name'] ?? 'HP LaserJet Pro MFP M126nw') ?></span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded bg-surface-container-highest text-on-surface-variant font-label-sm text-label-sm shrink-0">Default</span>
                </div>
                <div class="flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm mt-1">
                    <span>Windows Spool Queue</span>
                    <span class="font-label-sm text-label-sm font-bold text-on-surface">Connected</span>
                </div>
                <div class="w-full h-2 rounded-full bg-surface-container-highest overflow-hidden">
                    <div class="h-full bg-primary-container rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <div class="pt-space-xs grid grid-cols-2 gap-space-xs">
                <div class="p-space-sm rounded-lg bg-surface-container flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Desktop Agent</span>
                    <span class="font-label-lg text-label-lg text-on-surface font-bold">EasyPrint.exe</span>
                </div>
                <div class="p-space-sm rounded-lg bg-surface-container flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Direct Settlement</span>
                    <span class="font-label-lg text-label-lg text-primary font-bold">0% Cut</span>
                </div>
            </div>
        </div>

        <!-- Quick Rate Matrix Widget -->
        <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col gap-space-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary-container text-[22px]">price_change</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Rate Matrix</h3>
                </div>
                <a class="font-label-sm text-label-sm text-primary font-bold hover:underline" href="/dashboard/rates">Edit Rates</a>
            </div>
            <div class="flex flex-col gap-space-xs text-sm">
                <div class="p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-on-surface">A4 • B&amp;W Single-Sided</span>
                        <div class="text-[11px] text-on-surface-variant">Standard plain paper</div>
                    </div>
                    <span class="font-bold text-on-surface">₹<?= \App\Models\ResourcePricingRule::getRate(\App\Core\Auth::shop()['id'] ?? '', 'A4', 'BLACK_AND_WHITE', 'SINGLE') ?></span>
                </div>
                <div class="p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-on-surface">A4 • Color Single-Sided</span>
                        <div class="text-[11px] text-on-surface-variant">Full color print</div>
                    </div>
                    <span class="font-bold text-on-surface">₹<?= \App\Models\ResourcePricingRule::getRate(\App\Core\Auth::shop()['id'] ?? '', 'A4', 'COLOR', 'SINGLE') ?></span>
                </div>
            </div>
        </div>

        <!-- Counter Poster CTA Card -->
        <div class="bg-gradient-to-br from-primary-fixed to-surface-container-low border border-primary-container/20 rounded-xl p-space-lg shadow-sm flex flex-col gap-space-sm">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">qr_code_2</span>
                <h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Shop Counter Poster</h4>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant">
                Print and display this A4 QR poster at your shop cash desk so walk-in customers can scan &amp; print self-service.
            </p>
            <a href="/dashboard/poster" target="_blank" class="mt-2 inline-flex items-center justify-center gap-2 px-space-md py-2.5 rounded-xl bg-primary-container text-on-primary font-label-md text-label-md font-bold shadow-sm hover:bg-tertiary-container transition">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Open Printable A4 Poster</span>
            </a>
        </div>
    </aside>
</div>

<!-- Real-time Polling & Audio Alarm Script -->
<script>
    let soundEnabled = true;
    let lastPollTime = '<?= date('Y-m-d H:i:s') ?>';
    const audio = document.getElementById('orderAlarmAudio');

    document.getElementById('toggleAudioBtn').addEventListener('click', () => {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('audioIcon');
        const text = document.getElementById('alarmToggleText');
        if (soundEnabled) {
            icon.innerText = 'volume_up';
            text.innerText = 'Sound Alarm: Active';
        } else {
            icon.innerText = 'volume_off';
            text.innerText = 'Sound Alarm: Muted';
        }
    });

    function filterOrders(filter, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('bg-on-surface', 'text-surface');
            b.classList.add('bg-surface-container-lowest', 'text-on-surface-variant');
        });
        btn.classList.add('bg-on-surface', 'text-surface');
        btn.classList.remove('bg-surface-container-lowest', 'text-on-surface-variant');

        document.querySelectorAll('.order-item').forEach(item => {
            if (filter === 'all' || item.dataset.status === filter) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    async function pollLiveOrders() {
        try {
            const res = await fetch(`/api/dashboard/orders/poll?since=${encodeURIComponent(lastPollTime)}`);
            const data = await res.json();

            if (data.new_orders && data.new_orders.length > 0) {
                if (soundEnabled && audio) {
                    audio.play().catch(e => console.log('Audio autoplay blocked'));
                }
                lastPollTime = data.server_time;
                location.reload();
            }

            if (data.metrics) {
                document.getElementById('statTodayPrints').innerText = data.metrics.today_prints.toLocaleString();
                document.getElementById('statTodayRev').innerText = '₹' + parseFloat(data.metrics.today_revenue).toFixed(2);
                document.getElementById('statPendingOrders').innerText = data.metrics.pending_orders.toLocaleString();
            }
        } catch (e) {
            console.error('Polling error:', e);
        }
    }

    setInterval(pollLiveOrders, 3000);

    async function approveAndPrint(orderId) {
        try {
            const res = await fetch('/api/dashboard/orders/status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: 'PRINTING' })
            });
            if (res.ok) {
                location.reload();
            } else {
                alert('Failed to approve order.');
            }
        } catch (e) {
            alert('Action failed. Check network.');
        }
    }

    async function markOrderCompleted(orderId) {
        if (!confirm('Mark order as physically completed?')) return;
        try {
            const res = await fetch('/api/dashboard/orders/status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: 'COMPLETED' })
            });
            if (res.ok) {
                location.reload();
            }
        } catch (e) {
            alert('Action failed');
        }
    }
</script>