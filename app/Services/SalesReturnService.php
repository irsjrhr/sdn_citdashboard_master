<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use PDO;

class SalesReturnService
{
    private function formatDataset($rows)
    {
        $numericFields = [
            'TotalSales',
            'TotalReturns',
            'ReturnRate',
            'ReturnRatio',
            'ReturnValue',
            'ReturnRatePercent',
            'SalesContributionPercent',
            'ReturnContributionPercent'
        ];

        foreach ($rows as &$row) {
            foreach ($numericFields as $field) {
                if (isset($row[$field])) {
                    $row[$field] = formatAbbreviatedNumber($row[$field]);
                }
            }
        }

        return $rows;
    }

    public function getHighLevelOverview($filters)
    {
        $sdndwh = DB::connection('sqlsrv-sdndwh');

        // Extract parameters
        $params = [
            'regionCode'          => $filters['region'] ?? null,
            'branchCode'          => $filters['branch'] ?? null,
            'distributionChannel' => $filters['distributionChannel'] ?? null,
            'businessType'        => $filters['businessType'] ?? null,
            'principalCode'       => $filters['principalCode'] ?? null,
            'startDate'           => $filters['startDate'] ?? null,
            'endDate'             => $filters['endDate'] ?? null,
            'orderBy'             => $filters['sortBy'] ?? null,
            'sortDirection'       => $filters['orderBy'] ?? null,
        ];
        // dd($params);

        // Use PDO so we can fetch multiple datasets
        $pdo = $sdndwh->getPdo();

        $stmt = $pdo->prepare("
            EXEC sp_PortalSDN_GetSalesReturnHighLevelOverview
                @regionCode = :regionCode,
                @branchCode = :branchCode,
                @distributionChannel = :distributionChannel,
                @businessType = :businessType,
                @principalCode = :principalCode,
                @startDate = :startDate,
                @endDate = :endDate,
                @orderBy = :orderBy,
                @sortDirection = :sortDirection;
        ");

        $stmt->execute($params);

        // Fetch all datasets
        $datasets = [];
        do {
            $datasets[] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());

        // dd($datasets);

        // Map according to your FINAL mockup SP
        return [
            'highLevel'                 => $this->formatDataset($datasets[0] ?? []),
            'trend'                     => $datasets[1] ?? [],
            'topBranches'               => $datasets[2] ?? [],
            'topSKUs'                   => $datasets[3] ?? [],
            'topSalesman'               => $datasets[4] ?? [],
            'topStores'                 => $datasets[5] ?? [],
            'topDistributionChannels'   => $datasets[6] ?? [],
            'topRegions'                => $datasets[7] ?? [],
            'topPrincipals'             => $datasets[8] ?? [],
            'topBusinessTypes'          => $datasets[9] ?? [],
        ];

    }

    public function getHeaderAnalysis($dimension, $filters)
    {
        $sdndwh = DB::connection('sqlsrv-sdndwh');
        $pdo    = $sdndwh->getPdo();

        $params = [
            'dimension'           => $dimension,
            'distributionChannel' => $filters['distributionChannel'] ?? null,
            'principalCode'       => $filters['principalCode'] ?? null,
            'regionCode'          => $filters['region'] ?? null,
            'branchCode'          => $filters['branch'] ?? null,
            'businessType'        => $filters['businessType'] ?? null,
            'startDate'           => $filters['startDate'] ?? null,
            'endDate'             => $filters['endDate'] ?? null,
            'sortBy'              => $filters['sortBy'] ?? 'TotalReturns',
            'sortDirection'       => $filters['orderBy'] ?? 'DESC',
            'page'                => $filters['page'] ?? 1,
            'pageSize'            => $filters['pageSize'] ?? 100,
        ];

        $sql = "
            EXEC sp_PortalSDN_GetSalesReturnHeaderAnalysis
                @dimension           = :dimension,
                @distributionChannel = :distributionChannel,
                @principalCode       = :principalCode,
                @region              = :regionCode,
                @branchCode          = :branchCode,
                @businessType        = :businessType,
                @startDate           = :startDate,
                @endDate             = :endDate,
                @sortBy              = :sortBy,
                @sortDirection       = :sortDirection,
                @page                = :page,
                @pageSize            = :pageSize;
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $datasets = [];
        do {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result !== false) {
                $datasets[] = $result;
            }
        } while ($stmt->nextRowset());

        $summaryRow = $datasets[0][0] ?? null;
        $top10      = $datasets[1]    ?? [];
        $rows       = $datasets[2]    ?? [];

        $totalRows  = 0;
        if (isset($datasets[3][0]['TotalRows'])) {
            $totalRows = (int) $datasets[3][0]['TotalRows'];
        } else {
            $totalRows = count($rows);
        }

        return [
            'summary'   => $summaryRow,
            'top10'     => $top10,
            'rows'      => $rows,
            'totalRows' => $totalRows,
        ];
    }



