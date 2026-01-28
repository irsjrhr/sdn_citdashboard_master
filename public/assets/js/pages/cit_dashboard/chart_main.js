


function formatRupiah(val) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(Number(val));
}

function formatPercent(val, digit = 2) {
    return Number(val).toLocaleString('id-ID', {
        minimumFractionDigits: digit,
        maximumFractionDigits: digit
    }) + '%';
}


//+++++++++++++++++++++++ Build Stacked Bar +++++++++++++++++++++++


const datasets_bar_ex = [
    { label: 'Cabang A', collected_amount: 120, uncollected_amount: 30 },
    { label: 'Cabang B', collected_amount: 90,  uncollected_amount: 50 },
    { label: 'Cabang C', collected_amount: 150, uncollected_amount: 20 },
    { label: 'Cabang C', collected_amount: 150, uncollected_amount: 20 },
];
const DATA_CONFIG_FORMAT_STACKEDBARCHART = {
    el: document.getElementById('#IDChartPie'),  
    data: datasets_bar_ex,
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
}



//Fungsi untuk membuat stacked bar untuk umum
// function buildStackedBarChart(data_config = DATA_CONFIG_FORMAT_STACKEDBARCHART ) 
// {

//     //BODY FUNCTION


//     const el = data_config.el;
//     const data = data_config.data;
//     const config = data_config.config;

//     // Guard
//     if (!el) return;

//     // Set height
//     el.height = config.heightChart || 300;

//     const labels = data.map(item => item.label);

//     const datasets = config.stacks.map(stack => ({
//         label: stack.label,
//         data: data.map(item => {
//             let val = item[stack.key];
//             if (typeof val === 'string') {
//                 val = val.replace('%', '').replace(/,/g, '').trim();
//             }
//             return Number(val) || 0;
//         }),
//         backgroundColor: stack.backgroundColor
//     }));

//     const minBarWidth = 60;
//     el.style.width = Math.max(labels.length * minBarWidth, 1200) + 'px';

//     const chart = new Chart(el, {
//         type: 'bar',
//         data: { labels, datasets },
//         options: {
//             responsive: false,
//             maintainAspectRatio: false,
//             devicePixelRatio: 1,

//             // 🎯 EVENT CLICK
//             onClick: (evt, elements) => {

//                 if (!elements.length) return;

//                 const element = elements[0];

//                 const dataIndex = element.index;
//                 const datasetIndex = element.datasetIndex;

//                 const label = labels[dataIndex];
//                 const datasetLabel = datasets[datasetIndex].label;
//                 const value = datasets[datasetIndex].data[dataIndex];
//                 const rawData = data[dataIndex];

//                 // 🔔 Panggil callback user
//                 if (typeof config.onBarClick === 'function') {
//                     config.onBarClick(
//                         label,
//                         rawData, //Row data dari stackbar yang diklik
//                         datasetLabel,
//                         value,
//                         dataIndex,
//                         datasetIndex
//                         );
//                 }
//             },

//             plugins: {
//                 tooltip: {
//                 mode: 'index',        // 🔥 gabung per index (per BAR)
//                 intersect: false,     // 🔥 ga harus tepat kena segment
//                 callbacks: {
//                     title: function(context) {
//                     // Judul tooltip (label bar)
//                         return context[0].label;
//                     },
//                     label: function(context) {
//                         const datasetLabel = context.dataset.label;
//                         const value = context.parsed.y;
//                         return `${datasetLabel}: ${value.toLocaleString()}`;
//                     },
//                     footer: function(context) {
//                 // Optional: total semua stack
//                         const total = context.reduce((sum, item) => sum + item.parsed.y, 0);
//                         return `Total: ${total.toLocaleString()}`;
//                     }
//                 }
//             },

//             legend: {
//                 position: 'top',
//                 align: 'start', 
//                 padding: {
//                     bottom : 100
//                     },  // 🎯 jarak ke bawah (legend → chart)
//                     labels: { font: { size: 14 } }
//                 }
//             },
//             scales: {
//                 x: {
//                     stacked: true,
//                     ticks: { font: { size: 12 } }
//                 },
//                 y: {
//                     stacked: true,
//                     beginAtZero: true,
//                     ticks: { font: { size: 12 } }
//                 }
//             }
//         }
//     });

//     return chart;
// }

