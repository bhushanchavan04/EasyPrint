<!-- Page Header -->
<div class="mb-space-lg flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
    <div class="flex flex-col">
        <div class="flex items-center gap-space-xs">
            <span class="material-symbols-outlined text-primary-container text-[28px]">precision_manufacturing</span>
            <h1 class="font-headline-lg text-headline-lg text-on-surface font-extrabold tracking-tight">Machine Agents &amp; Hardware Printers</h1>
        </div>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
            Connect your counter PC with EasyPrint Connector to receive and silently spool print jobs with zero clicks.
        </p>
    </div>
    <div class="flex items-center gap-2">
        <form action="/dashboard/printers/generate-pin" method="POST">
            <?= $csrfField ?>
            <button type="submit" class="flex items-center gap-2 px-space-md py-2.5 rounded-xl bg-primary-container hover:bg-tertiary-container text-on-primary font-label-md text-label-md font-bold shadow-sm transition active:translate-y-px">
                <span class="material-symbols-outlined text-[18px]">key</span>
                <span>Pair New Windows PC</span>
            </button>
        </form>
        <a href="/downloads/EasyPrint.exe" class="flex items-center gap-2 px-space-md py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md font-semibold transition border border-surface-container-highest">
            <span class="material-symbols-outlined text-[18px] text-primary">download</span>
            <span>Download EasyPrint (.exe)</span>
        </a>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="mb-space-md p-space-md bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 font-label-md text-label-md flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>

