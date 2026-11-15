<!-- Page Header -->
<div class="mb-space-lg flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
    <div class="flex flex-col">
        <div class="flex items-center gap-space-xs">
            <span class="material-symbols-outlined text-primary-container text-[28px]">storefront</span>
            <h1 class="font-headline-lg text-headline-lg text-on-surface font-extrabold tracking-tight">Shop Profile &amp; UPI Settlement</h1>
        </div>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
            Configure your receiving bank details for 0% commission direct customer UPI payments.
        </p>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="mb-space-md p-space-md bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 font-label-md text-label-md flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="mb-space-md p-space-md bg-red-50 border border-red-200 rounded-xl text-red-800 font-label-md text-label-md flex items-center gap-2">
        <span class="material-symbols-outlined text-red-600 text-[20px]">error</span>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
<?php endif; ?>

<form action="/dashboard/shop" method="POST" enctype="multipart/form-data">
    <?= $csrfField ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <!-- Main Form Column (8 Cols) -->
        <div class="lg:col-span-8 flex flex-col gap-space-lg">
            <!-- 1. Shop Identity Section -->
            <section class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center justify-between pb-space-xs border-b border-surface-container">
                    <div class="flex items-center gap-space-sm">
                        <div class="w-9 h-9 rounded-lg bg-primary-fixed flex items-center justify-center text-primary font-bold">
                            <span class="material-symbols-outlined text-[20px]">badge</span>
                        </div>
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Shop Brand &amp; Identity</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Displayed on customer mobile kiosks and A4 counter posters</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md pt-space-xs">
                    <!-- Shop Name -->
                    <div class="flex flex-col gap-1.5 md:col-span-2">
                        <label class="font-label-md text-label-md text-on-surface font-semibold">
                            Shop Business Name <span class="text-primary">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-on-surface-variant text-[20px]">store</span>
                            <input type="text" name="name" value="<?= htmlspecialchars($shop['name']) ?>" required
                                   class="w-full pl-11 pr-4 py-3 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md font-semibold focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all">
                        </div>
                    </div>

                    <!-- Shop Slug Display (Read-only) -->
                    <div class="flex flex-col gap-1.5 md:col-span-2">
                        <label class="font-label-md text-label-md text-on-surface font-semibold">
                            Kiosk URL Slug
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-sm text-primary font-bold bg-surface-container px-3 py-2 rounded-lg">/print/<?= htmlspecialchars($shop['slug']) ?></span>
                            <a href="/print/<?= htmlspecialchars($shop['slug']) ?>" target="_blank" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                                <span>Preview Customer Kiosk</span>
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                            </a>
                        </div>
                    </div>

                    <!-- Logo Upload -->
                    <div class="flex flex-col gap-2 md:col-span-2 pt-space-xs">
                        <label class="font-label-md text-label-md text-on-surface font-semibold">Shop Brand Logo (Optional)</label>
                        <div class="flex flex-col sm:flex-row items-center gap-space-md p-space-md rounded-xl bg-surface-container-low">
                            <?php if (!empty($shop['logo_url'])): ?>
                                <div class="w-16 h-16 rounded-2xl bg-surface-container-highest overflow-hidden shadow-inner shrink-0">
                                    <img src="<?= htmlspecialchars($shop['logo_url']) ?>" alt="Logo" class="w-full h-full object-cover">
                                </div>
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-2xl bg-primary-container text-on-primary flex items-center justify-center shrink-0 shadow-sm">
                                    <span class="material-symbols-outlined text-[28px]">store</span>
                                </div>
                            <?php endif; ?>
                            <div class="flex flex-col flex-1">
                                <span class="font-label-md text-label-md text-on-surface font-bold">Upload Custom Logo</span>
                                <span class="text-xs text-on-surface-variant">Recommended 512x512 PNG or JPG. Appears on your kiosk and A4 poster.</span>
                                <input type="file" name="logo" accept="image/*" class="mt-2 text-xs text-on-surface-variant file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-surface-container-highest file:text-on-surface hover:file:bg-surface-container cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2. Direct UPI Settlement Engine -->
            <section class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center justify-between pb-space-xs border-b border-surface-container">
                    <div class="flex items-center gap-space-sm">
                        <div class="w-9 h-9 rounded-lg bg-primary-fixed flex items-center justify-center text-primary font-bold">
                            <span class="material-symbols-outlined text-[20px]">payments</span>
                        </div>
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Personal Direct UPI Settlement</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Customer print charges deposit directly to your bank account with 0% platform fee</p>
                        </div>
                    </div>
                    <span class="flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-label-md text-label-md font-bold">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        <span>0% Commission</span>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md pt-space-xs">
                    <!-- Personal UPI ID -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-label-md text-label-md text-on-surface font-semibold">
                            Personal UPI ID (VPA) <span class="text-primary">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 font-label-md text-label-md text-primary font-bold">@</span>
                            <input type="text" name="upi_id" value="<?= htmlspecialchars($shop['upi_id']) ?>" required
                                   class="w-full pl-9 pr-4 py-3 rounded-lg bg-surface-container-low text-on-surface font-mono text-sm font-semibold focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all">
                        </div>
                        <span class="text-[11px] text-on-surface-variant">e.g. merchant@okhdfcbank, shop@okicici</span>
                    </div>

                    <!-- Receiver Name -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-label-md text-label-md text-on-surface font-semibold">
                            UPI Receiver Display Name <span class="text-primary">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-on-surface-variant text-[20px]">badge</span>
                            <input type="text" name="upi_name" value="<?= htmlspecialchars($shop['upi_name']) ?>" required
                                   class="w-full pl-11 pr-4 py-3 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md font-semibold focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all">
                        </div>
                        <span class="text-[11px] text-on-surface-variant">Account holder name as verified with your bank.</span>
                    </div>
                </div>

                <!-- UPI Apps Strip -->
                <div class="pt-space-xs">
                    <span class="text-xs text-on-surface-variant font-semibold block mb-2">Supported Customer UPI Apps:</span>
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface">PhonePe</span>
                        <span class="px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface">Google Pay</span>
                        <span class="px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface">Paytm</span>
                        <span class="px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface">BHIM UPI</span>
                        <span class="px-2.5 py-1 rounded-lg bg-surface-container-low text-on-surface">CRED Pay</span>
                    </div>
                </div>
            </section>

            <!-- 3. Spooling & Friction Controls -->
            <section class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center justify-between pb-space-xs border-b border-surface-container">
                    <div class="flex items-center gap-space-sm">
                        <div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[20px]">tune</span>
                        </div>
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Counter Friction &amp; Spooling Rules</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Hardware trigger logic when customer payments conclude</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-space-sm divide-y divide-surface-container">
                    <!-- Autoprint Toggle -->
                    <div class="flex items-start justify-between gap-space-md pt-space-xs pb-space-xs">
                        <div class="flex flex-col pr-space-md">
                            <span class="font-label-lg text-label-lg text-on-surface font-bold flex items-center gap-2">
                                Automated Silent Printing (Autoprint)
                                <span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold">Recommended</span>
                            </span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                                Instantly route paid orders directly to the desktop printer spooler without requiring you to click "Approve" manually.
                            </span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                            <input type="checkbox" name="autoprint" value="1" <?= !empty($shop['autoprint']) ? 'checked' : '' ?> class="sr-only peer">
                            <div class="w-12 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-container shadow-inner"></div>
                        </label>
                    </div>

                    <!-- Accept Cash Toggle -->
                    <div class="flex items-start justify-between gap-space-md pt-space-md pb-space-xs">
                        <div class="flex flex-col pr-space-md">
                            <span class="font-label-lg text-label-lg text-on-surface font-bold">Accept Cash at Counter</span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                                Allow customers to select "Pay Cash at Counter" instead of UPI. Cash jobs await operator verification before spooling.
                            </span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                            <input type="checkbox" name="accept_cash" value="1" <?= !empty($shop['accept_cash']) ? 'checked' : '' ?> class="sr-only peer">
                            <div class="w-12 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-container shadow-inner"></div>
                        </label>
                    </div>
                </div>
            </section>

            <!-- Submit Button Bar -->
            <div class="flex justify-end pt-space-xs">
                <button type="submit" class="flex items-center gap-2 px-space-xl py-3 rounded-xl bg-primary-container hover:bg-tertiary-container text-on-primary font-headline-sm text-headline-sm font-bold shadow-md transition-all active:translate-y-px">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    <span>Save Shop Settings</span>
                </button>
            </div>
        </div>

        <!-- Right Companion Column: Summary Info (4 Cols) -->
        <div class="lg:col-span-4 flex flex-col gap-space-lg">
            <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center gap-2 font-headline-sm text-headline-sm text-on-surface font-bold">
                    <span class="material-symbols-outlined text-primary text-[22px]">verified_user</span>
                    <span>Direct Bank Settlement</span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                    Unlike food delivery platforms or aggregator apps, EasyPrint takes <strong class="text-on-surface">zero commission (0%)</strong> on customer print charges.
                </p>
                <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-1 text-xs">
                    <span class="font-bold text-on-surface">Payment Rail:</span>
                    <span class="text-on-surface-variant font-mono">UPI Standard Intent &amp; Dynamic QR</span>
                    <span class="font-bold text-on-surface mt-2">Retention:</span>
                    <span class="text-emerald-700 font-bold">100% Retained in Your Account</span>
                </div>
            </div>

            <div class="bg-surface-container-low rounded-2xl p-space-lg flex flex-col gap-space-sm">
                <div class="flex items-center gap-2 text-on-surface font-bold text-sm">
                    <span class="material-symbols-outlined text-primary text-[20px]">qr_code_2</span>
                    <span>Counter A4 Poster</span>
                </div>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    After updating your UPI details, make sure to generate your A4 counter poster so your customers can scan &amp; pay correctly.
                </p>
                <a href="/dashboard/poster" target="_blank" class="mt-2 inline-flex items-center justify-center gap-2 px-space-md py-2.5 rounded-xl bg-primary-container text-on-primary font-label-md text-label-md font-bold shadow-sm hover:bg-tertiary-container transition">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Print Updated Counter Poster</span>
                </a>
            </div>
        </div>
    </div>
</form>