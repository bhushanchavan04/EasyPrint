<!-- Razorpay Checkout JS SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<!-- Page Header -->
<div class="mb-space-lg flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
    <div class="flex flex-col">
        <div class="flex items-center gap-space-xs">
            <span class="material-symbols-outlined text-primary-container text-[28px]">credit_card</span>
            <h1 class="font-headline-lg text-headline-lg text-on-surface font-extrabold tracking-tight">Billing &amp; Subscription Paywall</h1>
        </div>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
            Choose your EasyPrint platform plan. 100% of customer print revenue deposits directly to your bank account with 0% platform fee.
        </p>
    </div>
</div>

<?php if (!empty($isPaywall)): ?>
    <div class="mb-space-lg p-space-lg bg-red-50 border-2 border-red-500 rounded-2xl shadow-md flex items-start gap-space-md">
        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
            <span class="material-symbols-outlined text-[24px]">lock</span>
        </div>
        <div>
            <h3 class="font-headline-sm text-headline-sm text-red-900 font-bold">Merchant Hard Paywall Active</h3>
            <p class="font-body-md text-body-md text-red-700 mt-1 leading-relaxed">
                <?= htmlspecialchars($error ?? 'An active subscription is required to unlock your live kiosk and desktop auto-print spooler.') ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="mb-space-md p-space-md bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 font-label-md text-label-md flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
<?php endif; ?>

<!-- Current Subscription Status Card -->
<div class="mb-space-lg bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-surface-container flex flex-col md:flex-row md:items-center justify-between gap-space-md">
    <div class="flex flex-col">
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Current Platform License</span>
        <div class="flex items-center gap-space-sm mt-1">
            <h2 class="font-headline-lg text-headline-lg text-on-surface font-black">
                <?= htmlspecialchars($subscription['plan_name'] ?? 'No Active Subscription') ?>
            </h2>
            <?php if ($subscription): ?>
                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-label-sm text-label-sm font-bold flex items-center gap-1.5 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    ACTIVE
                </span>
            <?php else: ?>
                <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 font-label-sm text-label-sm font-bold">
                    EXPIRED / LOCKED
                </span>
            <?php endif; ?>
        </div>
        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
            <?php if ($subscription): ?>
                Valid until: <span class="font-bold text-on-surface"><?= date('F d, Y - h:i A', strtotime($subscription['ends_at'])) ?></span>
            <?php else: ?>
                Select a subscription plan below to activate customer self-service kiosk &amp; auto-print desktop connector.
            <?php endif; ?>
        </p>
    </div>

    <div class="flex flex-col md:text-right">
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Customer Print Commission:</span>
        <span class="font-headline-sm text-headline-sm text-emerald-700 font-bold mt-0.5 flex items-center md:justify-end gap-1">
            <span class="material-symbols-outlined text-[20px]">verified</span>
            0% Cut (100% Direct to Your Bank UPI)
        </span>
    </div>
</div>

<!-- Pricing Tier Grid -->
<div class="mb-space-lg">
    <h3 class="font-headline-md text-headline-md text-on-surface font-bold mb-space-md">Available EasyPrint SaaS Plans</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-space-lg items-stretch">
        <?php foreach ($plans as $p): ?>
            <?php 
                $isCurrent = ($subscription && $subscription['plan_id'] === $p['id']); 
                $isPopular = ($p['price'] == 499);
            ?>
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm p-space-lg flex flex-col justify-between relative border <?= $isCurrent ? 'border-2 border-emerald-500' : ($isPopular ? 'border-2 border-primary-container shadow-md' : 'border border-surface-container') ?> <?= $isPopular ? 'md:-translate-y-2' : '' ?>">
                <?php if ($isPopular): ?>
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-primary-container text-on-primary px-4 py-1 rounded-full font-label-sm text-[11px] font-bold tracking-wide uppercase shadow-sm">
                        Most Popular for Cyber Cafes
                    </div>
                <?php endif; ?>

                <div class="flex flex-col">
                    <div class="flex items-center justify-between pt-1">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider"><?= $p['duration_days'] == 365 ? 'Annual Pass' : 'Monthly Node' ?></span>
                        <?php if ($isCurrent): ?>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 font-label-sm text-label-sm uppercase font-bold">
                                Current Active
                            </span>
                        <?php endif; ?>
                    </div>

                    <h3 class="font-headline-md text-headline-md text-on-surface mt-space-sm font-bold"><?= htmlspecialchars($p['name']) ?></h3>
                    
                    <div class="mt-space-md mb-space-md flex items-baseline gap-1">
                        <span class="font-display-xl text-display-xl text-on-surface font-extrabold tracking-tight">₹<?= number_format($p['price'], 0) ?></span>
                        <span class="font-body-md text-body-md text-on-surface-variant font-medium">/ <?= $p['duration_days'] == 365 ? 'year' : 'month' ?></span>
                    </div>

                    <div class="flex flex-col gap-space-sm pt-space-xs border-t border-surface-container">
                        <div class="flex items-start gap-2 text-xs">
                            <span class="material-symbols-outlined text-[18px] text-primary shrink-0 mt-0.5">check_circle</span>
                            <span><strong><?= $p['max_printers'] ?></strong> Connected Windows PC &amp; Printer Agent</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs">
                            <span class="material-symbols-outlined text-[18px] text-primary shrink-0 mt-0.5">check_circle</span>
                            <span><strong><?= $p['max_monthly_prints'] == 0 ? 'Unlimited' : number_format($p['max_monthly_prints']) ?></strong> Monthly Spool Prints</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs">
                            <span class="material-symbols-outlined text-[18px] text-primary shrink-0 mt-0.5">check_circle</span>
                            <span class="font-semibold text-emerald-700">0% Commission on Customer UPI payments</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs">
                            <span class="material-symbols-outlined text-[18px] text-primary shrink-0 mt-0.5">check_circle</span>
                            <span>24-Hour Zero-Knowledge GDPR document shredder</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs">
                            <span class="material-symbols-outlined text-[18px] text-primary shrink-0 mt-0.5">check_circle</span>
                            <span>Printable A4 Shop Counter QR Poster Generator</span>
                        </div>
                    </div>
                </div>

                <div class="mt-space-xl pt-space-sm">
                    <button type="button" onclick="initiateSubscriptionCheckout('<?= $p['id'] ?>')"
                            class="w-full h-12 rounded-xl <?= $isCurrent ? 'bg-surface-container text-on-surface-variant cursor-default' : ($isPopular ? 'bg-primary-container text-on-primary hover:bg-tertiary-container shadow-sm' : 'bg-on-surface text-surface hover:bg-surface-dim') ?> font-label-lg text-label-lg font-bold flex items-center justify-center gap-2 transition active:translate-y-px">
                        <span><?= $isCurrent ? 'Active Plan' : 'Subscribe via Razorpay' ?></span>
                        <?php if (!$isCurrent): ?>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        <?php endif; ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Billing History Invoices Table -->
