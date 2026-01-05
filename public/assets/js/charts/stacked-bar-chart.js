window.stackedBarChart = function ({
    canvasId,
    labels,              // Y-axis labels (e.g. Salesman names)
    stacks,              // [{ label, data, color }]
    orientation = 'horizontal', // horizontal | vertical
    showTotals = true,
    valueFormatter
}) {
    const isHorizontal = orientation === 'horizontal';

    const formatFull = (val) =>
        valueFormatter
            ? valueFormatter(val)
            : val.toLocaleString();

    const datasets = stacks.map(stack => ({
        label: stack.label,
        data: stack.data.map(v => Number(v) || 0),
        backgroundColor: stack.color,
        borderWidth: 0,
        stack: 'total'
    }));

    return new Chart(document.getElementById(canvasId), {
        type: 'bar',
        data: {
            labels,
            datasets
        },
        options: {
            responsive: true,
            indexAxis: isHorizontal ? 'y' : 'x',
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx =>
                            `${ctx.dataset.label}: ${formatFull(ctx.parsed[isHorizontal ? 'x' : 'y'])}`,
                        footer: items => {
                            const total = items.reduce(
                                (sum, i) => sum + i.parsed[isHorizontal ? 'x' : 'y'],
                                0
                            );
                            return showTotals ? `Total: ${formatFull(total)}` : '';
                        }
                    }
                },
                datalabels: {
                    display: false // stacked labels become unreadable
                }
            },
            scales: {
                x: {
                    stacked: true,
                    beginAtZero: true
                },
                y: {
                    stacked: true
                }
            }
        }
    });
};
