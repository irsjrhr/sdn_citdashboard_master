Chart.register(ChartDataLabels);

const {
    trendData,
    topDistributionChannels,
    topRegions,
    topPrincipals,
    topBusinessTypes,
    topBranches,
    topSKUs,
    topSalesman,
    topStores
} = window.dashboardData;

function initChart(chartId, renderFn) {
    if (!document.getElementById(chartId)) return;
    renderFn();
}

// ========= TREND =========
initChart("trendChart", () => {
    trendChart("trendChart", trendData);
});

// ========= TOP CHARTS =========
initChart("distributionChannelChart", () => {
    clusteredChart(
        "distributionChannelChart",
        topDistributionChannels.map(x => x.DistChannel),
        topDistributionChannels.map(x => x.TotalSales),
        topDistributionChannels.map(x => x.TotalReturns)
    );
});

initChart("businessTypeChart", () => {
    clusteredChart(
        "businessTypeChart",
        topBusinessTypes.map(x => x.BusinessType),
        topBusinessTypes.map(x => x.TotalSales),
        topBusinessTypes.map(x => x.TotalReturns)
    );
});

initChart("regionChart", () => {
    clusteredChart(
        "regionChart",
        topRegions.map(x => x.RegionCode),
        topRegions.map(x => x.TotalSales),
        topRegions.map(x => x.TotalReturns)
    );
});

initChart("branchChart", () => {
    clusteredChart(
        "branchChart",
        topBranches.map(x => x.BranchName),
        topBranches.map(x => x.TotalSales),
        topBranches.map(x => x.TotalReturns)
    );
});

initChart("storeChart", () => {
    clusteredChart(
        "storeChart",
        topStores.map(x => x.StoreName + "-" + x.BranchCode),
        topStores.map(x => x.TotalSales),
        topStores.map(x => x.TotalReturns)
    );
});

initChart("salesmanChart", () => {
    clusteredChart(
        "salesmanChart",
        topSalesman.map(x => x.SalesmanName + "-" + x.BranchCode),
        topSalesman.map(x => x.TotalSales),
        topSalesman.map(x => x.TotalReturns)
    );
});

initChart("principalChart", () => {
    clusteredChart(
        "principalChart",
        topPrincipals.map(x => x.PrincipalName),
        topPrincipals.map(x => x.TotalSales),
        topPrincipals.map(x => x.TotalReturns)
    );
});

initChart("skuChart", () => {
    clusteredChart(
        "skuChart",
        topSKUs.map(x => x.SKUName),
        topSKUs.map(x => x.TotalSales),
        topSKUs.map(x => x.TotalReturns)
    );
});
