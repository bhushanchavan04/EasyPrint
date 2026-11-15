<!-- Page Header -->
<div class="mb-space-lg flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
    <div class="flex flex-col">
        <div class="flex items-center gap-space-xs">
            <span class="material-symbols-outlined text-primary-container text-[28px]">price_change</span>
            <h1 class="font-headline-lg text-headline-lg text-on-surface font-extrabold tracking-tight">Rate Card Matrix</h1>
        </div>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
            Configure your per-page customer print rates. All customer UPI payments settle directly with 0% platform fee.
        </p>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="mb-space-md p-space-md bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 font-label-md text-label-md flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>

<form action="/dashboard/rates" method="POST">
    <?= $csrfField ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <!-- Left Column: Paper Pricing Matrix Cards (8 cols) -->
        <div class="lg:col-span-8 flex flex-col gap-space-lg">
            <!-- Paper Deck Card 1: Standard A4 Paper -->
            <div class="p-space-lg bg-surface-container-lowest rounded-xl shadow-sm flex flex-col gap-space-md relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-primary-container"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-space-sm">
                        <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-on-primary-fixed">
                            <span class="material-symbols-outlined text-[24px]">description</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-space-xs">
                                <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Standard A4 Paper</h2>
                                <span class="px-space-xs py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">90% Volume</span>
                            </div>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">Recommended Stock: 75 GSM - 80 GSM Bond Paper</span>
                        </div>
                    </div>
                </div>

                <!-- 4-Grid Price Inputs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md pt-space-xs">
                    <!-- B&W Single Sided -->
                    <div class="p-space-md bg-surface-container-low rounded-xl flex flex-col gap-space-xs">
                        <div class="flex justify-between items-center">
                            <span class="font-label-md text-label-md text-on-surface font-bold">B&amp;W (Single-Sided)</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant">1 sheet, 1 print</span>
                        </div>
                        <div class="relative flex items-center mt-1">
                            <span class="absolute left-3 font-headline-sm text-headline-sm text-on-surface font-semibold pointer-events-none">₹</span>
                            <input type="hidden" name="rates[0][paper_size]" value="A4">
                            <input type="hidden" name="rates[0][color_mode]" value="BLACK_AND_WHITE">
                            <input type="hidden" name="rates[0][sides]" value="SINGLE">
                            <input type="number" step="0.5" min="0" name="rates[0][price_per_page]" id="rateA4BwSingle"
                                   value="<?= \App\Models\ResourcePricingRule::getRate($shop['id'], 'A4', 'BLACK_AND_WHITE', 'SINGLE') ?>"
                                   class="w-full h-12 pl-8 pr-16 bg-surface-container-lowest rounded-lg font-headline-sm text-headline-sm text-on-surface font-bold focus:outline-none focus:ring-2 focus:ring-primary-container">
                            <span class="absolute right-3 font-label-sm text-label-sm text-on-surface-variant">/ page</span>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant text-[12px]">Standard xerox / laser print baseline.</span>
                    </div>

                    <!-- B&W Back-to-Back (Duplex) -->
                    <div class="p-space-md bg-surface-container-low rounded-xl flex flex-col gap-space-xs">
                        <div class="flex justify-between items-center">
                            <span class="font-label-md text-label-md text-on-surface font-bold">B&amp;W (Back-to-Back)</span>
                            <span class="px-1.5 py-0.5 rounded bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">Duplex</span>
                        </div>
                        <div class="relative flex items-center mt-1">
                            <span class="absolute left-3 font-headline-sm text-headline-sm text-on-surface font-semibold pointer-events-none">₹</span>
                            <input type="hidden" name="rates[1][paper_size]" value="A4">
                            <input type="hidden" name="rates[1][color_mode]" value="BLACK_AND_WHITE">
                            <input type="hidden" name="rates[1][sides]" value="DOUBLE">
                            <input type="number" step="0.5" min="0" name="rates[1][price_per_page]" id="rateA4BwDuplex"
                                   value="<?= \App\Models\ResourcePricingRule::getRate($shop['id'], 'A4', 'BLACK_AND_WHITE', 'DOUBLE') ?>"
                                   class="w-full h-12 pl-8 pr-16 bg-surface-container-lowest rounded-lg font-headline-sm text-headline-sm text-on-surface font-bold focus:outline-none focus:ring-2 focus:ring-primary-container">
                            <span class="absolute right-3 font-label-sm text-label-sm text-on-surface-variant">/ sheet</span>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant text-[12px]">2 sides printed on 1 physical sheet.</span>
                    </div>

                    <!-- Color Single Sided -->
                    <div class="p-space-md bg-surface-container-low rounded-xl flex flex-col gap-space-xs">
                        <div class="flex justify-between items-center">
                            <span class="font-label-md text-label-md text-on-surface font-bold">Color (Single-Sided)</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Inkjet / Laser</span>
                        </div>
                        <div class="relative flex items-center mt-1">
                            <span class="absolute left-3 font-headline-sm text-headline-sm text-on-surface font-semibold pointer-events-none">₹</span>
                            <input type="hidden" name="rates[2][paper_size]" value="A4">
                            <input type="hidden" name="rates[2][color_mode]" value="COLOR">
                            <input type="hidden" name="rates[2][sides]" value="SINGLE">
                            <input type="number" step="0.5" min="0" name="rates[2][price_per_page]" id="rateA4ColorSingle"
                                   value="<?= \App\Models\ResourcePricingRule::getRate($shop['id'], 'A4', 'COLOR', 'SINGLE') ?>"
                                   class="w-full h-12 pl-8 pr-16 bg-surface-container-lowest rounded-lg font-headline-sm text-headline-sm text-on-surface font-bold focus:outline-none focus:ring-2 focus:ring-primary-container">
                            <span class="absolute right-3 font-label-sm text-label-sm text-on-surface-variant">/ page</span>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant text-[12px]">Full spectrum color graphics / toner.</span>
                    </div>

                    <!-- Color Back-to-Back (Duplex) -->
                    <div class="p-space-md bg-surface-container-low rounded-xl flex flex-col gap-space-xs">
                        <div class="flex justify-between items-center">
                            <span class="font-label-md text-label-md text-on-surface font-bold">Color (Back-to-Back)</span>
                            <span class="px-1.5 py-0.5 rounded bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">Duplex</span>
                        </div>
                        <div class="relative flex items-center mt-1">
                            <span class="absolute left-3 font-headline-sm text-headline-sm text-on-surface font-semibold pointer-events-none">₹</span>
                            <input type="hidden" name="rates[3][paper_size]" value="A4">
                            <input type="hidden" name="rates[3][color_mode]" value="COLOR">
                            <input type="hidden" name="rates[3][sides]" value="DOUBLE">
                            <input type="number" step="0.5" min="0" name="rates[3][price_per_page]" id="rateA4ColorDuplex"
                                   value="<?= \App\Models\ResourcePricingRule::getRate($shop['id'], 'A4', 'COLOR', 'DOUBLE') ?>"
                                   class="w-full h-12 pl-8 pr-16 bg-surface-container-lowest rounded-lg font-headline-sm text-headline-sm text-on-surface font-bold focus:outline-none focus:ring-2 focus:ring-primary-container">
                            <span class="absolute right-3 font-label-sm text-label-sm text-on-surface-variant">/ sheet</span>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant text-[12px]">Project reports, presentations, brochures.</span>
                    </div>
                </div>
            </div>

            <!-- Paper Deck Card 2: Legal Size Paper -->
            <div class="p-space-lg bg-surface-container-lowest rounded-xl shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container flex items-center justify-center text-on-secondary-fixed">
                        <span class="material-symbols-outlined text-[24px]">gavel</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-space-xs">
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Legal Size Paper (8.5 × 14 in)</h2>
                            <span class="px-space-xs py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-sm text-label-sm">Courts &amp; Affidavits</span>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Recommended Stock: Green Ledger, Stamp Paper, Legal Bond</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <div class="p-space-md bg-surface-container-low rounded-xl flex flex-col gap-space-xs">
                        <span class="font-label-md text-label-md text-on-surface font-bold">Legal B&amp;W (Single-Sided)</span>
                        <div class="relative flex items-center mt-1">
                            <span class="absolute left-3 font-headline-sm text-headline-sm text-on-surface font-semibold pointer-events-none">₹</span>
                            <input type="hidden" name="rates[4][paper_size]" value="LEGAL">
                            <input type="hidden" name="rates[4][color_mode]" value="BLACK_AND_WHITE">
                            <input type="hidden" name="rates[4][sides]" value="SINGLE">
                            <input type="number" step="0.5" min="0" name="rates[4][price_per_page]"
                                   value="<?= \App\Models\ResourcePricingRule::getRate($shop['id'], 'LEGAL', 'BLACK_AND_WHITE', 'SINGLE') ?>"
                                   class="w-full h-12 pl-8 pr-16 bg-surface-container-lowest rounded-lg font-headline-sm text-headline-sm text-on-surface font-bold focus:outline-none focus:ring-2 focus:ring-primary-container">
                            <span class="absolute right-3 font-label-sm text-label-sm text-on-surface-variant">/ page</span>
                        </div>
                    </div>

                    <div class="p-space-md bg-surface-container-low rounded-xl flex flex-col gap-space-xs">
                        <span class="font-label-md text-label-md text-on-surface font-bold">Legal B&amp;W (Back-to-Back)</span>
                        <div class="relative flex items-center mt-1">
                            <span class="absolute left-3 font-headline-sm text-headline-sm text-on-surface font-semibold pointer-events-none">₹</span>
                            <input type="hidden" name="rates[5][paper_size]" value="LEGAL">
                            <input type="hidden" name="rates[5][color_mode]" value="BLACK_AND_WHITE">
                            <input type="hidden" name="rates[5][sides]" value="DOUBLE">
                            <input type="number" step="0.5" min="0" name="rates[5][price_per_page]"
                                   value="<?= \App\Models\ResourcePricingRule::getRate($shop['id'], 'LEGAL', 'BLACK_AND_WHITE', 'DOUBLE') ?>"
                                   class="w-full h-12 pl-8 pr-16 bg-surface-container-lowest rounded-lg font-headline-sm text-headline-sm text-on-surface font-bold focus:outline-none focus:ring-2 focus:ring-primary-container">
                            <span class="absolute right-3 font-label-sm text-label-sm text-on-surface-variant">/ sheet</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paper Deck Card 3: Large A3 Paper -->
            <div class="p-space-lg bg-surface-container-lowest rounded-xl shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed">
                        <span class="material-symbols-outlined text-[24px]">architecture</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-space-xs">
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Large A3 Paper (11.7 × 16.5 in)</h2>
                            <span class="px-space-xs py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-sm text-label-sm">Drawings &amp; Posters</span>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Architectural blueprints, charts, CAD schematics</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <div class="p-space-md bg-surface-container-low rounded-xl flex flex-col gap-space-xs">
                        <span class="font-label-md text-label-md text-on-surface font-bold">A3 B&amp;W (Single-Sided)</span>
                        <div class="relative flex items-center mt-1">
                            <span class="absolute left-3 font-headline-sm text-headline-sm text-on-surface font-semibold pointer-events-none">₹</span>
                            <input type="hidden" name="rates[6][paper_size]" value="A3">
                            <input type="hidden" name="rates[6][color_mode]" value="BLACK_AND_WHITE">
                            <input type="hidden" name="rates[6][sides]" value="SINGLE">
                            <input type="number" step="0.5" min="0" name="rates[6][price_per_page]"
                                   value="<?= \App\Models\ResourcePricingRule::getRate($shop['id'], 'A3', 'BLACK_AND_WHITE', 'SINGLE') ?>"
                                   class="w-full h-12 pl-8 pr-16 bg-surface-container-lowest rounded-lg font-headline-sm text-headline-sm text-on-surface font-bold focus:outline-none focus:ring-2 focus:ring-primary-container">
                            <span class="absolute right-3 font-label-sm text-label-sm text-on-surface-variant">/ page</span>
                        </div>
                    </div>

                    <div class="p-space-md bg-surface-container-low rounded-xl flex flex-col gap-space-xs">
                        <span class="font-label-md text-label-md text-on-surface font-bold">A3 Color (Single-Sided)</span>
                        <div class="relative flex items-center mt-1">
                            <span class="absolute left-3 font-headline-sm text-headline-sm text-on-surface font-semibold pointer-events-none">₹</span>
                            <input type="hidden" name="rates[7][paper_size]" value="A3">
                            <input type="hidden" name="rates[7][color_mode]" value="COLOR">
                            <input type="hidden" name="rates[7][sides]" value="SINGLE">
                            <input type="number" step="0.5" min="0" name="rates[7][price_per_page]"
                                   value="<?= \App\Models\ResourcePricingRule::getRate($shop['id'], 'A3', 'COLOR', 'SINGLE') ?>"
                                   class="w-full h-12 pl-8 pr-16 bg-surface-container-lowest rounded-lg font-headline-sm text-headline-sm text-on-surface font-bold focus:outline-none focus:ring-2 focus:ring-primary-container">
                            <span class="absolute right-3 font-label-sm text-label-sm text-on-surface-variant">/ page</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-space-xs">
                <button type="submit" class="flex items-center gap-2 px-space-xl py-3 rounded-xl bg-primary-container hover:bg-tertiary-container text-on-primary font-headline-sm text-headline-sm font-bold shadow-md transition-all active:translate-y-px">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    <span>Save Rate Card</span>
                </button>
            </div>
        </div>

        <!-- Right Column: Fare Simulator Card (4 cols) -->
        <div class="lg:col-span-4 flex flex-col gap-space-lg">
            <div class="p-space-lg bg-surface-container-lowest rounded-xl shadow-sm flex flex-col gap-space-md sticky top-24">
                <div class="flex items-center gap-space-xs pb-space-xs border-b border-surface-container">
                    <div class="w-8 h-8 rounded-lg bg-primary-container text-on-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">calculate</span>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Fare Simulator</h3>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Live Customer Calculation</span>
                    </div>
                </div>

                <div class="flex flex-col gap-space-sm pt-space-xs">
                    <!-- Quantity Slider -->
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between items-center">
                            <label class="font-label-sm text-label-sm text-on-surface font-bold">Page Count</label>
                            <span class="font-headline-sm text-headline-sm text-primary font-bold" id="simPageDisplay">10 Pages</span>
                        </div>
                        <input class="w-full h-2 bg-surface-container-high rounded-lg appearance-none cursor-pointer accent-primary-container" id="simPageCount" max="100" min="1" type="range" value="10" oninput="runFareSimulation()"/>
                    </div>

                    <!-- Color Mode -->
                    <div class="flex flex-col gap-1">
                        <span class="font-label-sm text-label-sm text-on-surface font-bold">Color Mode</span>
                        <div class="grid grid-cols-2 gap-space-xs">
                            <button type="button" class="sim-btn-color py-2 rounded-lg bg-on-surface text-surface font-label-sm text-label-sm font-semibold transition-all" id="btnSimBw" onclick="setSimColor('BLACK_AND_WHITE')">
                                Black &amp; White
                            </button>
                            <button type="button" class="sim-btn-color py-2 rounded-lg bg-surface-container-low text-on-surface font-label-sm text-label-sm font-semibold transition-all" id="btnSimColor" onclick="setSimColor('COLOR')">
                                Full Color
                            </button>
                        </div>
                    </div>

                    <!-- Side Layout -->
                    <div class="flex flex-col gap-1">
                        <span class="font-label-sm text-label-sm text-on-surface font-bold">Printing Sides</span>
                        <div class="grid grid-cols-2 gap-space-xs">
                            <button type="button" class="sim-btn-side py-2 rounded-lg bg-surface-container-low text-on-surface font-label-sm text-label-sm font-semibold transition-all" id="btnSimSingle" onclick="setSimSides('SINGLE')">
                                Single Sided
                            </button>
                            <button type="button" class="sim-btn-side py-2 rounded-lg bg-on-surface text-surface font-label-sm text-label-sm font-semibold transition-all" id="btnSimDouble" onclick="setSimSides('DOUBLE')">
                                Back-to-Back
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Simulation Result Card -->
                <div class="mt-space-sm p-space-md rounded-xl bg-on-surface text-surface flex flex-col gap-space-sm shadow-md">
                    <div class="flex justify-between items-center">
                        <span class="font-label-sm text-label-sm uppercase tracking-wider text-surface-dim font-bold">Customer Total</span>
                        <span class="px-space-xs py-0.5 rounded-full bg-primary-container text-on-primary font-label-sm text-label-sm font-bold">0% Cut</span>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <div class="flex items-baseline gap-1">
                            <span class="font-display-xl text-display-xl font-extrabold text-surface tracking-tight" id="simTotalAmount">₹15.00</span>
                        </div>
                        <span class="font-label-md text-label-md text-surface-dim" id="simUnitsDisplay">5 Sheets</span>
                    </div>
                    <div class="pt-space-xs border-t border-surface/20 flex items-center justify-between text-[11px] text-surface-dim">
                        <span>Direct to merchant UPI</span>
                        <span class="text-primary-container font-semibold">100% Retained</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    let simColor = 'BLACK_AND_WHITE';
    let simSides = 'DOUBLE';

    function setSimColor(mode) {
        simColor = mode;
        const btnBw = document.getElementById('btnSimBw');
        const btnColor = document.getElementById('btnSimColor');
        if (mode === 'BLACK_AND_WHITE') {
            btnBw.className = 'sim-btn-color py-2 rounded-lg bg-on-surface text-surface font-label-sm text-label-sm font-semibold transition-all';
            btnColor.className = 'sim-btn-color py-2 rounded-lg bg-surface-container-low text-on-surface font-label-sm text-label-sm font-semibold transition-all';
        } else {
            btnColor.className = 'sim-btn-color py-2 rounded-lg bg-on-surface text-surface font-label-sm text-label-sm font-semibold transition-all';
            btnBw.className = 'sim-btn-color py-2 rounded-lg bg-surface-container-low text-on-surface font-label-sm text-label-sm font-semibold transition-all';
        }
        runFareSimulation();
    }

    function setSimSides(sides) {
        simSides = sides;
        const btnSingle = document.getElementById('btnSimSingle');
        const btnDouble = document.getElementById('btnSimDouble');
        if (sides === 'SINGLE') {
            btnSingle.className = 'sim-btn-side py-2 rounded-lg bg-on-surface text-surface font-label-sm text-label-sm font-semibold transition-all';
            btnDouble.className = 'sim-btn-side py-2 rounded-lg bg-surface-container-low text-on-surface font-label-sm text-label-sm font-semibold transition-all';
        } else {
            btnDouble.className = 'sim-btn-side py-2 rounded-lg bg-on-surface text-surface font-label-sm text-label-sm font-semibold transition-all';
            btnSingle.className = 'sim-btn-side py-2 rounded-lg bg-surface-container-low text-on-surface font-label-sm text-label-sm font-semibold transition-all';
        }
        runFareSimulation();
    }

    function runFareSimulation() {
        const pages = parseInt(document.getElementById('simPageCount').value) || 1;
        document.getElementById('simPageDisplay').innerText = pages + ' Page' + (pages > 1 ? 's' : '');

        let rate = 2.0;
        if (simColor === 'BLACK_AND_WHITE') {
            rate = (simSides === 'DOUBLE')
                ? (parseFloat(document.getElementById('rateA4BwDuplex').value) || 3.0)
                : (parseFloat(document.getElementById('rateA4BwSingle').value) || 2.0);
        } else {
            rate = (simSides === 'DOUBLE')
                ? (parseFloat(document.getElementById('rateA4ColorDuplex').value) || 18.0)
                : (parseFloat(document.getElementById('rateA4ColorSingle').value) || 10.0);
        }

        const billedUnits = (simSides === 'DOUBLE') ? Math.ceil(pages / 2) : pages;
        const total = (billedUnits * rate).toFixed(2);

        document.getElementById('simTotalAmount').innerText = '₹' + total;
        document.getElementById('simUnitsDisplay').innerText = (simSides === 'DOUBLE') ? (billedUnits + ' Sheets') : (billedUnits + ' Pages');
    }

    // Run once on load
    runFareSimulation();
</script>