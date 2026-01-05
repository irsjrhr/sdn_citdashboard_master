window.clusteredChart = function (canvasId, labels, sales, returns) {
    const rawSales = sales.map(n => Number(n) || 0);
    const rawReturns = returns.map(n => Number(n) || 0);
    const returnRates = rawReturns.map((ret, i) =>
        rawSales[i] > 0 ? ((ret / rawSales[i]) * 100).toFixed(2) : "0.00"
    );

    new Chart(document.getElementById(canvasId), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Sales',
                    data: rawSales,
                    backgroundColor: [
                        'rgba(37, 99, 235, 0.35)'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 6,
                    grouped: false,
                    order: 1,
                    datalabels: { display: false }
                },
                {
                    label: 'Return',
                    data: rawReturns,
                    backgroundColor: [
                       'rgba(255, 80, 0, 0.95)'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 6,
                    grouped: false,
                    order: 2,
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'end',
                        offset: -4,
                        clip: false,
                        formatter: (value, ctx) => `${returnRates[ctx.dataIndex]}%`
                    }
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    filter: ctx => ctx.datasetIndex === 0,
                    callbacks: {
                        label: ctx => {
                            const i = ctx.dataIndex;
                            return [
                                `Sales: ${rawSales[i].toLocaleString()}`,
                                `Return: ${rawReturns[i].toLocaleString()}`,
                                `Return Rate: ${returnRates[i]}%`
                            ];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val =>
                            typeof window.formatAbbreviatedNumber === "function"
                                ? window.formatAbbreviatedNumber(val)
                                : val
                    },
                    title: { display: true, text: 'Value' }
                }
            }
        }
    });

};