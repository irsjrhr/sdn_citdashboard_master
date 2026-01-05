window.barChart = function ({
    canvasId,
    labels,
    data,
    label = 'Value',
    backgroundColor = 'rgba(37, 99, 235, 0.35)',
    yAxisLabel,
    valueFormatter,
    showDataLabels = false,
    orientation = 'vertical' // 👈 NEW
}) {
    const numericData = data.map(n => Number(n) || 0);
    const isHorizontal = orientation === 'horizontal';

    const formatFull = (val) =>
        valueFormatter
            ? valueFormatter(val)
            : val.toLocaleString();

    const formatAbbrev = (val) =>
        typeof window.formatAbbreviatedNumber === 'function'
            ? window.formatAbbreviatedNumber(val)
            : val.toLocaleString();

    return new Chart(document.getElementById(canvasId), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label,
                data: numericData,
                backgroundColor,
                borderWidth: 1.5,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            indexAxis: isHorizontal ? 'y' : 'x', // ✅ orientation switch
            plugins: {
                legend: { display: true },

                // Tooltip → FULL value (axis-aware)
                tooltip: {
                    callbacks: {
                        label: ctx =>
                            formatFull(isHorizontal ? ctx.parsed.x : ctx.parsed.y)
                    }
                },

                // DataLabels → ABBREVIATED (axis-aware)
                datalabels: {
                    display: showDataLabels,
                    anchor: 'end',
                    align: isHorizontal ? 'right' : 'top',
                    formatter: val => formatAbbrev(val),
                    font: {
                        weight: '600',
                        size: 12
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: isHorizontal,
                    title: {
                        display: isHorizontal,
                        text: isHorizontal ? (yAxisLabel || label) : undefined
                    },
                },
                y: {
                    beginAtZero: !isHorizontal,
                    title: {
                        display: !isHorizontal,
                        text: !isHorizontal ? (yAxisLabel || label) : undefined
                    },
                }
            }
        },
        plugins: showDataLabels ? [ChartDataLabels] : []
    });
};