window.stackbar_chart = function ({
    el,
    labels,
    series,
    xLabel = '',
    yLabel = '',
    showLegend = true,
    horizontal = false,
    barThickness = 40,
    maxBarThickness = 40,
    categoryPercentage = 0.8,
    barPercentage = 0.9
}) {

    return new Chart(el, {
        type: 'bar',
        data: {
            labels,
            datasets: series.map(s => ({
                label: s.label,
                data: s.data.map(v => Number(v) || 0),
                backgroundColor: s.color,
                borderWidth: 0,
                barThickness,
                maxBarThickness,
                categoryPercentage,
                barPercentage
            }))
        },
        options: {
            indexAxis: horizontal ? 'y' : 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: showLegend,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label(ctx) {
                            const value = ctx.parsed.y ?? ctx.parsed.x;
                            return `${ctx.dataset.label}: ${new Intl.NumberFormat('id-ID').format(value)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    title: { display: !!xLabel, text: xLabel }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: { display: !!yLabel, text: yLabel },
                    ticks: {
                        callback: v => new Intl.NumberFormat('id-ID').format(v)
                    }
                }
            }
        }
    });
};




window.buildStackbarByClass = function ({
    className,
    datasets,
    seriesConfig = [],
    horizontal = false,
    xLabel = '',
    yLabel = '',
    showLegend = true,
    scrollable = true,
    maxWidth = '1500px',
    maxHeight = '500px',
    barWidth = 40,
    spacing = 80
}) {

    document.querySelectorAll(`.${className}`).forEach(canvas => {

        if (!Array.isArray(datasets) || !datasets.length) return;
        if (!Array.isArray(seriesConfig) || !seriesConfig.length) return;

        /* =============================
         * 1️⃣ LABELS
         * ============================= */
        const labels = datasets.map(d => d.label ?? '');

        /* =============================
         * 2️⃣ SERIES (DINAMIS)
         * ============================= */
        const series = seriesConfig.map(cfg => ({
            label: cfg.label,
            data: datasets.map(d => Number(d[cfg.key]) || 0),
            color: cfg.color
        }));

        /* =============================
         * 3️⃣ SCROLL WRAPPER (VERTIKAL)
         * ============================= */
        if (scrollable && !horizontal) {
            const parent = canvas.parentNode;
            let wrapper = parent;

            if (!parent.classList.contains('chart-scroll-wrapper')) {
                wrapper = document.createElement('div');
                wrapper.className = 'chart-scroll-wrapper';
                parent.insertBefore(wrapper, canvas);
                wrapper.appendChild(canvas);
            }

            const totalBars = labels.length;
            const totalWidth = (totalBars * barWidth) + ((totalBars + 1) * spacing);

            wrapper.style.cssText = `
                width: 100%;
                max-width: ${maxWidth};
                max-height: ${maxHeight};
                overflow-x: auto;
                overflow-y: hidden;
                padding: 15px;
                background: #fff;
                border-radius: 8px;
            `;

            canvas.style.width = `${totalWidth}px`;
            canvas.style.height = maxHeight;
            canvas.style.display = 'block';
        }

        /* =============================
         * 4️⃣ RENDER CHART
         * ============================= */
        const chart = stackbar_chart({
            el: canvas,
            labels,
            series,
            xLabel,
            yLabel,
            showLegend,
            horizontal,
            barThickness: barWidth,
            maxBarThickness: barWidth
        });

        chart.update();
    });
};
