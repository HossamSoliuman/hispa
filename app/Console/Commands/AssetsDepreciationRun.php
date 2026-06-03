<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use Illuminate\Console\Command;

class AssetsDepreciationRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assets-depreciation-run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $assets = Asset::where('status', 'active')->get();

        foreach ($assets as $asset) {
            $lastDep = $asset->depreciations()->latest('year')->first();
            $startYear = $lastDep ? $lastDep->year + 1 : (int) date('Y', strtotime($asset->purchase_date));
            $currentYear = date('Y');

            for ($year = $startYear; $year <= $currentYear; $year++) {
                if ($asset->depreciation_method == 'straight_line') {
                    $depAmount = ($asset->purchase_cost - $asset->salvage_value) / $asset->useful_life_years;
                } else { // percentage
                    $accumulated = $lastDep ? $lastDep->accumulated_depreciation : 0;
                    $depAmount = ($asset->purchase_cost - $accumulated) * ($asset->depreciation_rate / 100);
                }

                $accumulatedDep = $lastDep ? $lastDep->accumulated_depreciation + $depAmount : $depAmount;
                $bookValue = $asset->purchase_cost - $accumulatedDep;

                $lastDep = AssetDepreciation::create([
                    'asset_id' => $asset->id,
                    'year' => $year,
                    'depreciation_amount' => $depAmount,
                    'accumulated_depreciation' => $accumulatedDep,
                    'book_value' => $bookValue,
                ]);
            }
        }
    }
}
