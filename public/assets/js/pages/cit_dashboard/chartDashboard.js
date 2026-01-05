// INGAT!!! INI KETERGANTUNGAN DENGAN SCRIPT INISIASI generalBarChart.js 

    window.chartDashboard = (
        selector_el = ".chart",
        data = [],
        labels = [],
        mapping = null,
        options = {}
        ) => {

        if (!Array.isArray(data) || data.length === 0) {
            console.warn('chartDashboard: data kosong');
            return;
        }

        const firstRow = data[0];
        const keys = Object.keys(firstRow);

    /* ===============================
     * AUTO DETECT MAPPING
     * =============================== */
        if (!mapping) {
            mapping = {
                label: keys[0],
                indicators: keys.slice(1)
            };
        }

    /* ===============================
     * X AXIS
     * =============================== */
        const xLabels = data.map(row => row[mapping.label]);

    /* ===============================
     * COLOR PALETTE
     * =============================== */
        const COLORS = [
        'rgba(34, 197, 94, 0.5)',   // green
        'rgba(239, 68, 68, 0.6)',   // red
        'rgba(59, 130, 246, 0.6)',  // blue
        'rgba(234, 179, 8, 0.6)',   // yellow
        'rgba(168, 85, 247, 0.6)',  // purple
    ];

    let hasPercentAxis = false;

    /* ===============================
     * BUILD DATASETS (AUTO INDICATOR)
     * =============================== */
    const datasets = mapping.indicators.map((key, index) => {

        const values = data.map(row => Number(row[key]) || 0);

        // ---- AUTO DETECT PERCENT ----
        const isPercent =
        /percent|rate|%/i.test(key) ||
        values.every(v => v >= 0 && v <= 100);

        if (isPercent) hasPercentAxis = true;

        return {
            label: labels[index] || key.replace(/_/g, ' ').toUpperCase(),
            data: values,
            backgroundColor: COLORS[index % COLORS.length],
            borderRadius: 6,
            yAxisID: isPercent ? 'y1' : 'y'
        };
    });

    /* ===============================
     * CHART OPTIONS (AUTO AXIS)
     * =============================== */
    const chartOptions = {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: options.yTitle || 'Value'
                }
            }
        }
    };

    if (hasPercentAxis) {
        chartOptions.scales.y1 = {
            beginAtZero: true,
            position: 'right',
            max: 100,
            grid: {
                drawOnChartArea: false
            },
            title: {
                display: true,
                text: options.y1Title || 'Percent (%)'
            },
            ticks: {
                callback: value => value + '%'
            }
        };
    }

    /* ===============================
     * RENDER
     * =============================== */
    generalBarChart(
        selector_el,
        xLabels,
        datasets,
        chartOptions
        );
};