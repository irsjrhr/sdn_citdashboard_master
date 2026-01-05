<?php

namespace App\Services;

class UserMetricFilterService
{
    public static function getUserAccessScrope(string $userTitle, string $userBranchCode): string
    {
        return str_contains($userTitle, 'Region') ? 'Region'    // REGIONAL USER
             : ($userBranchCode !== 'D3101' ? 'Branch'          // BRANCH USER (non-HO)
             : 'Head Office');                                  // HO / SUPERUSER
    }
    public static function getFilters(
        $connection,
        string $userTitle,
        string $userBranchCode,
        int $userRegion
    ): array {

        $branchQuery   = $connection->table('mst_branches');
        $regionQuery   = $connection->table('mst_branches');
        $locationQuery = $connection->table('mst_branches');

        // REGIONAL USER
        if (str_contains($userTitle, 'Regional')) {

            return [
                'regions' => $regionQuery
                    ->select('region')
                    ->where('region', 'like', '%' . $userRegion . '%')
                    ->distinct()
                    ->orderBy('region')
                    ->get(),

                'branches' => $branchQuery
                    ->select('territory_code', 'branch_name')
                    ->where('region', 'like', '%' . $userRegion . '%')
                    ->orderBy('branch_name')
                    ->get(),

                'locations' => $locationQuery
                    ->select('location')
                    ->where('region', 'like', '%' . $userRegion . '%')
                    ->distinct()
                    ->orderBy('location')
                    ->get(),
            ];
        }

        // BRANCH USER (non-HO)
        if ($userBranchCode !== 'D3101') {

            return [
                'regions' => $regionQuery
                    ->select('region')
                    ->where('territory_code', $userBranchCode)
                    ->distinct()
                    ->get(),

                'branches' => $branchQuery
                    ->select('territory_code', 'branch_name')
                    ->where('territory_code', $userBranchCode)
                    ->get(),

                'locations' => $locationQuery
                    ->select('location')
                    ->where('territory_code', $userBranchCode)
                    ->distinct()
                    ->get(),
            ];
        }

        // HO / SUPERUSER
        return [
            'regions' => $regionQuery
                ->select('region')
                ->distinct()
                ->orderBy('region')
                ->get(),

            'branches' => $branchQuery
                ->select('territory_code', 'branch_name')
                ->orderBy('branch_name')
                ->get(),

            'locations' => $locationQuery
                ->select('location')
                ->distinct()
                ->orderBy('location')
                ->get(),
        ];
    }

    public static function applyUserDefaultFilters(
        array $filters,
        string $userTitle,
        string $userBranchCode,
        ?string $userRegion
    ): array {
        // 1️⃣ Enforce access scope
        $scope = self::getUserAccessScrope($userTitle, $userBranchCode);

        if ($scope === 'Branch') {
            $filters['branch'] = $userBranchCode;
            $filters['region'] = null;
        }

        if ($scope === 'Region') {
            $filters['region'] = self::normalizeRegion($userRegion);
        }

        // 2️⃣ Normalize region format
        if (!empty($filters['region'])) {
            $filters['region'] = self::normalizeRegion($filters['region']);
        }

        return $filters;
    }

    private static function normalizeRegion($region): ?string
    {
        if (preg_match('/(\d+)/', (string) $region, $m)) {
            return 'Region ' . $m[1];
        }
        return null;
    }
}
