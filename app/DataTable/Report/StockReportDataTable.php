<?php

namespace App\DataTable\Report;

use App\Models\CatchDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\DataTables;

class StockReportDataTable extends DataTables
{
    /**
     * تقرير المخزون: يستخدم fish_stocks إن وُجد، وإلا catch_details (مع fish و catch_models).
     */
    public function getData(Request $request)
    {
        if (Schema::hasTable('fish_stocks')) {
            return $this->getDataFromFishStocks($request);
        }
        if (Schema::hasTable('catch_details')) {
            return $this->getDataFromCatchDetails($request);
        }

        return $this->emptyResponse();
    }

    private function emptyResponse()
    {
        return DataTables::of(collect([]))
            ->addIndexColumn()
            ->addColumn('name', fn () => '---')
            ->addColumn('total_weight', fn () => '0 '.__('admin.units.kg'))
            ->addColumn('added_by', fn () => '---')
            ->addColumn('weight_captain', fn () => '0 '.__('admin.units.kg'))
            ->addColumn('weight_counter', fn () => '0 '.__('admin.units.kg'))
            ->addColumn('weight_difference', fn () => '<span class="text-muted">0 '.__('admin.units.kg').'</span>')
            ->addColumn('correct_by', fn () => '---')
            ->addColumn('date', fn () => '---')
            ->with(['total_fish_count' => 0, 'totalWeight' => '0 '.__('admin.units.kg')])
            ->rawColumns(['weight_difference'])
            ->make(true);
    }

    /**
     * جلب البيانات من catch_details + fish + catch_models (جداول موجودة).
     */
    private function getDataFromCatchDetails(Request $request)
    {
        $query = CatchDetail::query()
            ->selectRaw(
                'catch_details.fish_id,
                fish.scientific_name,
                MAX(addedUser.name) as added_by_name,
                MAX(catch_details.created_at) as created_at,
                SUM(catch_details.weight) as total_weight'
            )
            ->join('fish', 'catch_details.fish_id', '=', 'fish.id')
            ->leftJoin('catch_models', 'catch_details.catch_id', '=', 'catch_models.id')
            ->leftJoin('users as addedUser', 'catch_models.owner_id', '=', 'addedUser.id')
            ->groupBy('catch_details.fish_id', 'fish.scientific_name')
            ->orderByDesc('total_weight');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('catch_details.created_at', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('fish_type')) {
            $query->where('catch_details.fish_id', $request->fish_type);
        }

        $data = $query->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('name', fn ($row) => $row->scientific_name ?? '---')
            ->addColumn('total_weight', fn ($row) => number_format((float) $row->total_weight, 2).__('admin.units.kg'))
            ->addColumn('added_by', fn ($row) => $row->added_by_name ?? '---')
            ->addColumn('weight_captain', fn ($row) => number_format((float) $row->total_weight, 2).__('admin.units.kg'))
            ->addColumn('weight_counter', fn ($row) => number_format((float) $row->total_weight, 2).__('admin.units.kg'))
            ->addColumn('weight_difference', fn ($row) => '<span class="text-muted">0 '.__('admin.units.kg').'</span>')
            ->addColumn('correct_by', fn ($row) => '---')
            ->addColumn('date', function ($row) {
                if (! $row->created_at) {
                    return '---';
                }
                try {
                    return formatHijriDate($row->created_at, 'dd/MM/yyyy HH:mm');
                } catch (\Throwable $e) {
                    return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d h:i A');
                }
            })
            ->with([
                'total_fish_count' => $data->count(),
                'totalWeight' => number_format($data->sum('total_weight'), 2).__('admin.units.kg'),
            ])
            ->rawColumns(['added_by', 'correct_by', 'total_weight', 'weight_difference'])
            ->make(true);
    }

    /**
     * جلب البيانات من fish_stocks عند توفر الجدول.
     */
    private function getDataFromFishStocks(Request $request)
    {
        $query = \App\Models\FishStock::selectRaw(
            'fish_stocks.fish_id, fish.scientific_name,
         addedUser.name as added_by_name,
         correctUser.name as correct_by_name,
         fish_stocks.created_at,
        MAX(fish_stocks.weight_captain) AS weight_captain,
    MAX(fish_stocks.weight_counter) AS weight_counter,
         SUM(fish_stocks.weight) as total_weight'
        )
            ->join('fish', 'fish_stocks.fish_id', '=', 'fish.id')
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
            ->addColumn('total_weight', fn ($row) => number_format($row->total_weight, 2).__('admin.units.kg'))
            ->addColumn('added_by', fn ($row) => $row->added_by_name ?? '---')
            ->addColumn('weight_captain', fn ($row) => number_format($row->weight_captain ?? 0, 2).__('admin.units.kg'))
            ->addColumn('weight_counter', fn ($row) => number_format($row->weight_counter ?? 0, 2).__('admin.units.kg'))
            ->addColumn('weight_difference', function ($row) {
                $diff = ($row->weight_counter ?? 0) - ($row->weight_captain ?? 0);
                $color = $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : 'text-muted');

                return '<span class="'.$color.'">'.number_format($diff, 2).' '.__('admin.units.kg').'</span>';
            })
            ->addColumn('correct_by', fn ($row) => $row->correct_by_name ?? '---')
            ->addColumn('date', function ($row) {
                if (! $row->created_at) {
                    return '---';
                }
                try {
                    return formatHijriDate($row->created_at, 'dd/MM/yyyy HH:mm');
                } catch (\Throwable $e) {
                    return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d h:i A');
                }
            })
            ->with([
                'total_fish_count' => $data->count(),
                'totalWeight' => number_format($data->sum('total_weight'), 2).__('admin.units.kg'),
            ])
            ->rawColumns(['added_by', 'correct_by', 'total_weight', 'weight_difference'])
            ->make(true);
    }
}
