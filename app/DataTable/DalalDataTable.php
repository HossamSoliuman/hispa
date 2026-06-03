<?php

namespace App\DataTable;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DalalDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = User::DalalRole()->orderBy('created_at', 'desc');

            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function (User $user) {
                    $logoUrl = $user->logo
                        ? asset($user->logo)
                        : asset('default-logo.png'); // صورة افتراضية
                    $profileUrl = '#';
                    // رابط الصفحة (تغيير حسب رابط صفحة البروفايل عندك)
                    if (auth()->user()->can('read_dalal')) {

                        $profileUrl = route('admin.dalal.show', $user->id); // لو ثابتة صفحة صاحب الجلسة
                    }
                    // أو لو لكل مستخدم رابط خاص:
                    // $profileUrl = route('users.show', $user->id);

                    return '<div class="d-flex align-items-center">
        <a href="'.$profileUrl.'" class="d-flex align-items-center text-decoration-none">
            <img src="'.$logoUrl.'" alt="logo" width="30" height="30" class="rounded-circle me-2">
            <span>'.e($user->name).'</span>
        </a>
    </div>';
                })
                ->addColumn('commission', function (User $user) {
                    $commissionSetting = $user->commissionSetting;

                    if (! $commissionSetting) {
                        return '<span class="badge bg-secondary">'.__('admin.dalal.no_commission_data').'</span>';
                    }

                    return
                        '<span class="badge bg-success"><i class="fas fa-percent"></i> '.__('admin.dalal.commission').': '
                        .number_format($commissionSetting->commission_rate, 2).'%</span><br>'.
                        '<span class="badge bg-warning text-dark"><i class="fas fa-wrench"></i> '.__('admin.dalal.labor').': '
                        .number_format($commissionSetting->labor_rate, 2).'%</span>';
                })
                ->addColumn('region', function (User $user) {
                    return $user->region->name ?? '--';
                })
                ->addColumn('governorate', function (User $user) {
                    return $user->governorate->name ?? '--';
                })
                //                ->addColumn('city', function (User $user) {
                //                    return $user->city->name ?? "--";
                //                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success">'.__('admin.status.active').'</span>';
                    } else {
                        return '<span class="badge bg-danger">'.__('admin.status.inactive').'</span>';
                    }
                })
                ->addColumn('action', function (User $user) {
                    $btn = '';
                    if (auth()->user()->can('update_dalal')) {

                        // زر التعديل
                        $btn .= '<a  href="'.route('admin.dalal.edit', $user->id).'"
        class="edit btn btn-primary btn-sm editBtn" title="تعديل">
        <i class="fas fa-edit"></i>
    </a> ';
                    }
                    if (auth()->user()->can('delete_dalal')) {

                        // زر الحذف
                        $btn .= '<a href="#" onclick="deleteRecord('.$user->id.')"
        class="btn btn-danger btn-sm" title="حذف">
        <i class="fas fa-trash"></i>
    </a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'name', 'region', 'governorate', 'commission']) // تأكد أن status أيضًا يحتوي على HTML مثل badges
                ->make(true);
        }
    }

    public function showData($id)
    {
        $query = Sale::with(['trip', 'details'])
            ->where('seller_id', $id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('invoice_number', fn ($row) => $row->number)
            ->addColumn('customer_name', fn ($row) => $row->customer_name ?? '---')
            ->addColumn('payment_method', function ($row) {
                $color = match ($row->payment_method) {
                    'نقدي' => 'success',
                    'شبكة' => 'info',
                    'شيك' => 'warning',
                    default => 'secondary',
                };

                return '<span class="badge bg-'.$color.'">'
                    .__('admin.dalal.payment_methods.'.$row->payment_method).'</span>';
                // return '<span class="badge bg-' . $color . '">' . e($row->payment_method ?? '---') . '</span>';
            })
            ->addColumn('sale_date', fn ($row) => $row->created_at?->format('Y-m-d H:i') ?? '---')
            ->addColumn('items_count', function ($row) {
                return '<a href="#" class="show-sale-details" data-sale-id="'.$row->id.'">'.$row->details->count().'</a>';
            })
            ->addColumn('total_weight', function ($row) {
                $weight = $row->details->sum('weight');

                return '<span class="text-primary fw-bold">'.number_format($weight, 2).' '.__('admin.units.kg').'</span>';
            })
            ->addColumn('price_per_kilo', function ($row) {
                $weight = $row->details->sum('weight');
                if ($weight > 0) {
                    $avgPrice = $row->total_price / $weight;

                    return number_format($avgPrice, 2).' '.__('admin.units.sar').'/ '.__('admin.units.kg');
                }

                return '---';
            })
            ->addColumn('total_price', function ($row) {
                return '<span class="text-success fw-bold">'.number_format($row->total_price, 2).' '.__('admin.units.sar').'</span>';
            })
            ->addColumn('commission_amount', function ($row) {
                return number_format($row->commission_amount, 2).' '.__('admin.units.sar');
            })
            ->addColumn('labor_amount', function ($row) {
                return number_format($row->labor_amount, 2).' '.__('admin.units.sar');
            })
            ->addColumn('net_owner_amount', function ($row) {
                return number_format($row->net_owner_amount, 2).' '.__('admin.units.sar');
            })
            ->addColumn('remaining_total', function ($row) {
                return number_format($row->remaining_total, 2).' '.__('admin.units.sar');
            })
            ->rawColumns([
                'payment_method',
                'items_count',
                'total_weight',
                'total_price',
                'price_per_kilo',
            ])
            ->make(true);
    }
}
