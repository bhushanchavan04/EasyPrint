<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-surface-card border border-surface-border rounded-3xl p-8 sm:p-10 shadow-card my-8">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex w-14 h-14 rounded-2xl bg-brand-500 text-white items-center justify-center mb-3 shadow-md">
                <span class="material-symbols-outlined text-[30px]">print</span>
            </a>
            <h1 class="text-2xl font-black tracking-tight text-charcoal">Merchant Console</h1>
            <p class="text-xs text-charcoal-muted mt-1">Sign in to control your EasyPrint automation kiosk</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-800 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-red-600 text-[18px]">error</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" class="space-y-4">
            <?= $csrfField ?>
            <div>
                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Email Address</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-3 text-charcoal-muted text-[18px]">mail</span>
                    <input type="email" name="email" required placeholder="owner@cybercafe.com"
                           class="w-full pl-11 pr-4 py-2.5 bg-surface-subtle border border-surface-border rounded-xl text-charcoal placeholder-charcoal-soft focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm font-medium">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-3 text-charcoal-muted text-[18px]">lock</span>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full pl-11 pr-4 py-2.5 bg-surface-subtle border border-surface-border rounded-xl text-charcoal placeholder-charcoal-soft focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm font-medium">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl transition duration-150 shadow-md text-sm flex items-center justify-center gap-2 mt-2">
                <span>Sign In to Dashboard</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-surface-border text-center text-xs text-charcoal-muted">
            Don't have a shop account yet? 
            <a href="/register" class="text-brand-600 hover:text-brand-700 font-bold ml-1">Register Shop</a>
        </div>
    </div>
</div>