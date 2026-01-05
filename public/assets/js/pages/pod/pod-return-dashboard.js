document.addEventListener('DOMContentLoaded', () => {
    // Distribution Channel chart
    if (Array.isArray(window.distributionChannelData) && window.distributionChannelData.length > 0) {
        const labels = window.distributionChannelData.map(r => r['Channel']);
        const data = window.distributionChannelData.map(r => Number(r['Total Rejection Order']));

        donutChart({
            canvasId: 'distributionChannelChart',
            labels: labels,
            data: data,
            valueFormatter: v => `${v.toLocaleString()} Orders`
        });
    }

    // Reason chart
    if (Array.isArray(window.reasonChartData) && window.reasonChartData.length > 0) {
        const labels = window.reasonChartData.map(r => r['Return Reason']);
        const data = window.reasonChartData.map(r => Number(r['Total Rejection Order']));

        barChart({
            canvasId: 'reasonChart',
            labels: labels,
            data: data,
            label: 'Total Rejection Order',
            yAxisLabel: 'Rejection Count'
        });
    }
});
