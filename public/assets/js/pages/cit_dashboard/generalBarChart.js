/**
 * General Bar Chart (Class Selector)
 * @param {string} canvasClass - Class canvas (tanpa titik)
 * @param {array} labels       - X-axis labels
 * @param {array} datasets     - Konfigurasi dataset
 * @param {object} options     - Custom options (optional)
 */
window.generalBarChart = function (canvasClass, labels, datasets, options = {}) {

    // =========================
    // VALIDASI ELEMENT
    // =========================
    const canvases = document.querySelectorAll(`${canvasClass}`);
    if (!canvases.length) {
        console.warn(`Canvas dengan class "${canvasClass}" tidak ditemukan`);
        return;
    }

    // =========================
    // NORMALISASI DATA
    // =========================
    const normalizedDatasets = datasets.map(ds => ({
        ...ds,
        data: Array.isArray(ds.data)
        ? ds.data.map(n => Number(n) || 0)
        : []
    }));

    // =========================
    // INIT CHART PER CANVAS
    // =========================
    canvases.forEach(canvas => {

        // Cegah error jika canvas invalid
        if (!canvas.getContext) return;

        new Chart(canvas, {
            type: 'bar',

            data: {
                labels,
                datasets: normalizedDatasets
            },

            options: {
                responsive: true,

                plugins: {
                    legend: { display: true },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    datalabels: {
                        display: false
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: options.yTitle || 'Value'
                        },
                        ticks: {
                            callback: val =>
                            typeof window.formatAbbreviatedNumber === 'function'
                            ? window.formatAbbreviatedNumber(val)
                            : val
                        }
                    }
                },

                // merge custom chart options
                ...(options.chart || {})
            }
        });
    });
};




/*
canvasClass = ".chart_top_branches"

labels = [
  "Surabaya",
  "Bandung",
  "Jakarta"
]

datasets = [
  {
    label: "Total AR (Rp)",
    data: [100, 200, 300]
  },
  {
    label: "Paid (%)",
    data: [80, 70, 90]
  }
];


*/