// EasyPrint Customer 4-Step Portal Controller
document.addEventListener('DOMContentLoaded', () => {
    const shopSlug = window.SHOP_SLUG;
    const rates = window.SHOP_RATES || [];

    // State
    let currentDoc = null;
    let copies = 1;
    let pageSelectionMode = 'all';
    let selectedPagesList = [];
    let layout = 'PORTRAIT';
    let colorMode = 'BLACK_AND_WHITE';
    let sides = 'SINGLE';
    let paperSize = 'A4';
    let currentOrder = null;
    let pollInterval = null;

    // Page Range Parsing Algorithm
    function parsePageRange(rangeStr, totalPages) {
        totalPages = Math.max(1, parseInt(totalPages, 10) || 1);
        const str = (rangeStr || '').trim().toLowerCase();

        if (!str || str === 'all') {
            const all = [];
            for (let i = 1; i <= totalPages; i++) all.push(i);
            return all;
        }

        if (str === 'odd' || str === 'odd pages only') {
            const odd = [];
            for (let i = 1; i <= totalPages; i += 2) odd.push(i);
            return odd;
        }

        if (str === 'even' || str === 'even pages only') {
            const even = [];
            for (let i = 2; i <= totalPages; i += 2) even.push(i);
            return even;
        }

        const set = new Set();
        const parts = str.split(',');
        for (let part of parts) {
            part = part.trim();
            if (!part) continue;
            if (part.includes('-')) {
                const bounds = part.split('-');
                const start = parseInt(bounds[0].trim(), 10);
                const end = parseInt(bounds[1].trim(), 10);
                if (!isNaN(start) && !isNaN(end) && start > 0 && end >= start) {
                    const s = Math.max(1, start);
                    const e = Math.min(totalPages, end);
                    for (let p = s; p <= e; p++) {
                        set.add(p);
                    }
                }
            } else {
                const p = parseInt(part, 10);
                if (!isNaN(p) && p >= 1 && p <= totalPages) {
                    set.add(p);
                }
            }
        }

        return Array.from(set).sort((a, b) => a - b);
    }

    function formatPageRanges(pages) {
        if (!pages || pages.length === 0) return '';
        if (pages.length <= 4) return pages.join(', ');

        const ranges = [];
        let start = pages[0];
        let end = start;

        for (let i = 1; i < pages.length; i++) {
            if (pages[i] === end + 1) {
                end = pages[i];
            } else {
                ranges.push(start === end ? `${start}` : `${start}-${end}`);
                start = pages[i];
                end = start;
            }
        }
        ranges.push(start === end ? `${start}` : `${start}-${end}`);
        return ranges.join(', ');
    }

    // DOM Elements
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const uploadSpinner = document.getElementById('uploadSpinner');

    // Steps
    const stepUpload = document.getElementById('stepUpload');
    const stepSettings = document.getElementById('stepSettings');
    const stepPayment = document.getElementById('stepPayment');
    const stepTracker = document.getElementById('stepTracker');

    // Indicators
    const ind1 = document.getElementById('stepIndicator1');
    const ind2 = document.getElementById('stepIndicator2');
    const ind3 = document.getElementById('stepIndicator3');
    const ind4 = document.getElementById('stepIndicator4');

    // File Drop & Select
    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-indigo-400', 'bg-slate-800/80');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-indigo-400', 'bg-slate-800/80');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-400', 'bg-slate-800/80');
        if (e.dataTransfer.files.length > 0) {
            handleFileUpload(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFileUpload(fileInput.files[0]);
        }
    });

    // File Upload Handler
    async function handleFileUpload(file) {
        dropzone.classList.add('hidden');
        uploadSpinner.classList.remove('hidden');

        const formData = new FormData();
        formData.append('document', file);

        try {
            const res = await fetch(`/api/print/${shopSlug}/upload`, {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                alert(data.error || 'Failed to parse file.');
                dropzone.classList.remove('hidden');
                uploadSpinner.classList.add('hidden');
                return;
            }

            currentDoc = data;
            showSettingsStep();
        } catch (err) {
            alert('Upload connection error. Please try again.');
            dropzone.classList.remove('hidden');
            uploadSpinner.classList.add('hidden');
        }
    }

    // Step 2: Show Settings & Live Preview
    function showSettingsStep() {
        stepUpload.classList.add('hidden');
        stepSettings.classList.remove('hidden');
        setIndicatorActive(ind2);

        const totalPages = currentDoc.total_pages || 1;
        document.getElementById('docFileName').innerText = currentDoc.file_name;
        document.getElementById('docPagesCount').innerText = totalPages;
        document.getElementById('docFileSize').innerText = currentDoc.file_size_formatted;
        document.getElementById('pageRangeDocLimit').innerText = `Total: ${totalPages} page(s)`;

        // Reset form state
        copies = 1;
        layout = 'PORTRAIT';
        colorMode = 'BLACK_AND_WHITE';
        sides = 'SINGLE';
        paperSize = 'A4';
        const copiesInput = document.getElementById('copiesInput');
        if (copiesInput) copiesInput.value = 1;
        const pagesSelect = document.getElementById('pagesSelect');
        if (pagesSelect) pagesSelect.value = 'all';
        const customRangeWrapper = document.getElementById('customRangeWrapper');
        if (customRangeWrapper) customRangeWrapper.classList.add('hidden');
        const pageRangeInput = document.getElementById('pageRangeInput');
        if (pageRangeInput) pageRangeInput.value = '';
        const layoutSelect = document.getElementById('layoutSelect');
        if (layoutSelect) layoutSelect.value = 'PORTRAIT';
        const colorSelect = document.getElementById('colorSelect');
        if (colorSelect) colorSelect.value = 'BLACK_AND_WHITE';
        const twoSidedCheckbox = document.getElementById('twoSidedCheckbox');
        if (twoSidedCheckbox) twoSidedCheckbox.checked = false;
        const paperSizeSelect = document.getElementById('paperSizeSelect');
        if (paperSizeSelect) paperSizeSelect.value = 'A4';

        // Setup preview
        updatePreview(currentDoc.preview_url, 1, totalPages);

        selectedPagesList = parsePageRange('all', totalPages);
        recalculatePrice();
    }

    // ── Live Document Preview Engine ─────────────────────────────────────────
    let slicePreviewTimer = null;
    let lastSlicedRange = '';

    function updatePreview(baseUrl, pageIndex, totalPages) {
        const pdfFrame   = document.getElementById('pdfPreviewFrame');
        const imgPreview = document.getElementById('imagePreview');
        const docxHolder = document.getElementById('docxPreviewPlaceholder');
        const indicator  = document.getElementById('previewPageIndicator');

        if (pdfFrame)   pdfFrame.classList.add('hidden');
        if (imgPreview) imgPreview.classList.add('hidden');
        if (docxHolder) docxHolder.classList.add('hidden');

        if (!currentDoc) return;

        if (currentDoc.is_pdf && pdfFrame) {
            pdfFrame.src = baseUrl + '#toolbar=0&navpanes=0';
            pdfFrame.classList.remove('hidden');
            if (indicator) indicator.innerText = totalPages > 1 ? `All ${totalPages} Page(s)` : 'Ready';
        } else if (currentDoc.is_image && imgPreview) {
            imgPreview.src = baseUrl;
            imgPreview.classList.remove('hidden');
            if (indicator) indicator.innerText = 'Ready';
        } else if (docxHolder) {
            docxHolder.classList.remove('hidden');
            if (indicator) indicator.innerText = 'DOCX';
        }
    }

    function refreshPreviewForSelection() {
        if (!currentDoc || !currentDoc.is_pdf) return;
        const totalPages = currentDoc.total_pages || 1;
        const pdfFrame   = document.getElementById('pdfPreviewFrame');
        const indicator  = document.getElementById('previewPageIndicator');

        if (slicePreviewTimer) clearTimeout(slicePreviewTimer);

        // If 'all' pages or empty selection, restore full document preview
        if (selectedPagesList.length === 0 || selectedPagesList.length === totalPages) {
            if (lastSlicedRange !== 'all') {
                lastSlicedRange = 'all';
                if (pdfFrame) {
                    pdfFrame.src = currentDoc.preview_url + '#toolbar=0&navpanes=0';
                    pdfFrame.classList.remove('hidden');
                }
            }
            if (indicator) indicator.innerText = `All ${totalPages} Page(s)`;
            return;
        }

        const formatted = formatPageRanges(selectedPagesList);
        if (formatted === lastSlicedRange) return;

        if (indicator) indicator.innerText = 'Updating preview...';

        // Debounce server-side preview slicing by 300ms
        slicePreviewTimer = setTimeout(async () => {
            try {
                const res = await fetch(`/api/print/${shopSlug}/preview-slice`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        document_id: currentDoc.document_id,
                        page_range: formatted
                    })
                });

                const data = await res.json();
                if (data.success && data.is_sliced && data.preview_url) {
                    lastSlicedRange = formatted;
                    if (pdfFrame) {
                        pdfFrame.src = data.preview_url + '#toolbar=0&navpanes=0';
                        pdfFrame.classList.remove('hidden');
                    }
                    if (indicator) {
                        indicator.innerText = `Page(s) ${formatted} (${data.page_count} of ${totalPages})`;
                    }
                }
            } catch (err) {
                console.error('Failed to update live preview:', err);
                if (indicator) indicator.innerText = `Selected: ${formatted}`;
            }
        }, 300);
    }

    // Reset document
    function resetToUpload() {
        currentDoc = null;
        fileInput.value = '';
        stepSettings.classList.add('hidden');
        stepUpload.classList.remove('hidden');
        dropzone.classList.remove('hidden');
        uploadSpinner.classList.add('hidden');
        previewPageList = [];
        previewCurrentPage = 1;
        setIndicatorReset(ind2);
    }

    const btnChangeDoc = document.getElementById('btnChangeDoc');
    if (btnChangeDoc) btnChangeDoc.addEventListener('click', resetToUpload);

    const btnCancelPrint = document.getElementById('btnCancelPrint');
    if (btnCancelPrint) btnCancelPrint.addEventListener('click', resetToUpload);

    // Setting Controls
    const pagesSelect        = document.getElementById('pagesSelect');
    const customRangeWrapper = document.getElementById('customRangeWrapper');
    const pageRangeInput     = document.getElementById('pageRangeInput');
    const pageRangeSummary   = document.getElementById('pageRangeSummary');

    if (pagesSelect) {
        pagesSelect.addEventListener('change', () => {
            const val = pagesSelect.value;
            if (val === 'custom') {
                customRangeWrapper.classList.remove('hidden');
                pageRangeInput.focus();
            } else {
                customRangeWrapper.classList.add('hidden');
            }
            recalculatePrice();
        });
    }

    if (pageRangeInput) {
        pageRangeInput.addEventListener('input', () => recalculatePrice());
    }

    // Copies Controls
    const copiesInput = document.getElementById('copiesInput');
    const btnMinusCopy = document.getElementById('btnMinusCopy');
    const btnPlusCopy = document.getElementById('btnPlusCopy');

    if (btnMinusCopy) {
        btnMinusCopy.addEventListener('click', () => {
            if (copies > 1) {
                copies--;
                if (copiesInput) copiesInput.value = copies;
                recalculatePrice();
            }
        });
    }

    if (btnPlusCopy) {
        btnPlusCopy.addEventListener('click', () => {
            copies++;
            if (copiesInput) copiesInput.value = copies;
            recalculatePrice();
        });
    }

    if (copiesInput) {
        copiesInput.addEventListener('input', () => {
            let val = parseInt(copiesInput.value, 10);
            if (isNaN(val) || val < 1) val = 1;
            if (val > 99) val = 99;
            copies = val;
            recalculatePrice();
        });
    }

    // Layout Select
    const layoutSelect = document.getElementById('layoutSelect');
    if (layoutSelect) {
        layoutSelect.addEventListener('change', () => {
            layout = layoutSelect.value;
        });
    }

    // Color Select
    const colorSelect = document.getElementById('colorSelect');
    if (colorSelect) {
        colorSelect.addEventListener('change', () => {
            colorMode = colorSelect.value;
            recalculatePrice();
        });
    }

    // Two-sided Checkbox
    const twoSidedCheckbox = document.getElementById('twoSidedCheckbox');
    if (twoSidedCheckbox) {
        twoSidedCheckbox.addEventListener('change', () => {
            sides = twoSidedCheckbox.checked ? 'DOUBLE' : 'SINGLE';
            recalculatePrice();
        });
    }

    // Paper Size Select
    const paperSizeSelect = document.getElementById('paperSizeSelect');
    if (paperSizeSelect) {
        paperSizeSelect.addEventListener('change', () => {
            paperSize = paperSizeSelect.value;
            recalculatePrice();
        });
    }

    // More Settings Accordion Toggle
    const btnMoreSettings = document.getElementById('btnMoreSettings');
    const moreSettingsContent = document.getElementById('moreSettingsContent');
    const moreSettingsIcon = document.getElementById('moreSettingsIcon');

    if (btnMoreSettings && moreSettingsContent) {
        btnMoreSettings.addEventListener('click', () => {
            const isHidden = moreSettingsContent.classList.contains('hidden');
            if (isHidden) {
                moreSettingsContent.classList.remove('hidden');
                if (moreSettingsIcon) moreSettingsIcon.style.transform = 'rotate(180deg)';
            } else {
                moreSettingsContent.classList.add('hidden');
                if (moreSettingsIcon) moreSettingsIcon.style.transform = 'rotate(0deg)';
            }
        });
    }

    // Dynamic Price & Sheet Calculation
    function recalculatePrice() {
        if (!currentDoc) return 0;

        const totalDocPages = Math.max(1, currentDoc.total_pages || 1);
        const selMode = pagesSelect ? pagesSelect.value : 'all';

        if (selMode === 'custom') {
            const raw = pageRangeInput ? pageRangeInput.value.trim() : '';
            if (!raw) {
                selectedPagesList = [];
                if (pageRangeSummary) {
                    pageRangeSummary.innerText = `e.g. 1-${Math.min(3, totalDocPages)}, ${totalDocPages}`;
                    pageRangeSummary.className = 'text-[11px] text-charcoal-muted font-normal';
                }
            } else {
                selectedPagesList = parsePageRange(raw, totalDocPages);
                if (selectedPagesList.length === 0) {
                    if (pageRangeSummary) {
                        pageRangeSummary.innerText = `⚠ Out of bounds (document has ${totalDocPages} pages)`;
                        pageRangeSummary.className = 'text-[11px] text-amber-600 font-semibold';
                    }
                } else {
                    const formatted = formatPageRanges(selectedPagesList);
                    if (pageRangeSummary) {
                        pageRangeSummary.innerText = `✓ ${selectedPagesList.length} page(s) selected: ${formatted}`;
                        pageRangeSummary.className = 'text-[11px] text-brand-600 font-semibold';
                    }
                }
            }
        } else if (selMode === 'odd') {
            selectedPagesList = parsePageRange('odd', totalDocPages);
        } else if (selMode === 'even') {
            selectedPagesList = parsePageRange('even', totalDocPages);
        } else {
            selectedPagesList = parsePageRange('all', totalDocPages);
        }

        // Update live preview to reflect selection
        refreshPreviewForSelection();

        const activePagesCount = selectedPagesList.length > 0 ? selectedPagesList.length : totalDocPages;
        const sheetsPerCopy = (sides === 'DOUBLE') ? Math.ceil(activePagesCount / 2) : activePagesCount;
        const totalSheets = sheetsPerCopy * copies;

        // Update sheet counter in header
        const sheetHeaderEl = document.getElementById('sheetCountHeader');
        if (sheetHeaderEl) {
            sheetHeaderEl.innerHTML = `<span class="material-symbols-outlined text-[13px]">layers</span> ${totalSheets} sheet${totalSheets === 1 ? '' : 's'} of paper`;
        }

        // Rate lookup
        let ratePerPage = 2.0;
        const match = rates.find(r => r.paper_size === paperSize && r.color_mode === colorMode && r.sides === sides);
        if (match) {
            ratePerPage = parseFloat(match.price_per_page);
        } else {
            if (colorMode === 'COLOR') {
                ratePerPage = (sides === 'DOUBLE') ? 18.0 : 10.0;
            } else {
                ratePerPage = (sides === 'DOUBLE') ? 3.0 : 2.0;
            }
        }

        let totalPrice = Math.round(sheetsPerCopy * ratePerPage * copies * 100) / 100;
        if (totalPrice < 1.0) totalPrice = 1.0;

        const summaryPagesEl = document.getElementById('summaryTotalPages');
        if (summaryPagesEl) {
            const sideLabel = sides === 'DOUBLE' ? 'Back-to-Back' : 'Single-Sided';
            summaryPagesEl.innerText = `${activePagesCount} Page(s) • ${totalSheets} Sheet(s) (${sideLabel})`;
        }

        const summaryPriceEl = document.getElementById('summaryTotalPrice');
        if (summaryPriceEl) {
            summaryPriceEl.innerText = `₹${totalPrice.toFixed(2)}`;
        }

        return totalPrice;
    }

    // Step 2 -> Step 3: Proceed to Pay
    document.getElementById('btnProceedToPay').addEventListener('click', async () => {
        const btn = document.getElementById('btnProceedToPay');
        btn.disabled = true;
        btn.innerHTML = `<span class="material-symbols-outlined text-[16px] animate-spin">refresh</span><span>Processing...</span>`;

        let finalPageRange = 'all';
        const selMode = pagesSelect ? pagesSelect.value : 'all';
        if (selMode === 'custom') {
            const raw = pageRangeInput ? pageRangeInput.value.trim() : '';
            if (!raw || selectedPagesList.length === 0) {
                alert(`Please enter valid page numbers (e.g. 1, 3, or 1-5) between 1 and ${currentDoc.total_pages}.`);
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[16px]">print</span><span>Print</span>`;
                pageRangeInput.focus();
                return;
            }
            finalPageRange = formatPageRanges(selectedPagesList) || raw;
        } else if (selMode === 'odd') {
            finalPageRange = 'odd';
        } else if (selMode === 'even') {
            finalPageRange = 'even';
        }

        try {
            const res = await fetch(`/api/print/${shopSlug}/order`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    document_id: currentDoc.document_id,
                    copies: copies,
                    color_mode: colorMode,
                    sides: sides,
                    paper_size: paperSize,
                    page_range: finalPageRange,
                    layout: layout
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                alert(data.error || 'Failed to create print order.');
                btn.disabled = false;
                btn.innerHTML = `<span class="material-symbols-outlined text-[16px]">print</span><span>Print</span>`;
                return;
            }

            currentOrder = data;
            showPaymentStep(data);
        } catch (e) {
            alert('Connection failed. Please retry.');
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-outlined text-[16px]">print</span><span>Print</span>`;
        }
    });

    // Step 3: Show Payment Screen
    function showPaymentStep(order) {
        stepSettings.classList.add('hidden');
        stepPayment.classList.remove('hidden');

        setIndicatorActive(ind3);

        document.getElementById('payAmountDisplay').innerText = `₹${parseFloat(order.total_amount).toFixed(2)}`;
        document.getElementById('payOrderNumber').innerText = order.order_number;
        document.getElementById('upiQrImg').src = order.qr_url;

        // Set 1-Click App Links
        if (order.app_links) {
            document.getElementById('btnPhonePe').href = order.app_links.phonepe;
            document.getElementById('btnGPay').href = order.app_links.gpay;
            document.getElementById('btnPaytm').href = order.app_links.paytm;
            document.getElementById('btnBhim').href = order.app_links.bhim;
        }
    }

    // Submit Payment Reference (UTR)
    document.getElementById('btnConfirmUpiPayment').addEventListener('click', async () => {
        const utr = document.getElementById('utrInput').value.trim();
        await submitPaymentRecord('DIRECT_UPI', utr);
    });

    // Pay Cash at Counter
    const btnCash = document.getElementById('btnPayCashAtCounter');
    if (btnCash) {
        btnCash.addEventListener('click', async () => {
            if (confirm("Confirm paying cash directly at the shop counter?")) {
                await submitPaymentRecord('CASH_AT_COUNTER', 'CASH_VERIFY_PENDING');
            }
        });
    }

    async function submitPaymentRecord(method, ref) {
        if (!currentOrder) return;

        try {
            const res = await fetch(`/api/orders/${currentOrder.order_number}/pay`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    payment_method: method,
                    transaction_reference: ref
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                showTrackerStep(data);
            } else {
                alert(data.error || 'Failed to submit payment.');
            }
        } catch (e) {
            alert('Failed to submit. Check your network.');
        }
    }

    // Step 4: Show Live Tracker & Poll 2.5s
    function showTrackerStep(paymentResponse) {
        stepPayment.classList.add('hidden');
        stepTracker.classList.remove('hidden');

        setIndicatorActive(ind4);

        document.getElementById('trackerOrderNumber').innerText = currentOrder.order_number;

        if (paymentResponse && paymentResponse.payment_method === 'CASH_AT_COUNTER') {
            const titleEl = document.getElementById('trackerStatusTitle');
            const descEl = document.getElementById('trackerStatusDesc');
            const iconEl = document.getElementById('trackerStatusIcon');
            const iconBoxEl = document.getElementById('trackerStatusIconBox');
            const spoolEl = document.getElementById('trackerSpoolStatus');

            if (titleEl) titleEl.innerText = 'Please Pay Cash at Counter';
            if (descEl) descEl.innerHTML = `Order: <span class="font-mono text-amber-700 font-bold">${currentOrder.order_number}</span> • Hand cash to shopkeeper`;
            if (iconEl) iconEl.innerText = 'payments';
            if (iconBoxEl) iconBoxEl.className = 'w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 mx-auto flex items-center justify-center mb-4';
            if (spoolEl) spoolEl.innerText = 'AWAITING CASH VERIFICATION';
        }

        // Poll order status every 2500ms
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(pollOrderStatus, 2500);
    }

    async function pollOrderStatus() {
        if (!currentOrder) return;

        try {
            const res = await fetch(`/api/orders/${currentOrder.order_number}/status`);
            const data = await res.json();

            const titleEl = document.getElementById('trackerStatusTitle');
            const descEl = document.getElementById('trackerStatusDesc');
            const iconEl = document.getElementById('trackerStatusIcon');
            const iconBoxEl = document.getElementById('trackerStatusIconBox');
            const spoolEl = document.getElementById('trackerSpoolStatus');

            if (data.order_status === 'AWAITING_PAYMENT') {
                if (titleEl) titleEl.innerText = 'Please Pay Cash at Counter';
                if (descEl) descEl.innerHTML = `Order: <span class="font-mono text-amber-700 font-bold">${currentOrder.order_number}</span> • Hand cash to shopkeeper`;
                if (iconEl) iconEl.innerText = 'payments';
                if (iconBoxEl) iconBoxEl.className = 'w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 mx-auto flex items-center justify-center mb-4';
                if (spoolEl) spoolEl.innerText = 'AWAITING CASH VERIFICATION';
            } else if (data.order_status === 'PRINTING') {
                if (titleEl) titleEl.innerText = 'Printing Your Copy Now...';
                if (descEl) descEl.innerHTML = `Order: <span class="font-mono text-brand-600 font-bold">${currentOrder.order_number}</span>`;
                if (iconEl) iconEl.innerText = 'print';
                if (iconBoxEl) iconBoxEl.className = 'w-16 h-16 rounded-2xl bg-brand-50 border border-brand-200 text-brand-600 mx-auto flex items-center justify-center mb-4 animate-pulse';
                if (spoolEl) spoolEl.innerText = data.print_status || 'SPOOLING TO HARDWARE';
            } else if (spoolEl && data.print_status) {
                spoolEl.innerText = data.print_status;
            }

            if (data.is_completed) {
                clearInterval(pollInterval);
                triggerPrintCompletion();
            }
        } catch (e) {
            console.error('Polling error:', e);
        }
    }

    // When status becomes COMPLETED
    function triggerPrintCompletion() {
        document.getElementById('printingState').classList.add('hidden');
        document.getElementById('completedState').classList.remove('hidden');

        // 1. Mobile Haptic Buzz
        if ('vibrate' in navigator) {
            navigator.vibrate([150, 80, 150]);
        }

        // 2. Audio Chime
        const audio = document.getElementById('completedChime');
        if (audio) {
            audio.play().catch(e => console.log('Audio autoplay prevented'));
        }
    }

    function setIndicatorActive(el) {
        el.classList.remove('text-slate-500');
        el.classList.add('text-white');
        const badge = el.querySelector('span:first-child');
        badge.classList.remove('bg-slate-700', 'text-slate-400');
        badge.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
    }

    function setIndicatorReset(el) {
        el.classList.remove('text-white');
        el.classList.add('text-slate-500');
        const badge = el.querySelector('span:first-child');
        badge.classList.add('bg-slate-700', 'text-slate-400');
        badge.classList.remove('bg-indigo-600', 'text-white');
    }
});

