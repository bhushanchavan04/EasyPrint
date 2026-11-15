<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-surface-card border border-surface-border rounded-3xl p-8 sm:p-10 shadow-card my-8">
        <div class="text-center mb-6">
            <a href="/" class="inline-flex w-14 h-14 rounded-2xl bg-brand-500 text-white items-center justify-center mb-3 shadow-md">
                <span class="material-symbols-outlined text-[30px]">storefront</span>
            </a>
            <h1 class="text-2xl font-black tracking-tight text-charcoal">Register Shop</h1>
            <p class="text-xs text-charcoal-muted mt-1">Set up automated self-service cloud printing in 2 minutes</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-800 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-red-600 text-[18px]">error</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST" class="space-y-4">
            <?= $csrfField ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Owner Full Name</label>
                    <input type="text" name="name" required placeholder="Ramesh Sharma"
                           class="w-full px-3.5 py-2.5 bg-surface-subtle border border-surface-border rounded-xl text-charcoal placeholder-charcoal-soft focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Phone (WhatsApp)</label>
                    <input type="text" name="phone" placeholder="+91 98765 43210"
                           class="w-full px-3.5 py-2.5 bg-surface-subtle border border-surface-border rounded-xl text-charcoal placeholder-charcoal-soft focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Email (Login ID)</label>
                    <input type="email" name="email" required placeholder="ramesh@cybercafe.com"
                           class="w-full px-3.5 py-2.5 bg-surface-subtle border border-surface-border rounded-xl text-charcoal placeholder-charcoal-soft focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 bg-surface-subtle border border-surface-border rounded-xl text-charcoal placeholder-charcoal-soft focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>
            </div>

            <div class="border-t border-surface-border pt-4 mt-2">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold text-brand-600 uppercase tracking-wider">Shop &amp; Direct UPI Details</h3>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[10px] font-bold">0% Commission</span>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Shop Business Name</label>
                        <input type="text" name="shop_name" required placeholder="Pixaprint Xerox &amp; Cyber Cafe"
                               class="w-full px-3.5 py-2.5 bg-surface-subtle border border-surface-border rounded-xl text-charcoal placeholder-charcoal-soft focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Personal UPI ID (VPA)</label>
                            <input type="text" name="upi_id" required placeholder="ramesh@okhdfcbank"
                                   class="w-full px-3.5 py-2.5 bg-surface-subtle border border-surface-border rounded-xl text-charcoal placeholder-charcoal-soft focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm font-mono">
                            <span class="text-[11px] text-charcoal-muted mt-0.5 block">Customer payments hit your bank directly.</span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">UPI Receiver Name</label>
                            <input type="text" name="upi_name" placeholder="PixaPrint Solutions"
                                   class="w-full px-3.5 py-2.5 bg-surface-subtle border border-surface-border rounded-xl text-charcoal placeholder-charcoal-soft focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full mt-4 py-3.5 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl transition duration-150 shadow-md text-sm flex items-center justify-center gap-2">
                <span>Create Shop &amp; Continue</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-surface-border text-center text-xs text-charcoal-muted">
            Already have an account? 
            <a href="/login" class="text-brand-600 hover:text-brand-700 font-bold ml-1">Sign In</a>
        </div>
    </div>
</div>