    public function getDetailAnalysis($filters)
    {
        $sdndwh = DB::connection('sqlsrv-sdndwh');
        $pdo = $sdndwh->getPdo();

        $params = [
            'dimension'           => $filters['dimension'],
            'code'                => $filters['code'],
            'startDate'           => $filters['startDate'] ?? null,
            'endDate'             => $filters['endDate'] ?? null,
            'distributionChannel' => $filters['distributionChannel'] ?? null,
            'principalCode'       => $filters['principalCode'] ?? null,
            'businessType'        => $filters['businessType'] ?? null,
            'regionCode'          => $filters['region'] ?? null,
            'branchCode'          => $filters['branch'] ?? null,
            'sortBy'              => $filters['sortBy'] ?? 'TotalReturns',
            'sortDirection'       => $filters['orderBy'] ?? 'DESC',

            // NEW pagination params
            'branchesPage'        => $filters['branchesPage'] ?? 1,
            'storesPage'          => $filters['storesPage'] ?? 1,
            'salesmenPage'        => $filters['salesmenPage'] ?? 1,
            'skusPage'            => $filters['skusPage'] ?? 1,
            'pageSize'            => $filters['pageSize'] ?? 25,
        ];
        

        $stmt = $pdo->prepare("
            EXEC sp_PortalSDN_GetSalesReturnDetailAnalysis
                @dimension           = :dimension,
                @code                = :code,
                @startDate           = :startDate,
                @endDate             = :endDate,
                @distributionChannel = :distributionChannel,
                @principalCode       = :principalCode,
                @businessType        = :businessType,
                @region              = :regionCode,
                @branchCode          = :branchCode,
                @sortBy              = :sortBy,
                @sortDirection       = :sortDirection,
                @branchesPage        = :branchesPage,
                @storesPage          = :storesPage,
                @salesmenPage        = :salesmenPage,
                @skusPage            = :skusPage,
                @pageSize            = :pageSize;
        ");

        $stmt->execute($params);

        // Read ALL result sets
        $datasets = [];
        do {
            $datasets[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());

        return [
            'overview'      => $datasets[0][0] ?? [],
            'trend'         => $datasets[1] ?? [],

            // Stores
            'stores_total'  => $datasets[2][0]['TotalStores'] ?? 0,
            'stores_rows'   => $datasets[3] ?? [],

            // Salesmen
            'salesmen_total'=> $datasets[4][0]['TotalSalesmen'] ?? 0,
            'salesmen_rows' => $datasets[5] ?? [],

            // Skus
            'skus_total'    => $datasets[6][0]['TotalSkus'] ?? 0,
            'skus_rows'     => $datasets[7] ?? [],

            // Branches
            'branches_total'=> $datasets[8][0]['TotalBranches'] ?? 0,
            'branches_rows' => $datasets[9] ?? [],
        ];
    }
}