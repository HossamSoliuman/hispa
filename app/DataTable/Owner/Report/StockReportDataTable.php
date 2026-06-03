<?php

namespace App\DataTable\Owner\Report;

use App\Models\FishStock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class StockReportDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $owner_id = auth()->user()->id;

        $query = FishStock::selectRaw(
            'fish_stocks.fish_id, fish.scientific_name,
         addedUser.name as added_by_name,
         correctUser.name as correct_by_name,
         fish_stocks.created_at,
         MAX(fish_stocks.weight_captain) AS weight_captain,
         MAX(fish_stocks.weight_counter) AS weight_counter,
         SUM(fish_stocks.weight) as total_weight'
        )
            ->join('fish', 'fish_stocks.fish_id', '=', 'fish.id')
            ->join('trips', 'trips.id', '=', 'fish_stocks.trip_id')
            ->where('trips.owner_id', $owner_id)
            ->leftJoin('users as addedUser', 'fish_stocks.added_by', '=', 'addedUser.id')
            ->leftJoin('users as correctUser', 'fish_stocks.corrected_by', '=', 'correctUser.id')
            ->groupBy('fish_stocks.fish_id', 'fish.scientific_name', 'addedUser.name', 'correctUser.name', 'fish_stocks.created_at')
            ->orderByDesc('total_weight');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('fish_stocks.created_at', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('fish_type')) {
            $query->where('fish_stocks.fish_id', $request->fish_type);
        }

        $data = $query->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('name', fn ($row) => $row->scientific_name ?? '---')
            ->addColumn('total_weight', fn ($row) => number_format($row->total_weight, 2).' كغم')
            ->addColumn('added_by', fn ($row) => $row->added_by_name ?? '---')
            ->addColumn('weight_captain', fn ($row) => number_format($row->weight_captain ?? 0, 2).' كغم')
            ->addColumn('weight_counter', fn ($row) => number_format($row->weight_counter ?? 0, 2).' كغم')
            ->addColumn('weight_difference', function ($row) {
                $diff = ($row->weight_counter ?? 0) - ($row->weight_captain ?? 0);
                $color = $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : 'text-muted');

                return '<span class="'.$color.'">'.number_format($diff, 2).' كجم</span>';
            })
            ->addColumn('correct_by', fn ($row) => $row->correct_by_name ?? '---')
            ->addColumn('date', function ($row) {
                if (! $row->created_at) {
                    return '---';
                }
                try {
                    // Try to format Hijri using IntlDateFormatter (same approach as @hijri Blade directive)
                    if (class_exists('\\IntlDateFormatter')) {
                        $pattern = app()->getLocale() === 'ar' ? 'd MMM yyyy' : 'd MMM yyyy';
                        $fmt = new \IntlDateFormatter(
                            app()->getLocale() === 'ar' ? 'ar_SA@calendar=islamic' : 'en_US@calendar=islamic',
                            \IntlDateFormatter::MEDIUM,
                            \IntlDateFormatter::NONE,
                            null,
                            \IntlDateFormatter::TRADITIONAL,
                            $pattern
                        );
                        $hijriOut = $fmt->format(new \DateTime($row->created_at));
                        $hijri = $hijriOut !== false ? $hijriOut : (new \DateTime($row->created_at))->format('Y-m-d');
                    } else {
                        $hijri = (new \DateTime($row->created_at))->format('Y-m-d');
                    }

                    $time = Carbon::parse($row->created_at)->format('h:i A');

                    return $hijri.' '.$time;
                } catch (\Exception $e) {
                    return Carbon::parse($row->created_at)->format('Y-m-d h:i A');
                }
            })
            ->with([
                'total_fish_count' => $data->count(),
                // return raw numeric totals so frontend can format and choose units (kg / ton)
                'totalWeight' => round($data->sum('total_weight'), 2),
                'total_records' => $data->count(),
                'total_difference' => round($data->sum(function ($row) {
                    return ($row->weight_counter ?? 0) - ($row->weight_captain ?? 0);
                }), 2),
            ])
            ->rawColumns(['added_by', 'correct_by', 'total_weight', 'weight_difference'])
            ->make(true);
    }
}
