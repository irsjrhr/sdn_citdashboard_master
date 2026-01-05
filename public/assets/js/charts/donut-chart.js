window.donutChart = function ({
    canvasId,
    labels,
    data,
    colors,
    valueFormatter
}) {
    const numericData = data.map(n => Number(n) || 0);

    return new Chart(document.getElementById(canvasId), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: numericData,
                backgroundColor: colors || [
                    '#2563eb',
                    '#16a34a',
                    '#f97316',
                    '#dc2626',
                    '#9333ea'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const val = ctx.parsed;
                            return valueFormatter
                                ? valueFormatter(val)
                                : val.toLocaleString();
                        }
                    }
                }
            }
        }
    });
};
