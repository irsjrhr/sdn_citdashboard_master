
const datasets_default = [
    { label: 'Cabang A', collected_amount: 120, uncollected_amount: 30 },
    { label: 'Cabang B', collected_amount: 90,  uncollected_amount: 50 },
    { label: 'Cabang C', collected_amount: 150, uncollected_amount: 20 },
    { label: 'Cabang C', collected_amount: 150, uncollected_amount: 20 },
];




function buildStackedBarChart(data_config = {
    el: {},
    data: datasets_default,
    config: {
        stacks: [
            {
                key: 'key_row_green',
                label: 'Label Data Green',
                backgroundColor: '#4CAF50'
            },
            {
                key: 'key_row_red',
                label: 'Label Data Red',
                backgroundColor: 'F44336'
            },
        ],
        heightChart: 300,
        onBarClick: null // Diisi argumen function callback
    }
}) 
{

    //BODY FUNCTION


    const el = data_config.el;
    const data = data_config.data;
    const config = data_config.config;

    // Guard
    if (!el) return;

    // Set height
    el.height = config.heightChart || 300;

    const labels = data.map(item => item.label);

    const datasets = config.stacks.map(stack => ({
        label: stack.label,
        data: data.map(item => {
            let val = item[stack.key];
            if (typeof val === 'string') {
                val = val.replace('%', '').replace(/,/g, '').trim();
            }
            return Number(val) || 0;
        }),
        backgroundColor: stack.backgroundColor
    }));

    const minBarWidth = 60;
    el.style.width = Math.max(labels.length * minBarWidth, 1200) + 'px';

    const chart = new Chart(el, {
        type: 'bar',
        data: { labels, datasets },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            devicePixelRatio: 1,

            // 🎯 EVENT CLICK
            onClick: (evt, elements) => {

                if (!elements.length) return;

                const element = elements[0];

                const dataIndex = element.index;
                const datasetIndex = element.datasetIndex;

                const label = labels[dataIndex];
                const datasetLabel = datasets[datasetIndex].label;
                const value = datasets[datasetIndex].data[dataIndex];
                const rawData = data[dataIndex];

                // 🔔 Panggil callback user
                if (typeof config.onBarClick === 'function') {
                    config.onBarClick(
                        label,
                        rawData, //Row data dari stackbar yang diklik
                        datasetLabel,
                        value,
                        dataIndex,
                        datasetIndex
                        );
                }
            },

            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 14 } }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    ticks: { font: { size: 12 } }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: { font: { size: 12 } }
                }
            }
        }
    });

    return chart;
}





