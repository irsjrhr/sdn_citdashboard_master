const COLOR_MAP = {
    // Return Reason
    'barang bocor': '#ef4444',
    'barang kedauarsa': '#dc2626',
    'produk expired': '#b91c1c',

    'barang rusak': '#f97316',
    'kemasan rusak/sobek': '#fb923c',

    'produk near expired': '#facc15',

    'produk kurang/hilang (gudang)': '#3b82f6',
    'produk kurang/hilang (pengiriman)': '#2563eb',

    'salah kirim': '#a855f7',
    'salah material': '#9333ea',
    'salah input': '#7c3aed',
    'double input': '#6d28d9',

    'barcode tidak dapat di scan': '#14b8a6',
    'barcode tidak dapat discan': '#14b8a6',

    'outlet over stock': '#22c55e',
    'over stock': '#16a34a',

    'outlet tidak order': '#84cc16',
    'outlet tidak ada cash': '#65a30d',
    'outlet tutup': '#4d7c0f',

    'failed delivery': '#0ea5e9',
    'waktu tidak cukup': '#0284c7',

    'promo tidak ter-setting': '#64748b',
    'po mati': '#475569',
    'long lat master data outlet salah': '#334155',

    'barang tidak laku': '#9ca3af',
    'lain lain': '#6b7280',

    // Distribution Channels
    'gt': '#3b82f6',
    'mt': '#22c55e',
    'it': '#f97316',
    'fs': '#a855f7',
    'lmt': '#14b8a6',
    'mtka': '#ef4444',
    'ld': '#64748b' 
};

function buildStackedBarDataset({
    rows,
    xKey,
    stackKey,
    valueKey,
    totalKey = 'total_rejection',
    colorFn = getColorByKey
}) {
     // 1️⃣ sort entities by total rejection
    const totals = {};
    rows.forEach(r => {
        totals[r[xKey]] = r[totalKey];
    });

    const xLabels = [...new Set(rows.map(r => r[xKey]))].sort((a, b) => (totals[b] || 0) - (totals[a] || 0));
    const stacks = [...new Set(rows.map(r => r[stackKey]))];

    const map = {};
    rows.forEach(r => {
        map[r[xKey]] ??= {};
        map[r[xKey]][r[stackKey]] = Number(r[valueKey]) || 0;
    });

    const series = stacks.map(stack => ({
        label: stack,
        data: xLabels.map(x => map[x]?.[stack] ?? 0),
        color: colorFn(stack)
    }));

    return { labels: xLabels, series };
}

function normalizeReason(reason) {
    return reason
        ?.split(',')[0]
        .trim()
        .replace(/\s+/g, ' ')
        .toLowerCase();
}

function getColorByKey(key) {
    const normalized = normalizeReason(key);
    return COLOR_MAP[normalized] ?? '#6b7280'; // fallback gray
}


document.addEventListener('DOMContentLoaded', () => {

    /* ========= SALESMAN ========= */
    const salesmanData = buildStackedBarDataset({
        rows: window.datasets.salesman,
        xKey: 'Salesman Name',
        stackKey: 'Return Reason',
        valueKey: 'Rejection Count'
    });
    stackedBar2Chart({
        canvasId: 'salesmanChart',
        labels: salesmanData.labels,
        series: salesmanData.series,
        yLabel: 'Rejection Count',
        horizontal: true
    });


    /* ========= DRIVER ========= */
    const driverData = buildStackedBarDataset({
        rows: window.datasets.driver,
        xKey: 'Driver',
        stackKey: 'Return Reason',
        valueKey: 'Rejection Count',
    });
    stackedBar2Chart({
        canvasId: 'driverChart',
        labels: driverData.labels,
        series: driverData.series,
        yLabel: 'Rejection Count',
        horizontal: true
    });

    /* ========= PRINCIPAL ========= */
    donutChart({
        canvasId: 'principalChart',
        labels: datasets.principal.map(d => d.Principal),
        data: datasets.principal.map(d => d['Total Amount Rejection']),
        valueFormatter: v => v.toLocaleString()
    });

    /* ========= CHANNEL (ORDERS) ========= */
    const channelBranchData = buildStackedBarDataset({
        rows: window.datasets.channel,
        xKey: 'warehousename',
        stackKey: 'Channel',
        valueKey: 'Channel Rejection Count'
    });
    stackedBar2Chart({
        canvasId: 'channelChart',
        labels: channelBranchData.labels,
        series: channelBranchData.series,
        yLabel: 'Rejection Count'
    });

    /* ========= SKU ========= */
    barChart({
        canvasId: 'skuChart',
        labels: datasets.sku.map(d => d.SKU),
        data: datasets.sku.map(d => Object.values(d).pop()),
        label: 'Rejection',
        yAxisLabel: 'Amount',
        showDataLabels: true
    });

});
