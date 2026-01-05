window.stackedBar2Chart = function ({
    canvasId,
    labels,
    series,
    xLabel,
    yLabel,
    showLegend = true,
    horizontal = false, // 👈 new flag
}) {
    return new Chart(document.getElementById(canvasId), {
        type: 'bar',
        data: {
            labels,
            datasets: series.map(s => ({
                label: s.label,
                data: s.data.map(v => Number(v) || 0),
                backgroundColor: s.color,
                borderWidth: 0
            }))
        },
        options: {
            indexAxis: horizontal ? 'y' : 'x', // 👈 flip axis
            responsive: true,
            plugins: {
                legend: {
                    display: showLegend,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        title: ctx => ctx[0].label,
                        label: ctx => {
                            const value = horizontal ? ctx.parsed.x : ctx.parsed.y;
                            if (value === 0) return null;
                            return `${ctx.dataset.label}: ${value.toLocaleString()}`;
                        },
                        footer: ctx => {
                            const total = ctx.reduce((sum, i) => {
                                return sum + (horizontal ? i.parsed.x : i.parsed.y);
                            }, 0);
                            return `Total: ${total.toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: !!(horizontal ? yLabel : xLabel),
                        text: horizontal ? yLabel : xLabel
                    }
                },
                y: {
                    stacked: true,
                    title: {
                        display: !!(horizontal ? xLabel : yLabel),
                        text: horizontal ? xLabel : yLabel
                    }
                }
            }
        }
    });
};
