
window.buildStackbarByClass = function ({
    className,
    datasets,
    ordertype = null,
    horizontal = true,
    xLabel = '',
    yLabel = '',
    showLegend = true
}) {
    document.querySelectorAll(`.${className}`).forEach(canvas => {

        const filtered = ordertype
            ? datasets.filter(d => d.ordertype === ordertype)
            : datasets;

        if (!filtered.length) return;

        const labels = filtered.map(d => d.label);

        const series = [
            {
                label: 'Collected',
                data: filtered.map(d => d.confirmed_amount),
                color: 'rgba(34, 197, 94, 0.5)'
            },
            {
                label: 'Uncollected',
                data: filtered.map(d => d.unconfirmed_amount),
                color: 'rgba(239, 68, 68, 0.6)'
            }
        ];

        window.stackbar_chart({
            el: canvas,
            labels,
            series,
            xLabel,
            yLabel,
            showLegend,
            horizontal
        });
    });
};



window.stackbar_chart = function ({
    el,
    labels,
    series,
    xLabel = '',
    yLabel = '',
    showLegend = true,
    horizontal = false
}) {
    return new Chart(el, {
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
            indexAxis: horizontal ? 'y' : 'x',
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
                        label: ctx => {
                            const value = horizontal ? ctx.parsed.x : ctx.parsed.y;
                            if (!value) return null;
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