<?php if (!empty($history)): ?>
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm p-space-lg flex flex-col gap-space-md border border-surface-container">
        <div class="flex items-center justify-between">
            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Billing Invoices &amp; Payment History</h3>
            <span class="px-2.5 py-1 rounded bg-surface-container text-on-surface font-label-sm text-label-sm font-semibold">
                <?= count($history) ?> Recorded Invoices
            </span>
        </div>

        <div class="flex flex-col divide-y divide-surface-container text-xs">
            <?php foreach ($history as $h): ?>
                <div class="py-space-sm flex items-center justify-between first:pt-0 last:pb-0">
                    <div class="flex flex-col">
                        <span class="font-bold text-on-surface"><?= htmlspecialchars($h['plan_name']) ?></span>
                        <span class="text-[11px] text-on-surface-variant font-mono">
                            ID: <?= htmlspecialchars($h['gateway_order_id'] ?? $h['id']) ?> • <?= date('M d, Y - h:i A', strtotime($h['created_at'])) ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-space-md text-right">
                        <div>
                            <span class="font-headline-sm text-headline-sm text-on-surface font-extrabold">₹<?= number_format($h['price'], 2) ?></span>
                            <span class="block text-[10px] font-mono text-emerald-700 font-bold"><?= htmlspecialchars($h['status']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Razorpay Integration Script -->
<script>
    async function initiateSubscriptionCheckout(planId) {
        try {
            const res = await fetch('/api/billing/create-order', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ plan_id: planId })
            });

            const order = await res.json();
            if (!res.ok || !order.success) {
                alert(order.error || 'Failed to initiate checkout.');
                return;
            }

            // Simulated sandbox mode
            if (order.key_id.includes('YourKeyIdHere') || order.order_id.startsWith('order_sim_')) {
                const proceed = confirm(`Simulated Razorpay Sandbox Checkout:\n\nPlan: ${order.plan_name}\nAmount: ₹${order.plan_price}\n\nClick OK to simulate instant payment verification.`);
                if (proceed) {
                    await verifySubscriptionPayment(planId, order.order_id, 'pay_sim_' + Date.now(), 'sig_simulated_valid');
                }
                return;
            }

            const options = {
                key: order.key_id,
                amount: order.amount,
                currency: order.currency,
                name: "EasyPrint SaaS",
                description: "Merchant Subscription - " + order.plan_name,
                order_id: order.order_id,
                handler: async function (response) {
                    await verifySubscriptionPayment(
                        planId,
                        response.razorpay_order_id,
                        response.razorpay_payment_id,
                        response.razorpay_signature
                    );
                },
                prefill: {
                    name: order.shop_name
                },
                theme: {
                    color: "#f97316"
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();
        } catch (e) {
            alert('Payment checkout error: ' + e.message);
        }
    }

    async function verifySubscriptionPayment(planId, orderId, paymentId, signature) {
        try {
            const res = await fetch('/api/billing/verify-payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    plan_id: planId,
                    razorpay_order_id: orderId,
                    razorpay_payment_id: paymentId,
                    razorpay_signature: signature
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                window.location.href = data.redirect || '/dashboard';
            } else {
                alert(data.error || 'Payment verification failed.');
            }
        } catch (e) {
            alert('Verification connection error.');
        }
    }
</script>