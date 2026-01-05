window.trendChart = function (canvasId, trendData) {

    const labels = trendData.map(t => t.MonthText);
    const returnValues = trendData.map(t => Number(t.ReturnValue) || 0);

    new Chart(document.getElementById(canvasId), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Return',
                    data: returnValues,
                    borderColor: '#ef4444',
                    backgroundColor: '#ef4444',
                    borderWidth: 3,
                    tension: 0.3,
                    pointRadius: 3
                }
            ]
        },
        options: {
            plugins: {
                datalabels: {
                    display: false
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        title: ctx => ctx[0].label,
                        label: ctx => {
                            const i = ctx.dataIndex;
                            const row = trendData[i];

                            const salesValue  = Number(row.TotalSales) || 0;
                            const returnValue = Number(row.ReturnValue) || 0;

                            const rate = salesValue > 0
                                ? ((returnValue / salesValue) * 100).toFixed(2)
                                : "0.00";

                            return [
                                `Sales: ${salesValue.toLocaleString()}`,
                                `Return: ${returnValue.toLocaleString()}`,
                                `Rate: ${rate}%`
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
                    title: {
                        display: true,
                        text: 'Value'
                    }
                }
            }
        }
    });
};