function buildStackedBarChart( data_config = DATA_CONFIG_FORMAT_STACKEDBARCHART ) {

    //BODY FUNCTION

    const el     = data_config.el;
    const data   = data_config.data;
    const config = data_config.config;

    // Guard
    if ( !el ) return;

    // Set height
    el.height = config.heightChart || 300;

    const labels = data.map( item => item.label );

    const datasets = config.stacks.map( stack => ({

        label : stack.label,
        stackKey : stack.key, // 🔑 key asli stack
        backgroundColor : stack.backgroundColor,

        data : data.map( item => {

            let val = item[ stack.key ];

            if ( typeof val === 'string' ) {
                val = val.replace('%', '').replace(/,/g, '').trim();
            }

            return Number( val ) || 0;

        })


    }));

    // Set dynamic width
    const minBarWidth = 60;
    el.style.width = Math.max( labels.length * minBarWidth, 1200 ) + 'px';

    const chart = new Chart( el, {

        type : 'bar',

        data : {
            labels,
            datasets
        },

        options : {

            responsive : false,
            maintainAspectRatio : false,
            devicePixelRatio : 1,

            // 🎯 EVENT CLICK
            onClick : ( evt, elements ) => {

                if ( !elements.length ) return;

                const element = elements[0];

                const dataIndex    = element.index;
                const datasetIndex = element.datasetIndex;

                const label        = labels[ dataIndex ];
                const datasetLabel = datasets[ datasetIndex ].label;
                const value        = datasets[ datasetIndex ].data[ dataIndex ];
                const stackKey     = datasets[ datasetIndex ].stackKey;
                const row_data     = data[ dataIndex ];

                // 🔔 Callback user
                if ( typeof config.onBarClick === 'function' ) {

                    if ( !row_data || Object.keys( row_data ).length === 0 ) return;

                    config.onBarClick(
                        label,
                        row_data,
                        datasetLabel,
                        value,
                        dataIndex,
                        datasetIndex,
                        stackKey
                        );
                }

            },

            layout : {
                padding : {
                    top : 20
                }
            },

            plugins : {

                // 💬 TOOLTIP
                tooltip : {

                    mode : 'index',
                    intersect : false,

                    callbacks : {

                        title : function( context ) {
                            return context[0].label;
                        },

                        label : function( context ) {

                            const datasetLabel = context.dataset.label;
                            const value        = context.parsed.y;

                            return datasetLabel + ' : ' + value.toLocaleString();

                        },

                        // ➕ EXTRA CUSTOM INFO
                        afterBody : function( context ) {

                            const dataIndex = context[0].dataIndex;
                            const row_data  = data[ dataIndex ];

                            if ( !config.tooltipExtras || !row_data ) return [];

                            return config.tooltipExtras.map( function( extra ){

                                let val = row_data[ extra.key ];

                                if ( val == null ) return extra.label + ' : -';

                                if ( extra.format === 'number' ) {
                                    val = Number( val ).toLocaleString();
                                }

                                if ( extra.suffix ) {
                                    val = val + extra.suffix;
                                }

                                return extra.label + ' : ' + val;

                            });

                        },

                        footer : function( context ) {

                            const total = context.reduce(
                                ( sum, item ) => sum + item.parsed.y,
                                0
                                );

                            return 'Total : ' + total.toLocaleString();

                        }

                    }

                },

                // 📊 LEGEND
                legend : {

                    position : 'top',
                    align : 'start',

                    labels : {
                        font : { size : 14 },
                        padding : 20
                    }

                }

            },

            scales : {

                x : {
                    stacked : true,
                    ticks : { font : { size : 12 } }
                },

                y : {
                    stacked : true,
                    beginAtZero : true,
                    ticks : { font : { size : 12 } }
                }

            }

        }

    });

return chart;

}








// Fungsi untuk membuat stacked bar KHUSUS UNTUK BAD COLLECTION DRIVER

