// FUNGSI UTAMA stackbar_chart - REVISI
window.stackbar_chart = function ({
    el,
    labels,
    series,
    xLabel = '',
    yLabel = '',
    showLegend = true,
    horizontal = false,
    barThickness = 40,      // 🔥 TERIMA PARAMETER INI
    maxBarThickness = 40,   // 🔥 TERIMA PARAMETER INI
    categoryPercentage = 0.8, // 🔥 BARU
    barPercentage = 0.9,    // 🔥 BARU
    spacing = 30            // 🔥 BARU
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
                barThickness: barThickness,        // 🔥 PAKE PARAMETER
                maxBarThickness: maxBarThickness,  // 🔥 PAKE PARAMETER
                categoryPercentage: categoryPercentage, // 🔥 BARU
                barPercentage: barPercentage       // 🔥 BARU
            }))
        },
        options: {
            indexAxis: horizontal ? 'y' : 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: showLegend,
                    position: 'top',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += new Intl.NumberFormat('id-ID').format(context.parsed.y || context.parsed.x);
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: !!xLabel,
                        text: xLabel,
                        font: { 
                            weight: 'bold',
                            size: 14 
                        },
                        padding: { top: 10, bottom: 10 }
                    },
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 30,
                        font: { size: 12 },
                        padding: 8
                    },
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0,0,0,0.05)'
                    },
                    // 🔥 INI BUAT SPACING DI ANTAR BAR
                    offset: true,
                    afterFit: function(scale) {
                        scale.paddingLeft = spacing / 2;
                        scale.paddingRight = spacing / 2;
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: !!yLabel,
                        text: yLabel,
                        font: { 
                            weight: 'bold',
                            size: 14 
                        },
                        padding: { top: 0, bottom: 15 }
                    },
                    ticks: {
                        autoSkip: false,
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID').format(value);
                        },
                        font: { size: 12 }
                    },
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            },
            layout: {
                padding: {
                    left: 15,
                    right: 15,
                    top: showLegend ? 25 : 15,
                    bottom: 20
                }
            }
        }
    });
};

// FUNGSI buildStackbarByClass - FIXED VERSION
window.buildStackbarByClass = function ({
    className,
    datasets,
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

        // 1. Labels dan Series
        const labels = datasets.map(d => d.label || '');
        const series = [
            {
                label: 'Collected',
                data: datasets.map(d => Number(d.confirmed_amount) || 0),
                color: 'rgba(34, 197, 94, 0.7)'
            },
            {
                label: 'Uncollected',
                data: datasets.map(d => Number(d.unconfirmed_amount) || 0),
                color: 'rgba(239, 68, 68, 0.7)'
            }
        ];

        // 2. Setup scroll container untuk VERTIKAL chart
        if (scrollable && !horizontal) {
            const parent = canvas.parentNode;
            let wrapper = parent;
            
            if (!parent.classList.contains('chart-scroll-wrapper')) {
                wrapper = document.createElement('div');
                wrapper.className = 'chart-scroll-wrapper';
                parent.insertBefore(wrapper, canvas);
                wrapper.appendChild(canvas);
            }
            
            // 🎯 HITUNG LEBAR TOTAL DENGAN SPACING
            const totalBars = labels.length;
            const totalSpacing = (totalBars + 1) * spacing; // Spacing di kiri+kanan setiap bar
            const totalBarWidth = totalBars * barWidth;
            const totalWidth = totalBarWidth + totalSpacing + 100; // + margin
            
            // Set wrapper style
            wrapper.style.cssText = `
                width: 100%;
                max-width: ${maxWidth};
                max-height: ${maxHeight};
                overflow-x: auto;
                overflow-y: hidden;
                position: relative;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 20px 10px 15px 10px;
                margin: 0 auto;
                background: white;
            `;
            
            // Set canvas dimensions
            canvas.style.width = `${totalWidth}px`;
            canvas.style.height = maxHeight;
            canvas.style.display = 'block';
            
            console.log(`Chart: ${totalBars} bars | Bar: ${barWidth}px | Spacing: ${spacing}px | Total: ${totalWidth}px`);
        }

        // 3. Render chart dulu
        const chart = window.stackbar_chart({
            el: canvas,
            labels,
            series,
            xLabel,
            yLabel,
            showLegend,
            horizontal: false
        });

        // 4. 🎯 MODIFY SETELAH CHART DIBUAT
        if (!horizontal) {
            // Tunggu chart selesai render
            setTimeout(() => {
                // A. Atur bar thickness
                chart.data.datasets.forEach(dataset => {
                    dataset.barThickness = barWidth;
                    dataset.maxBarThickness = barWidth;
                });
                
                // B. Atur spacing pada X-axis
                if (chart.options.scales.x) {
                    // Update ticks untuk rotation
                    chart.options.scales.x.ticks = {
                        ...chart.options.scales.x.ticks,
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 30,
                        padding: 15 // Tambah padding label
                    };
                    
                    // 🎯 INI KUNCI NYA: Buat custom scale
                    const originalFit = chart.scales.x.fit;
                    chart.scales.x.fit = function() {
                        originalFit.call(this);
                        // Tambah padding kiri-kanan untuk spacing
                        this.width = this.width - (spacing * 2);
                        this.left += spacing;
                        this.right -= spacing;
                    };
                    
                    // Force update layout
                    chart.options.layout = {
                        ...chart.options.layout,
                        padding: {
                            left: spacing / 2,
                            right: spacing / 2,
                            top: showLegend ? 40 : 20,
                            bottom: 40
                        }
                    };
                }
                
                // C. Update chart
                chart.update('none'); // Update tanpa animasi
                
                // D. 🎯 INI TRICKNYA: Adjust canvas width untuk spacing
                setTimeout(() => {
                    const currentWidth = parseInt(canvas.style.width);
                    const newWidth = currentWidth + (spacing * labels.length);
                    canvas.style.width = `${newWidth}px`;
                    
                    // Update wrapper scroll width
                    const wrapper = canvas.closest('.chart-scroll-wrapper');
                    if (wrapper) {
                        wrapper.scrollLeft = 0; // Reset scroll position
                    }
                    
                    console.log('Adjusted canvas width:', newWidth);
                }, 100);
                
            }, 100);
        }
    });
};