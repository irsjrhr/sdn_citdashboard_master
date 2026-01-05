document.addEventListener('DOMContentLoaded', function() {
    const d = window.chartData;

    // 🎨 Global Color Palette (consistent across all charts)
    const colors = {
        primary: '#1E88E5',      // blue
        secondary: '#007bff',    // lighter blue
        success: '#2ecc71',      // green
        warning: '#f1c40f',      // yellow
        danger: '#e74c3c',       // red
        info: '#00bcd4',         // cyan
        neutral: '#95a5a6',      // grey
        dark: '#003366',         // dark navy
        orange: '#e67e22'        // orange for aging
    };

    // 🧭 Global Chart Defaults
    Chart.defaults.color = colors.dark;
    Chart.defaults.font.family = 'Segoe UI, sans-serif';
    Chart.defaults.plugins.title.color = colors.dark;

    // === Tickets by Type ===
    new Chart(document.getElementById('typeChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(d.ticketsByType),
            datasets: [{
                label: 'Tickets',
                data: Object.values(d.ticketsByType),
                backgroundColor: colors.secondary
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Tickets by Type' }},
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // === Tickets by Department ===
    new Chart(document.getElementById('deptChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(d.ticketsByDept),
            datasets: [{
                label: 'Tickets',
                data: Object.values(d.ticketsByDept),
                backgroundColor: colors.primary
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Tickets by Department' }},
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true } }
        }
    });

    // === Monthly Trend ===
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: Object.keys(d.monthlyTrend).map(k => {
                const monthIndex = parseInt(k.split('-')[1], 10) - 1;
                return new Date(0, monthIndex).toLocaleString('default', { month: 'short' });
            }),
            datasets: [{
                label: 'Tickets',
                data: Object.values(d.monthlyTrend),
                fill: false,
                borderColor: colors.primary,
                tension: 0.2
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Monthly Trend' }},
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // === Tickets by Category ===
    new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(d.ticketsByCategory),
            datasets: [{
                label: 'Tickets',
                data: Object.values(d.ticketsByCategory),
                backgroundColor: [
                    colors.primary, colors.secondary, colors.success,
                    colors.warning, colors.danger, colors.info, colors.neutral
                ]
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Tickets by Category' }},
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // === Tickets by Product ===
    new Chart(document.getElementById('productChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(d.ticketsByProduct),
            datasets: [{
                label: 'Tickets',
                data: Object.values(d.ticketsByProduct),
                backgroundColor: colors.secondary
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Tickets by Product' }},
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // === SLA Compliance ===
    new Chart(document.getElementById('slaChart'), {
        type: 'doughnut',
        data: {
            labels: [
                `Within SLA (${d.sla.withinPct}%)`,
                `Breached SLA (${d.sla.breachPct}%)`
            ],
            datasets: [{
                label: 'Tickets',
                data: [d.sla.withinCount, d.sla.breachCount],
                backgroundColor: [colors.success, colors.danger],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'SLA Compliance'
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            return `${ctx.parsed} tickets`;
                        }
                    }
                },
                legend: { position: 'bottom' }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%' // donut hole size
        }
    });

    // === YTD Priority Chart ===
    new Chart(document.getElementById('priorityYTDChart'), {
        type: 'line',
        data: {
            // 🔹 Convert '2025-01' -> 'Jan', '2025-02' -> 'Feb', etc.
            labels: Object.keys(d.highPriorityYTDTrend).map(k => {
                const monthIndex = parseInt(k.split('-')[1], 10) - 1;
                return new Date(0, monthIndex).toLocaleString('default', { month: 'short' });
            }),
            datasets: [{
                label: 'High Priority Tickets',
                data: Object.values(d.highPriorityYTDTrend),
                fill: false,
                borderColor: colors.primary,
                tension: 0.2
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'High Priority YTD Trend' },
                legend: { position: 'bottom' }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });


    // === Priority Chart (Dynamic Coloring) ===
    new Chart(document.getElementById('priorityChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(d.ticketsByPriority),
            datasets: [{
                label: 'Tickets',
                data: Object.values(d.ticketsByPriority),
                backgroundColor: Object.keys(d.ticketsByPriority).map(priority => {
                    if (priority.toLowerCase().includes('high')) return colors.danger;   // red
                    if (priority.toLowerCase().includes('medium')) return colors.warning; // yellow
                    if (priority.toLowerCase().includes('low')) return colors.success;   // green
                    return colors.primary;
                })
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Tickets by Priority' }},
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // === Acknowledgment Chart ===
    new Chart(document.getElementById('ackChart'), {
        type: 'doughnut',
        data: {
            labels: [
                `Acknowledged (${d.acknowledgement.ackPct}%)`,
                `Unacknowledged (${d.acknowledgement.unackPct}%)`
            ],
            datasets: [{
                label: 'Acknowledgement',
                data: [
                    d.acknowledgement.acknowledgedCount,
                    d.acknowledgement.unacknowledgedCount
                ],
                backgroundColor: [colors.success, colors.danger],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Ticket Acknowledgement' },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            return `${ctx.parsed} tickets`;
                        }
                    }
                },
                legend: { position: 'bottom' }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%'
        }
    });


    // === Aging Tickets Chart ===
    new Chart(document.getElementById('agingChart'), {
        type: 'bar',
        data: {
            labels: ['0–7 Days', '8–14 Days', '15–30 Days', '> 30 Days'],
            datasets: [{
                label: 'Open Tickets',
                data: [
                    d.aging['7d'],
                    d.aging['14d'],
                    d.aging['30d'],
                    d.aging['moreThan30d']
                ],
                backgroundColor: [
                    colors.success, // 0–7 days
                    colors.warning, // 8–14 days
                    colors.orange,  // 15–30 days
                    colors.danger   // >30 days
                ],
                borderRadius: 8,
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Outstanding & Aging Tickets' },
                legend: { display: false }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Tickets Count' },
                    ticks: { stepSize: 5 }
                },
                x: {
                    title: { display: true, text: 'Aging Range' }
                }
            }
        }
    });

    // === Baseline Issue Tickets Chart ===
    new Chart(document.getElementById('baselineIssueChart'), {
        type: 'bar',
        data: {
            labels: ['Analysis', 'Dev', 'UAT', 'Deployment'],
            datasets: [{
                label: 'Baseline Issue Tickets',
                data: [
                    d.baselineIssue['analysis'],
                    d.baselineIssue['dev'],
                    d.baselineIssue['uat'],
                    d.baselineIssue['deployment']
                ],
                backgroundColor: [
                    colors.danger,   // 0-7 days
                    colors.orange,  // 8-14 days
                    colors.warning, // 15-30 days
                    colors.success, // >30 days
                ],
                borderRadius: 8,
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Baseline Issue Tickets' },
                legend: { display: false }
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Tickets Count' },
                    ticks: { stepSize: 5 }
                },
                x: {
                    title: { display: true, text: 'Status' }
                }
            }
        }
    });

    new Chart(document.getElementById('baselineAgingChart'), {
        type: 'bar',
        data: {
            labels: ['0–15 Days', '16–30 Days', '31–45 Days', '>45 Days'],
            datasets: [{
                label: 'Ticket Count',
                data: [
                    d.baselineAging['15d'],
                    d.baselineAging['30d'],
                    d.baselineAging['45d'],
                    d.baselineAging['moreThan45d']
                ],
                backgroundColor: [
                    colors.success, // 0-15 days
                    colors.warning, // 16-30 days
                    colors.orange,  // 31-45 days
                    colors.danger,  // >45 days
                ],
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: {
                    label: ctx => `${ctx.parsed.y} tickets`
                }}
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Tickets' } },
                x: { title: { display: true, text: 'Aging Bucket' } }
            }
        }
    });
});