function buildStackedBarChart_customer(data_config = DATA_CONFIG_FORMAT_STACKEDBARCHART ) 
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
            responsive: true,
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
                tooltip: {
                mode: 'index',        // 🔥 gabung per index (per BAR)
                intersect: false,     // 🔥 ga harus tepat kena segment
                callbacks: {
                    title: function(context) {
                    // Judul tooltip (label bar)
                        return context[0].label;
                    },
                    label: function(context) {
                        const datasetLabel = context.dataset.label;
                        const value = context.parsed.y;
                        return `${datasetLabel}: ${value.toLocaleString()}`;
                    },
                    footer: function(context) {
                // Optional: total semua stack
                        const total = context.reduce((sum, item) => sum + item.parsed.y, 0);
                        return `Total: ${total.toLocaleString()}`;
                    }
                }
            },

            legend: {
                position: 'top',
                align: 'start', 
                padding: {
                    bottom : 100
                    },  // 🎯 jarak ke bawah (legend → chart)
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


function buildStackedBarChart_color( data_config = DATA_CONFIG_FORMAT_STACKEDBARCHART ) {

    //BODY FUNCTION

    const el     = data_config.el;
    const data   = data_config.data;
    const config = data_config.config;

    // Guard
    if ( !el ) return;

    // Set height
    el.height = config.heightChart || 300;

    const labels = data.map( item => item.label );

    const datasets = config.stacks.map( stack => ({

        label : stack.label,
        stackKey : stack.key, // 🔑 key asli stack
        backgroundColor : data.map( row_data => {

            //Ini diatur di datasetsnya untuk setiap row punya ket backgroundColor
            return row_data.backgroundColor;

        }),

        data : data.map( row_data => {

            let val = row_data[ stack.key ];

            if ( typeof val === 'string' ) {
                val = val.replace('%', '').replace(/,/g, '').trim();
            }

            return Number( val ) || 0;
        })


    }));

    // Set dynamic width
    const minBarWidth = 60;
    el.style.width = Math.max( labels.length * minBarWidth, 1200 ) + 'px';

    const chart = new Chart( el, {

        type : 'bar',

        data : {
            labels,
            datasets
        },

        options : {

            responsive : false,
            maintainAspectRatio : false,
            devicePixelRatio : 1,

            // 🎯 EVENT CLICK
            onClick : ( evt, elements ) => {

                if ( !elements.length ) return;

                const element = elements[0];

                const dataIndex    = element.index;
                const datasetIndex = element.datasetIndex;

                const label        = labels[ dataIndex ];
                const datasetLabel = datasets[ datasetIndex ].label;
                const value        = datasets[ datasetIndex ].data[ dataIndex ];
                const stackKey     = datasets[ datasetIndex ].stackKey;
                const row_data     = data[ dataIndex ];

                // 🔔 Callback user
                if ( typeof config.onBarClick === 'function' ) {

                    if ( !row_data || Object.keys( row_data ).length === 0 ) return;

                    config.onBarClick(
                        label,
                        row_data,
                        datasetLabel,
                        value,
                        dataIndex,
                        datasetIndex,
                        stackKey
                        );
                }

            },

            layout : {
                padding : {
                    top : 20
                }
            },

            plugins : {

                // 💬 TOOLTIP
                tooltip : {

                    mode : 'index',
                    intersect : false,

                    callbacks : {

                        title : function( context ) {
                            return context[0].label;
                        },

                        label : function( context ) {

                            const datasetLabel = context.dataset.label;
                            const value        = context.parsed.y;

                            return datasetLabel + ' : ' + value.toLocaleString();

                        },

                        // ➕ EXTRA CUSTOM INFO
                        afterBody : function( context ) {

                            const dataIndex = context[0].dataIndex;
                            const row_data  = data[ dataIndex ];

                            if ( !config.tooltipExtras || !row_data ) return [];

                            return config.tooltipExtras.map( function( extra ){

                                let val = row_data[ extra.key ];

                                if ( val == null ) return extra.label + ' : -';

                                if ( extra.format === 'number' ) {
                                    val = Number( val ).toLocaleString();
                                }

                                if ( extra.suffix ) {
                                    val = val + extra.suffix;
                                }

                                return extra.label + ' : ' + val;

                            });

                        },

                        footer : function( context ) {

                            const total = context.reduce(
                                ( sum, item ) => sum + item.parsed.y,
                                0
                                );

                            return 'Total : ' + total.toLocaleString();

                        }

                    }

                },

                // 📊 LEGEND
                legend : {

                    position : 'top',
                    align : 'start',

                    labels : {
                        font : { size : 14 },
                        padding : 20
                    }

                }

            },

            scales : {

                x : {
                    stacked : true,
                    ticks : { font : { size : 12 } }
                },

                y : {
                    stacked : true,
                    beginAtZero : true,
                    ticks : { font : { size : 12 } }
                }

            }

        }

    });

return chart;

}



function buildSingleBarChart_cohBankIn(
    data_config = DATA_CONFIG_FORMAT_BARCHART
    ) {

    const el     = data_config.el;
    const data   = data_config.data;
    const config = data_config.config;

    if (!el) return;

    // HEIGHT SAMA
    el.height = config.heightChart || 500;

    const labels = data.map(row => row.label);

    // ✅ WIDTH DINAMIS (INI YANG PENTING)
    const minBarWidth = 60;
    el.style.width = Math.max(labels.length * minBarWidth, 1200) + 'px';

    const dataset = {
        label : config.label || '',

        data : data.map(row => {
            let val = row[ config.valueKey ];
            if (typeof val === 'string') {
                val = val.replace('%', '').replace(/,/g, '').trim();
            }
            return Number(val) || 0;
        }),

        backgroundColor : data.map(
            row => row.backgroundColor || '#999'
            )
    };

    const chart = new Chart(el, {

        type : 'bar',

        data : {
            labels,
            datasets : [ dataset ]
        },

        options : {

            // 🔥 SAMA PERSIS
            responsive : false,
            maintainAspectRatio : false,
            devicePixelRatio : 1,

            onClick : (evt, elements) => {

                if (!elements.length) return;

                const elx = elements[0];
                const dataIndex = elx.index;
                const row_data = data[dataIndex];

                if (typeof config.onBarClick === 'function') {
                    config.onBarClick(
                        labels[dataIndex],
                        row_data,
                        dataset.label,
                        dataset.data[dataIndex],
                        dataIndex
                        );
                }
            },

            plugins : {

                tooltip : {
                    mode : 'index',
                    intersect : false,
                    callbacks : {
                        label : ctx =>
                        ctx.dataset.label + ' : ' +
                        ctx.parsed.y.toLocaleString()
                    }
                },

                legend : {
                    display : !!config.label,
                    position : 'top',
                    align : 'start',
                    labels : {

                        generateLabels : function () {


                            var label_generate = [
                                {
                                    text: 'COH',
                                    fillStyle: '#3BC1A8',
                                    strokeStyle: '#3BC1A8',
                                    lineWidth: 1
                                },
                                {
                                    text: 'Bank In',
                                    fillStyle: '#005461',
                                    strokeStyle: '#005461',
                                    lineWidth: 1
                                },
                            ];





                            return label_generate;
                        }
                    }

                }
            },

            scales : {
                x : {
                    ticks : { font : { size : 12 } }
                },
                y : {
                    beginAtZero : true,
                    ticks : { font : { size : 12 } }
                }
            }
        }
    });

    return chart;
}


//===================================== Build Pie Chart
const datasets_pie_ex = [
    {
        percent_amount: "20.029666319264795",
        confirmed_amount: "73480666921.0",
        ordertype: "TOP",
        paymenttype: "Cash", 
        label: "Cash",
    },
    {
        percent_amount: "20.029666319264795",
        confirmed_amount: "73480666921.0",
        ordertype: "TOP",
        paymenttype: "Giro", 
        label: "Giro",
        color_label : "#00aba9"
    },
    {
        percent_amount: "20.029666319264795",
        confirmed_amount: "73480666921.0",
        ordertype: "TOP",
        paymenttype: "Transfer",
        label: "Transfer",

    },
]


//+++++++++++++++++++++++ Build Stacked Bar +++++++++++++++++++++++

const DATA_CONFIG_FORMAT_PIECHART = {

    el: document.getElementById('IDCHART'),
    datasets: datasets_pie_ex,
    key_value: "confirmed_amount",
    label_color: {
        Giro: "#22C55E",
        Transfer: "#3B82F6",
        Cash: "#F59E0B"
    }  
}
function buildPieChart(data_config = DATA_CONFIG_FORMAT_PIECHART ) {

    var el = data_config.el;
    var label_color = data_config.label_color || DATA_CONFIG_FORMAT_PIECHART.label_color;

    var data_label = [];
    var data_value = [];
    var data_background = [];

    //Membuat dataset untuk grafik berdasarakkan formatnya
    for (var i = 0; i < data_config.datasets.length; i++) {

        var row_obj = data_config.datasets[i];
        var label = row_obj.label;
        var value_slice = row_obj[data_config.key_value];

        //Menambahkan untuk label setiap slice
        data_label.push(label);

        //Menambahkan nilai slice di chart
        data_value.push(Number(value_slice));

        //Menambahkan warna pada slice
        var color = label_color.hasOwnProperty(label)
        ? label_color[label]
        : "#000";

        data_background.push(color);
    }


    // console.log( "DEBUG COLOR DATA" );
    // console.log( data_background );

    // return false;

    new Chart(el, 
    {
        type: "pie",
        data: {
            labels: data_label,
            datasets: [{
                data: data_value,
                backgroundColor: data_background
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {

                        // Judul tooltip (baris atas)
                        title: function (tooltipItems) {
                            return tooltipItems[0].label;
                        },


                        // Konten utama tooltip
                        label: function (context) {
                            var index_row = context.dataIndex;

                            //Akses informasinya dari datasets berdasarkan row index
                            var row_datasets = data_config.datasets[index_row];
                            var value = row_datasets[data_config.key_value];
                            var percent = Number( row_datasets.percent_amount );//Karena dateng dari backend sourcenya itu bentuknya string

                            return [
                                'Confirmed Amount : ' + formatRupiah(value),
                                'Percent of total : ' + formatPercent(percent)
                            ];
                        }
                    }
                }
            }
        }

    });

}