<!-- Audio Notification for Print Completion -->
<audio id="completedChime" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3" type="audio/mpeg">
</audio>

<div id="kioskContainer" class="space-y-6 max-w-4xl mx-auto">
    <!-- Step Progress Indicator -->
    <div class="bg-surface-card border border-surface-border rounded-2xl p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2 text-xs font-bold text-charcoal" id="stepIndicator1">
            <span class="w-7 h-7 rounded-full bg-brand-500 text-white flex items-center justify-center text-xs shadow-sm">1</span>
            <span class="hidden sm:inline">Upload</span>
        </div>
        <div class="h-0.5 flex-1 mx-2 sm:mx-4 bg-brand-500" id="stepLine1"></div>
        
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400" id="stepIndicator2">
            <span class="w-7 h-7 rounded-full bg-surface-subtle text-slate-500 border border-surface-border flex items-center justify-center text-xs">2</span>
            <span class="hidden sm:inline">Settings</span>
        </div>
        <div class="h-0.5 flex-1 mx-2 sm:mx-4 bg-surface-border" id="stepLine2"></div>

        <div class="flex items-center gap-2 text-xs font-bold text-slate-400" id="stepIndicator3">
            <span class="w-7 h-7 rounded-full bg-surface-subtle text-slate-500 border border-surface-border flex items-center justify-center text-xs">3</span>
            <span class="hidden sm:inline">Pay</span>
        </div>
        <div class="h-0.5 flex-1 mx-2 sm:mx-4 bg-surface-border" id="stepLine3"></div>

        <div class="flex items-center gap-2 text-xs font-bold text-slate-400" id="stepIndicator4">
            <span class="w-7 h-7 rounded-full bg-surface-subtle text-slate-500 border border-surface-border flex items-center justify-center text-xs">4</span>
            <span class="hidden sm:inline">Print</span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STEP 1: UPLOAD STEP                        -->
    <!-- ========================================== -->
    <div id="stepUpload" class="step-transition bg-surface-card border border-surface-border rounded-2xl p-6 sm:p-8 shadow-card">
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-brand-50 border border-brand-200 text-brand-600 mx-auto flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-[32px]">cloud_upload</span>
            </div>
            <h2 class="text-xl font-bold text-charcoal">Upload Document to Print</h2>
            <p class="text-xs text-charcoal-muted mt-1">Select PDF, DOCX, or Image (PNG, JPG, JPEG)</p>
        </div>

        <div id="dropzone" class="border-2 border-dashed border-brand-500/40 hover:border-brand-500 rounded-2xl p-8 sm:p-10 text-center cursor-pointer bg-brand-50/30 hover:bg-brand-50/60 transition group">
            <input type="file" id="fileInput" class="hidden" accept=".pdf,.docx,.png,.jpg,.jpeg">
            <span class="material-symbols-outlined text-brand-500 text-[42px] group-hover:scale-110 transition duration-200 mb-2 block">upload_file</span>
            <p class="text-sm font-bold text-charcoal">Tap to Browse or Drag File Here</p>
            <p class="text-xs text-charcoal-muted mt-1">Files are automatically purged after 24 hours</p>
        </div>

        <!-- Uploading Spinner (Hidden) -->
        <div id="uploadSpinner" class="hidden text-center py-8">
            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-brand-500 border-t-transparent mb-3"></div>
            <p class="text-sm font-semibold text-brand-700">Reading &amp; calculating pages...</p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STEP 2: PRINT SETTINGS & PREVIEW (REAL DIALOG) -->
    <!-- ========================================== -->
    <div id="stepSettings" class="hidden step-transition space-y-4">
        <!-- Responsive Grid: Preview on Left, Real Printer Setting on Right -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            <!-- Left Column: Document Info & Live Preview -->
            <div class="lg:col-span-5 space-y-4">
                <!-- Document Info Card -->
                <div class="flex items-center justify-between p-3.5 bg-surface-card rounded-2xl border border-surface-border shadow-sm">
                    <div class="flex items-center gap-3 truncate">
                        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold shrink-0 border border-brand-200">
                            <span class="material-symbols-outlined text-[22px]" id="docIcon">description</span>
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-charcoal truncate" id="docFileName">document.pdf</p>
                            <p class="text-[11px] text-charcoal-muted">
                                <span id="docPagesCount" class="font-bold text-brand-700">1</span> Page(s) • <span id="docFileSize">0 KB</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" id="btnChangeDoc" class="text-xs font-semibold text-charcoal-muted hover:text-red-600 p-1.5 flex items-center gap-1 rounded-lg hover:bg-surface-subtle transition" title="Remove & re-upload">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>Change</span>
                    </button>
                </div>

                <!-- Live Document Preview Container -->
                <div class="border border-surface-border rounded-2xl overflow-hidden bg-surface-card p-3 shadow-card">
                    <div class="flex items-center justify-between px-1 py-1 text-[11px] text-charcoal-muted border-b border-surface-border mb-2.5">
                        <span class="flex items-center gap-1.5 font-bold text-charcoal">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">visibility</span>
                            Document Preview
                        </span>
                        <span id="previewPageIndicator" class="text-brand-600 font-mono text-[11px] font-bold">Ready</span>
                    </div>
                    <div id="previewFrameContainer" class="w-full h-80 sm:h-96 bg-zinc-100 rounded-xl flex items-center justify-center overflow-hidden border border-surface-border/60 relative">
                        <iframe id="pdfPreviewFrame" src="" class="w-full h-full border-0 hidden"></iframe>
                        <img id="imagePreview" src="" class="max-h-full max-w-full object-contain hidden" alt="Document Preview">
                        <div id="docxPreviewPlaceholder" class="text-center p-6 hidden">
                            <span class="material-symbols-outlined text-blue-600 text-[48px] mb-2 block">description</span>
                            <p class="text-xs text-charcoal font-bold">Word Document Ready for Spooling</p>
                            <p class="text-[11px] text-charcoal-muted mt-1">Direct Windows spooling supported</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Print Settings (Themed to match project) -->
            <div class="lg:col-span-7 bg-surface-card border border-surface-border rounded-2xl p-5 sm:p-6 shadow-card space-y-4">

                <!-- Print Header with Dynamic Sheet Counter -->
                <div class="flex items-center justify-between pb-3 border-b border-surface-border">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200 text-brand-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">print</span>
                        </div>
                        <h3 class="text-base font-bold text-charcoal">Print Settings</h3>
                    </div>
                    <span id="sheetCountHeader" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-subtle border border-surface-border text-charcoal-muted text-xs font-semibold">
                        <span class="material-symbols-outlined text-[13px]">layers</span>
                        1 sheet of paper
                    </span>
                </div>

                <!-- Destination Row -->
                <div class="grid grid-cols-12 gap-3 items-center">
                    <label class="col-span-4 text-xs font-semibold text-charcoal-muted">Destination</label>
                    <div class="col-span-8">
                        <div class="relative">
                            <select id="printerDestination" class="w-full bg-surface-subtle border border-surface-border rounded-xl px-3 py-2.5 text-xs text-charcoal font-medium appearance-none focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 pr-8 cursor-pointer transition">
                                <option value="<?= htmlspecialchars($defaultPrinter['printer_name'] ?? 'Canon LBP2900') ?>"><?= htmlspecialchars($defaultPrinter['printer_name'] ?? 'Canon LBP2900') ?></option>
                            </select>
                            <span class="material-symbols-outlined text-[18px] text-charcoal-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>

                <!-- Pages Row -->
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-4 text-xs font-semibold text-charcoal-muted pt-2.5">Pages</label>
                    <div class="col-span-8 space-y-2">
                        <div class="relative">
                            <select id="pagesSelect" class="w-full bg-surface-subtle border border-surface-border rounded-xl px-3 py-2.5 text-xs text-charcoal font-medium appearance-none focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 pr-8 cursor-pointer transition">
                                <option value="all">All pages</option>
                                <option value="custom">Custom pages</option>
                                <option value="odd">Odd pages only</option>
                                <option value="even">Even pages only</option>
                            </select>
                            <span class="material-symbols-outlined text-[18px] text-charcoal-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                        </div>

                        <!-- Custom Page Range Input -->
                        <div id="customRangeWrapper" class="hidden">
                            <div class="relative">
                                <span class="material-symbols-outlined text-[16px] text-charcoal-soft absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">tag</span>
                                <input type="text" id="pageRangeInput"
                                    placeholder="e.g. 3  or  1-5, 8, 11-13"
                                    class="w-full pl-9 pr-3 py-2.5 bg-white border border-surface-border rounded-xl text-xs font-mono text-charcoal placeholder-charcoal-soft focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                            </div>
                            <div class="flex items-center justify-between mt-1.5 px-0.5">
                                <span id="pageRangeSummary" class="text-[11px] text-brand-600 font-semibold">Enter page numbers to print</span>
                                <span id="pageRangeDocLimit" class="text-[11px] text-charcoal-muted">Total: 1 page</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Copies Row -->
                <div class="grid grid-cols-12 gap-3 items-center">
                    <label class="col-span-4 text-xs font-semibold text-charcoal-muted">Copies</label>
                    <div class="col-span-8">
                        <div class="flex items-center bg-surface-subtle border border-surface-border rounded-xl overflow-hidden w-36">
                            <button type="button" id="btnMinusCopy" class="w-10 h-9 hover:bg-brand-50 text-charcoal font-bold flex items-center justify-center transition text-base">-</button>
                            <input type="number" id="copiesInput" min="1" max="99" value="1" class="flex-1 text-center bg-transparent border-0 text-charcoal font-bold text-sm focus:outline-none">
                            <button type="button" id="btnPlusCopy" class="w-10 h-9 hover:bg-brand-50 text-charcoal font-bold flex items-center justify-center transition text-base">+</button>
                        </div>
                    </div>
                </div>

                <!-- Layout Row -->
                <div class="grid grid-cols-12 gap-3 items-center">
                    <label class="col-span-4 text-xs font-semibold text-charcoal-muted">Layout</label>
                    <div class="col-span-8">
                        <div class="relative">
                            <select id="layoutSelect" class="w-full bg-surface-subtle border border-surface-border rounded-xl px-3 py-2.5 text-xs text-charcoal font-medium appearance-none focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 pr-8 cursor-pointer transition">
                                <option value="PORTRAIT">Portrait</option>
                                <option value="LANDSCAPE">Landscape</option>
                            </select>
                            <span class="material-symbols-outlined text-[18px] text-charcoal-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>

                <!-- Color Row -->
                <div class="grid grid-cols-12 gap-3 items-center">
                    <label class="col-span-4 text-xs font-semibold text-charcoal-muted">Color</label>
                    <div class="col-span-8">
                        <div class="relative">
                            <select id="colorSelect" class="w-full bg-surface-subtle border border-surface-border rounded-xl px-3 py-2.5 text-xs text-charcoal font-medium appearance-none focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 pr-8 cursor-pointer transition">
                                <option value="BLACK_AND_WHITE">Black and white</option>
                                <option value="COLOR">Color</option>
                            </select>
                            <span class="material-symbols-outlined text-[18px] text-charcoal-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>

                <!-- Two-Sided Row -->
                <div class="grid grid-cols-12 gap-3 items-center">
                    <label class="col-span-4 text-xs font-semibold text-charcoal-muted">Two-sided</label>
                    <div class="col-span-8">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer select-none group">
                            <div class="relative">
                                <input type="checkbox" id="twoSidedCheckbox" class="sr-only peer">
                                <div class="w-9 h-5 bg-surface-border peer-checked:bg-brand-500 rounded-full transition peer-focus:ring-2 peer-focus:ring-brand-500/30"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-xs text-charcoal font-medium group-hover:text-brand-600 transition">Print on both sides</span>
                        </label>
                    </div>
                </div>

                <!-- More Settings Accordion -->
                <div class="border-t border-surface-border pt-3">
                    <button type="button" id="btnMoreSettings" class="flex items-center justify-between w-full py-1 text-xs font-semibold text-charcoal-muted hover:text-charcoal transition group">
                        <span>More settings</span>
                        <span class="material-symbols-outlined text-[18px] transition-transform duration-200" id="moreSettingsIcon">keyboard_arrow_down</span>
                    </button>

                    <div id="moreSettingsContent" class="hidden space-y-4 pt-3">
                        <!-- Paper Size -->
                        <div class="grid grid-cols-12 gap-3 items-center">
                            <label class="col-span-4 text-xs font-semibold text-charcoal-muted">Paper size</label>
                            <div class="col-span-8">
                                <div class="relative">
                                    <select id="paperSizeSelect" class="w-full bg-surface-subtle border border-surface-border rounded-xl px-3 py-2.5 text-xs text-charcoal font-medium appearance-none focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 pr-8 cursor-pointer transition">
                                        <option value="A4">A4</option>
                                        <option value="LETTER">Letter</option>
                                        <option value="LEGAL">Legal</option>
                                        <option value="A3">A3</option>
                                    </select>
                                    <span class="material-symbols-outlined text-[18px] text-charcoal-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <!-- Pages per sheet -->
                        <div class="grid grid-cols-12 gap-3 items-center">
                            <label class="col-span-4 text-xs font-semibold text-charcoal-muted">Pages per sheet</label>
                            <div class="col-span-8">
                                <div class="relative">
                                    <select id="pagesPerSheetSelect" class="w-full bg-surface-subtle border border-surface-border rounded-xl px-3 py-2.5 text-xs text-charcoal font-medium appearance-none focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 pr-8 cursor-pointer transition">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="4">4</option>
                                    </select>
                                    <span class="material-symbols-outlined text-[18px] text-charcoal-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <!-- Margins -->
                        <div class="grid grid-cols-12 gap-3 items-center">
                            <label class="col-span-4 text-xs font-semibold text-charcoal-muted">Margins</label>
                            <div class="col-span-8">
                                <div class="relative">
                                    <select id="marginsSelect" class="w-full bg-surface-subtle border border-surface-border rounded-xl px-3 py-2.5 text-xs text-charcoal font-medium appearance-none focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 pr-8 cursor-pointer transition">
                                        <option value="DEFAULT">Default</option>
                                        <option value="NONE">None</option>
                                        <option value="MINIMUM">Minimum</option>
                                    </select>
                                    <span class="material-symbols-outlined text-[18px] text-charcoal-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price & Sheet Summary Callout -->
                <div class="p-4 bg-brand-50 border border-brand-200 rounded-2xl flex items-center justify-between mt-1">
                    <div>
                        <span class="text-[11px] text-brand-700 block font-semibold uppercase tracking-wide">Selected Pages</span>
                        <span id="summaryTotalPages" class="text-sm font-bold text-charcoal mt-0.5 block">1 Page(s) • 1 Sheet(s) (Single-Sided)</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] text-brand-600 block font-bold">Total Price (0% Fee)</span>
                        <span id="summaryTotalPrice" class="text-2xl font-black text-brand-600">₹2.00</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" id="btnCancelPrint" class="px-5 py-2.5 rounded-xl border border-surface-border text-xs font-bold text-charcoal-muted hover:bg-surface-subtle hover:text-charcoal transition">
                        Cancel
                    </button>
                    <button type="button" id="btnProceedToPay" class="px-7 py-2.5 bg-brand-500 hover:bg-brand-600 text-white text-xs font-black rounded-xl transition shadow-sm flex items-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">print</span>
                        <span>Print</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STEP 3: DIRECT 0% COMMISSION UPI PAYMENT   -->
    <!-- ========================================== -->
    <div id="stepPayment" class="hidden step-transition bg-surface-card border border-surface-border rounded-2xl p-5 sm:p-6 shadow-card space-y-4">
        <div class="text-center">
            <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-full text-xs font-bold">
                <span class="material-symbols-outlined text-[14px]">bolt</span>
                0% Platform Commission • Direct to Shopkeeper
            </span>
            <h2 class="text-base font-bold text-charcoal mt-2.5">
                Pay <span id="payAmountDisplay" class="text-brand-600 font-extrabold text-2xl">₹2.00</span>
            </h2>
            <p class="text-xs text-charcoal-muted">Order: <span id="payOrderNumber" class="font-mono text-charcoal font-bold">MP-XXXX</span></p>
        </div>

        <!-- Dynamic QR Code Card -->
        <div class="bg-white p-4 rounded-2xl max-w-[220px] mx-auto shadow-md border border-surface-border text-center">
            <img id="upiQrImg" src="" alt="Scan UPI QR" class="w-full h-auto mx-auto rounded-lg">
            <p class="text-[11px] text-charcoal-muted font-semibold mt-2">Scan with any UPI App</p>
        </div>

        <!-- 1-Click Mobile App Buttons -->
        <div class="space-y-2">
            <p class="text-[11px] text-center text-charcoal-muted font-medium">Or pay directly in your installed UPI app:</p>
            <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
                <a id="btnPhonePe" href="#" class="p-2.5 bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-800 rounded-xl flex items-center justify-center gap-1.5 transition">
                    <span>PhonePe</span>
                </a>
                <a id="btnGPay" href="#" class="p-2.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-800 rounded-xl flex items-center justify-center gap-1.5 transition">
                    <span>Google Pay</span>
                </a>
                <a id="btnPaytm" href="#" class="p-2.5 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 text-cyan-800 rounded-xl flex items-center justify-center gap-1.5 transition">
                    <span>Paytm</span>
                </a>
                <a id="btnBhim" href="#" class="p-2.5 bg-orange-50 hover:bg-orange-100 border border-orange-200 text-orange-800 rounded-xl flex items-center justify-center gap-1.5 transition">
                    <span>BHIM / Other</span>
                </a>
            </div>
        </div>

        <!-- Payment Verification Form -->
        <div class="bg-surface-subtle border border-surface-border rounded-xl p-4 space-y-3">
            <label class="block text-xs font-bold text-charcoal">Payment Reference (UTR / UPI Ref Number)</label>
            <input type="text" id="utrInput" placeholder="e.g. 412345678901 or last 4 digits"
                   class="w-full px-3 py-2.5 bg-white border border-surface-border rounded-xl text-charcoal text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
            
            <button type="button" id="btnConfirmUpiPayment" class="w-full py-3 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                <span>I Have Paid – Start Printing Now</span>
            </button>

            <?php if (!empty($shop['accept_cash'])): ?>
                <div class="pt-2 border-t border-surface-border text-center">
                    <button type="button" id="btnPayCashAtCounter" class="text-xs text-amber-700 hover:underline font-bold flex items-center justify-center gap-1 mx-auto">
                        <span class="material-symbols-outlined text-[14px]">payments</span>
                        <span>Pay Cash at Counter Instead</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STEP 4: TRACKER & NOTIFICATION             -->
    <!-- ========================================== -->
    <div id="stepTracker" class="hidden step-transition bg-surface-card border border-surface-border rounded-2xl p-6 sm:p-8 shadow-card text-center space-y-5">
        <!-- In Progress State -->
        <div id="printingState">
            <div id="trackerStatusIconBox" class="w-16 h-16 rounded-2xl bg-brand-50 border border-brand-200 text-brand-600 mx-auto flex items-center justify-center mb-4">
                <span id="trackerStatusIcon" class="material-symbols-outlined text-[36px] animate-pulse">print</span>
            </div>
            <h2 id="trackerStatusTitle" class="text-xl font-black text-charcoal">Printing Your Copy Now...</h2>
            <p id="trackerStatusDesc" class="text-xs text-charcoal-muted mt-1">Order: <span id="trackerOrderNumber" class="font-mono text-brand-600 font-bold">MP-XXXX</span></p>

            <div class="my-5 p-4 bg-surface-subtle rounded-xl border border-surface-border text-xs text-left space-y-2">
                <div class="flex justify-between text-charcoal-muted">
                    <span>Target Hardware:</span>
                    <span class="text-charcoal font-semibold"><?= htmlspecialchars($defaultPrinter['printer_name'] ?? 'HP LaserJet Pro MFP M126nw') ?></span>
                </div>
                <div class="flex justify-between text-charcoal-muted">
                    <span>Spool Status:</span>
                    <span id="trackerSpoolStatus" class="text-brand-600 font-mono font-bold animate-pulse">QUEUED_IN_SPOOLER</span>
                </div>
            </div>

            <p class="text-[11px] text-charcoal-soft">Checking printer status in real-time (2.5s polling)...</p>
        </div>

        <!-- Completed Ready State -->
        <div id="completedState" class="hidden">
            <div class="w-20 h-20 rounded-full bg-emerald-100 border-2 border-emerald-500 text-emerald-600 mx-auto flex items-center justify-center mb-4 shadow-lg animate-pulse">
                <span class="material-symbols-outlined text-[44px]">check</span>
            </div>
            <h2 class="text-2xl font-black text-emerald-700">Your Print is Ready!</h2>
            <p class="text-sm font-semibold text-charcoal mt-1">Collect your copy from the shop counter.</p>

            <div class="my-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 space-y-1">
                <p>Thank you for using <strong class="text-charcoal"><?= htmlspecialchars($shop['name']) ?></strong> automated kiosk!</p>
                <p class="text-[10px] text-emerald-700">Digital temporary files scheduled for automatic 24-hr deletion.</p>
            </div>

            <button type="button" onclick="location.reload()" class="w-full py-3 bg-charcoal hover:bg-zinc-800 text-white font-bold rounded-xl text-xs transition">
                Print Another Document
            </button>
        </div>
    </div>
</div>

<!-- Embedded Rate Card Data for Pure Client Calculation -->
<script>
    window.SHOP_SLUG = <?= json_encode($shop['slug']) ?>;
    window.SHOP_RATES = <?= json_encode($rates) ?>;
</script>
<script src="/js/customer-portal.js"></script>