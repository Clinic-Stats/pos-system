{{-- resources/views/partials/mobile-tables.blade.php
     بۆ مۆبایل: خشتەکان دەبنە کارت، و ڕێکخستنی گشتی. تەنها لە شاشەی بچووک کار دەکات، چاپ و کۆمپیوتەر نەگۆڕدراون. --}}
<style>
@media screen and (max-width: 767px) {

    /* ===== ڕێکخستنی گشتی ===== */
    body { height: auto !important; min-height: 100vh; overflow: auto !important; padding: .6rem !important; }
    .p-8 { padding: 1rem !important; }
    .p-6 { padding: .9rem !important; }
    .p-5 { padding: .85rem !important; }
    .flex.justify-between.items-center { flex-wrap: wrap; gap: .5rem; }
    .h-\[70vh\] { height: auto !important; }

    /* خانەکان: ١٦px بۆ ئەوەی ئایفۆن زووم نەکات */
    input:not([type=checkbox]):not([type=radio]):not([type=file]):not([type=hidden]),
    select, textarea { font-size: 16px !important; }

    /* ===== خشتە -> کارت ===== */
    table.mc, table.mc tbody, table.mc tr, table.mc td { display: block; width: 100%; }
    table.mc thead { display: none; }

    table.mc tbody tr {
        position: relative;
        margin: 0 0 .65rem;
        padding: .55rem .75rem !important;
        border: 1px solid rgba(148, 163, 184, .28) !important;
        border-radius: .9rem;
        background: rgba(148, 163, 184, .06);
    }
    table.mc tr.hidden { display: none; }

    table.mc td {
        position: relative;
        min-height: 1.6rem;
        padding: .35rem 42% .35rem 0 !important;
        border: 0 !important;
        text-align: right !important;
        word-break: break-word;
    }
    table.mc td + td { border-top: 1px dashed rgba(148, 163, 184, .2) !important; }

    /* ناونیشانی خانە (لای ڕاست) */
    table.mc td[data-label]::before {
        content: attr(data-label);
        position: absolute; right: 0; top: .4rem; width: 38%;
        font-size: 10px; font-weight: 800; line-height: 1.5; color: #94a3b8;
    }

    /* خانەی پانی تەواو (کردارەکان، فۆڕم، پەیام) */
    table.mc td.mc-stack { padding: .35rem 0 !important; }
    table.mc td.mc-stack[data-label]::before {
        position: static; display: block; width: auto; margin-bottom: .3rem;
    }

    /* خانە و دوگمەکانی ناو کارت */
    table.mc td input:not([type=checkbox]):not([type=radio]):not([type=hidden]),
    table.mc td select { width: 100% !important; max-width: 100% !important; }
    table.mc td .flex { flex-wrap: wrap; }
    table.mc td form { max-width: 100%; }
}
</style>

<script>
(function () {
    const txt = el => (el.textContent || '').replace(/\s+/g, ' ').trim();

    function labelTable(table) {
        if (table.hasAttribute('data-no-cards')) return;
        const head = table.tHead;
        if (!head || !head.rows.length) return;

        const labels = [];
        Array.from(head.rows[head.rows.length - 1].cells).forEach(th => {
            const n = th.colSpan || 1;
            for (let i = 0; i < n; i++) labels.push(txt(th));
        });
        table.classList.add('mc');

        Array.from(table.tBodies).forEach(tb => Array.from(tb.rows).forEach(tr => {
            let idx = 0;
            Array.from(tr.cells).forEach(td => {
                const span = td.colSpan || 1;
                const l = span === 1 ? (labels[idx] || '') : '';
                idx += span;

                if (span > 1 || !l || /کردار/.test(l)) {
                    // پەیام، یان خانەی کردارەکان: بێ ناونیشان، پانی تەواو
                    td.classList.add('mc-stack');
                    td.removeAttribute('data-label');
                } else {
                    td.setAttribute('data-label', l);
                    td.classList.toggle('mc-stack', !!td.querySelector('input, select, textarea, form'));
                }
            });
        }));
    }

    function run() { document.querySelectorAll('table').forEach(labelTable); }

    // دێڕی نوێ (وەک زیادکردنی کاڵا بە JS) خۆکار ناونیشانی دەدرێتێ
    let raf;
    function watch() {
        new MutationObserver(() => { cancelAnimationFrame(raf); raf = requestAnimationFrame(run); })
            .observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { run(); watch(); });
    } else { run(); watch(); }
})();
</script>