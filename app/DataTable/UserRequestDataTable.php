<?php

namespace App\DataTable;

use App\Models\UserRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserRequestDataTable extends DataTables
{
    public function getData(Request $request)
    {
        $requests = UserRequest::with('user')->select('user_requests.*');
        $data = $requests->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('user_name', function ($request) {
                return $request->user->name ?? '-';
            })
            ->editColumn('status', function ($request) {
                if ($request->status == 'approved') {
                    return '<span class="badge bg-success">'.__('admin.user_request.accepted').'</span>';
                } elseif ($request->status == 'rejected') {
                    return '<span class="badge bg-danger">'.__('admin.user_request.rejected').'</span>';
                } else {
                    return '<span class="badge bg-warning">'.__('admin.user_request.pending').'</span>';
                }
            })
            ->addColumn('fields_summary', function ($request) {
                $fieldNames = [
                    'name' => __('admin.user_request.name'),
                    'address' => __('admin.user_request.address'),
                    'email' => __('admin.user_request.email'),
                    'phone' => __('admin.user_request.phone'),
                    'boat_name' => __('admin.user_request.boat_name'),
                    'boat_number' => __('admin.user_request.boat_number'),
                    'nationality' => __('admin.user_request.nationality'),
                    'crew_count' => __('admin.user_request.crew_count'),
                    'city_id' => __('admin.user_request.city_id'),
                    'region_id' => __('admin.user_request.region_id'),
                    'governorate_id' => __('admin.user_request.governorate_id'),
                    'tax_number' => __('admin.user_request.tax_number'),
                    'record_number' => __('admin.user_request.record_number'),
                    'id_number' => __('admin.user_request.id_number'),
                    'port_id' => __('admin.user_request.port_id'),
                ];

                $keys = array_keys($request->data);
                $summary = implode('، ', array_map(fn ($key) => __('admin.actions.edit').' '.($fieldNames[$key] ?? $key), $keys));

                return $summary ?: '-';
            })
            ->editColumn('attachment', function ($request) {
                if ($request->attachment != null) {
                    return '<a href="'.asset($request->attachment).'" target="_blank" class="btn btn-outline-info btn-sm"> '.__('admin.actions.show').'</a>';
                }

                return '-';
            })
            ->addColumn('actions', function ($request) {
                if ($request->status == 'pending') {
                    if (auth()->user()->can('update_user_request')) {

                        $approveRoute = route('admin.requests.approve', $request->id);
                        $rejectRoute = route('admin.requests.reject', $request->id);

                        return '
                    <form action="'.$approveRoute.'" method="POST" style="display:inline-block;">
                        '.csrf_field().'
                        <button type="submit" class="btn btn-success btn-sm">'.__('admin.user_request.approve').'</button>
                    </form>
                    <form action="'.$rejectRoute.'" method="POST" style="display:inline-block;">
                        '.csrf_field().'
                        <button type="submit" class="btn btn-danger btn-sm">'.__('admin.user_request.reject').'</button>
                    </form>
                ';
                    }
                }

            })
            ->rawColumns(['status', 'attachment', 'actions'])
            ->make(true);
    }
}