<!-- Active Pairing Code Banner (if generated) -->
<?php if (!empty($pairingCode)): ?>
    <div class="mb-space-lg p-space-lg bg-gradient-to-r from-primary-fixed via-surface-container-low to-primary-fixed border-2 border-primary-container rounded-2xl shadow-md text-center space-y-3">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary-container text-on-primary rounded-full text-xs font-bold uppercase tracking-wider">
            <span class="material-symbols-outlined text-[16px]">lock_clock</span>
            Temporary Machine Pairing PIN (Valid for 15 minutes)
        </span>
        <div class="flex items-center justify-center gap-3 my-2">
            <div class="text-4xl sm:text-5xl font-mono font-black text-on-surface tracking-widest select-all bg-surface-container-lowest px-8 py-3 rounded-2xl border border-primary-container shadow-inner inline-block" id="pairingPinDisplay">
                <?= htmlspecialchars($pairingCode) ?>
            </div>
            <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($pairingCode) ?>'); this.innerText='Copied!'; setTimeout(()=>this.innerText='Copy PIN', 2000)" class="px-space-md py-3 bg-primary-container hover:bg-tertiary-container text-on-primary rounded-xl text-xs font-bold transition flex items-center gap-1 shadow">
                <span class="material-symbols-outlined text-[16px]">content_copy</span>
                <span>Copy PIN</span>
            </button>
        </div>
        <p class="text-xs text-on-surface-variant max-w-md mx-auto">
            Open <span class="font-bold text-on-surface">EasyPrint.exe</span> on your Windows counter PC and enter this plain 6-digit pin <span class="font-mono text-primary font-bold">(<?= htmlspecialchars($pairingCode) ?>)</span> to pair this shop immediately.
        </p>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
    <!-- Left Column: Detected Physical Printers (7 Cols) -->
    <div class="xl:col-span-7 flex flex-col gap-space-md">
        <div class="flex items-center justify-between px-space-xs">
            <div class="flex items-center gap-space-xs">
                <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Detected Physical Printers</h2>
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
                    <?= count($printers) ?> Connected
                </span>
            </div>
        </div>

        <?php if (empty($printers)): ?>
            <div class="p-8 text-center bg-surface-container-lowest rounded-2xl border border-surface-container shadow-sm">
                <span class="material-symbols-outlined text-surface-dim text-[48px] mb-2 block">print_disabled</span>
                <p class="font-headline-sm text-headline-sm text-on-surface">No physical printers registered yet</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 max-w-sm mx-auto">
                    Launch EasyPrint Windows Connector (.exe) on your counter PC. It will automatically detect your local printers and filter out virtual drivers.
                </p>
            </div>
        <?php else: ?>
            <div class="flex flex-col gap-space-md">
                <?php foreach ($printers as $p): ?>
                    <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm hover:shadow-md transition-shadow relative overflow-hidden flex flex-col gap-space-md border <?= $p['is_default'] ? 'border-primary-container' : 'border-surface-container' ?>">
                        <?php if ($p['is_default']): ?>
                            <div class="absolute top-0 left-0 right-0 h-1 bg-primary-container"></div>
                        <?php endif; ?>

                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-space-sm pt-space-xs">
                            <div class="flex items-start gap-space-md">
                                <div class="w-12 h-12 rounded-xl <?= $p['is_default'] ? 'bg-primary-container text-on-primary' : 'bg-surface-container text-on-surface-variant' ?> flex items-center justify-center shrink-0 shadow-sm">
                                    <span class="material-symbols-outlined text-[28px]">print</span>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap items-center gap-space-xs mb-1">
                                        <span class="font-headline-sm text-headline-sm text-on-surface font-bold"><?= htmlspecialchars($p['printer_name']) ?></span>
                                        <?php if ($p['is_default']): ?>
                                            <span class="px-2 py-0.5 rounded-full bg-primary-container text-on-primary font-label-sm text-label-sm">
                                                Default Spooler
                                            </span>
                                            <span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-primary-container"></span> Active Output
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                                        System Spool Name: <span class="font-mono text-on-surface font-semibold"><?= htmlspecialchars($p['system_spool_name']) ?></span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <?php if (!$p['is_default']): ?>
                                    <form action="/dashboard/printers/set-default" method="POST" class="inline">
                                        <?= $csrfField ?>
                                        <input type="hidden" name="printer_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="h-9 px-space-md rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-sm text-label-sm transition-all flex items-center gap-1 font-semibold">
                                            <span class="material-symbols-outlined text-[16px]">check</span>
                                            <span>Set Default</span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="h-9 px-space-md rounded-lg bg-emerald-50 text-emerald-700 font-label-sm text-label-sm flex items-center gap-1 font-bold">
                                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                        <span>Active Default</span>
                                    </span>
                                <?php endif; ?>

                                <form action="/dashboard/printers/disconnect" method="POST" onsubmit="return confirm('Disconnect this printer?');" class="inline">
                                    <?= $csrfField ?>
                                    <input type="hidden" name="printer_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="h-9 px-3 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-label-sm text-label-sm transition-all flex items-center gap-1 font-semibold border border-red-200" title="Disconnect Printer">
                                        <span class="material-symbols-outlined text-[16px]">link_off</span>
                                        <span>Disconnect</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-space-sm rounded-xl bg-surface-container-low text-xs text-on-surface-variant">
                            <div class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Driver Ready</span>
                            </div>
                            <span>Supports Raw Win32 Spooling</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Host Machine Agents (5 Cols) -->
    <div class="xl:col-span-5 flex flex-col gap-space-lg">
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm flex flex-col gap-space-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[24px]">desktop_windows</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-headline-sm text-headline-sm text-on-surface font-bold">Connected Windows PCs</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Machine Agent Instances</span>
                    </div>
                </div>
            </div>

            <?php if (empty($devices)): ?>
                <div class="p-6 text-center bg-surface-container-low rounded-xl text-on-surface-variant text-xs">
                    <p>No Windows PC paired yet. Click "Pair New Windows PC" above to connect your counter PC.</p>
                </div>
            <?php else: ?>
                <div class="flex flex-col divide-y divide-surface-container">
                    <?php foreach ($devices as $d): ?>
                        <div class="py-space-sm flex items-center justify-between first:pt-0 last:pb-0">
                            <div class="flex items-center gap-space-sm">
                                <span class="w-2.5 h-2.5 rounded-full <?= $d['is_online'] ? 'bg-primary-container animate-pulse' : 'bg-surface-dim' ?>"></span>
                                <div class="flex flex-col">
                                    <span class="font-label-md text-label-md text-on-surface font-bold"><?= htmlspecialchars($d['device_name']) ?></span>
                                    <span class="text-[11px] text-on-surface-variant font-mono">
                                        Last Ping: <?= $d['last_ping_at'] ? date('h:i:s A, d M', strtotime($d['last_ping_at'])) : 'Never' ?>
                                    </span>
                                </div>
                            </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-mono text-[11px]">
                                        ID: <?= substr($d['id'], 0, 8) ?>...
                                    </span>
                                    <form action="/dashboard/devices/disconnect" method="POST" onsubmit="return confirm('Disconnect this Windows PC?');" class="inline">
                                        <?= $csrfField ?>
                                        <input type="hidden" name="device_id" value="<?= $d['id'] ?>">
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 transition" title="Disconnect Windows PC">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="pt-space-xs border-t border-surface-container text-xs text-on-surface-variant">
                <span>The desktop connector daemon polls jobs and spools directly to hardware drivers without opening print dialogs.</span>
            </div>
        </div>

        <!-- EasyPrint Daemon Quick Guide -->
        <div class="p-space-lg bg-surface-container-low rounded-2xl flex flex-col gap-space-sm">
            <div class="flex items-center gap-2 text-on-surface font-bold text-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">help</span>
                <span>How to Setup Your Windows PC:</span>
            </div>
            <ol class="list-decimal list-inside text-xs text-on-surface-variant space-y-1.5 leading-relaxed">
                <li>Download <span class="font-semibold text-on-surface">EasyPrint.exe</span> using the button above.</li>
                <li>Click <span class="font-semibold text-on-surface">Pair New Windows PC</span> to generate a 6-digit PIN.</li>
                <li>Run EasyPrint.exe and enter the 6-digit PIN when prompted.</li>
                <li>Your printers will appear above automatically and start silent printing!</li>
            </ol>
        </div>
    </div>
</div>