<?php

namespace App\DataTable;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class OwnerDataTable extends DataTables
{
    private const ACTIVE_STATUS = 1;

    public function getData(Request $request): JsonResponse
    {
        if (! $request->ajax()) {
            return response()->json([], 400);
        }

        $query = User::OwnerRole()
            ->withCount(['boats', 'subscriptions'])
            ->with([
                'region',
                'governorate',
                'port',
                'activeSubscription',
                'boats' => fn ($q) => $q->with(['captain:id,salary_type,boat_id', 'crews:id,salary_type,boat_id']),
            ])
            ->orderBy('created_at', 'desc');

        // دعم البحث سواء من فورم مخصص أو من DataTables نفسها
        if ($request->filled('search') || $request->filled('search.value')) {
            $searchInput = $request->input('search');

            if (is_array($searchInput)) {
                $term = $searchInput['value'] ?? '';
            } else {
                $term = $searchInput;
            }

            if ($term !== null && $term !== '') {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            }
        }

        // فلتر نوع المالك: صياد فرد / مؤسسة
        if ($request->filled('owner_type') && in_array($request->owner_type, ['fisherman', 'company'], true)) {
            $query->where('owner_type', $request->owner_type);
        }

        $this->applyPayrollFilter($query, $request);

        $data = $query->get();
        $total_count = $data->count();
        $active_count = $data->where('status', self::ACTIVE_STATUS)->count();
        $inactive_count = $total_count - $active_count;
        $fixed_count = $this->getOwnersCountByPayrollType('fixed');
        $percentage_count = $this->getOwnersCountByPayrollType('percentage');
        $fisherman_count = $data->where('owner_type', 'fisherman')->count();
        $company_count = $data->where('owner_type', 'company')->count();

        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function (User $user) {
                    $logoUrl = $user->logo ? asset($user->logo) : asset('default-logo.png');
                    $profileUrl = route('admin.owner.show', $user->id);

                    return '<div class="d-flex align-items-center justify-content-center">
                        <a href="' . e($profileUrl) . '" class="d-flex align-items-center text-decoration-none text-dark">
                            <img src="' . e($logoUrl) . '" alt="logo" width="32" height="32" class="rounded-circle me-2 object-fit-cover">
                            <span class="fw-medium">' . e($user->name) . '</span>
                        </a>
                    </div>';
                })
                ->addColumn('phone', function (User $user) {
                    $phone = $user->phone ?? '--';
                    return $phone !== '--' ? '<a href="tel:' . e($phone) . '" class="text-decoration-none">' . e($phone) . '</a>' : '<span class="text-muted">--</span>';
                })
                ->addColumn('email', function (User $user) {
                    $email = $user->email ?? '--';
                    return $email !== '--' ? '<a href="mailto:' . e($email) . '" class="text-decoration-none">' . e(Str::limit($email, 25)) . '</a>' : '<span class="text-muted">--</span>';
                })
                ->addColumn('region', function (User $user) {
                    return e($user->region->name ?? '--');
                })
                ->addColumn('governorate', function (User $user) {
                    return e($user->governorate->name ?? '--');
                })
                ->addColumn('port', function (User $user) {
                    return e($user->port->name ?? '--');
                })
                ->addColumn('boats_count', function (User $user) {
                    $count = $user->boats_count ?? 0;
                    return '<span class="badge bg-primary">' . (int) $count . '</span>';
                })
                ->addColumn('owner_type', function (User $user) {
                    // إذا لم يتم تحديد نوع المالك نعرض شرطة فقط
                    if (empty($user->owner_type)) {
                        return '-';
                    }

                    $type = $user->owner_type;
                    $label = $type === 'company' ? 'مؤسسة / شركة' : 'صيّاد (فرد)';
                    $badgeType = $type === 'company' ? 'dark' : 'success';

                    return $this->badgeHtml($badgeType, $label);
                })
                ->addColumn('payroll_type', function (User $user) {
                    return $this->getPayrollTypeBadge($user);
                })
                ->addColumn('subscription_status', function (User $user) {
                    if ($user->relationLoaded('activeSubscription') && $user->activeSubscription) {
                        return $this->badgeHtml('success', __('admin.owner.subscription_active'));
                    }
                    $hasAny = ($user->subscriptions_count ?? 0) > 0;
                    if ($hasAny) {
                        return '<span class="badge bg-warning text-dark">' . e(__('admin.owner.subscription_expired')) . '</span>';
                    }

                    return $this->badgeHtml('secondary', __('admin.owner.subscription_none'));
                })
                ->addColumn('registered_at', function (User $user) {
                    return $user->created_at?->format('Y-m-d') ?? '--';
                })
                ->addColumn('status', function (User $user) {
                    return $user->status === self::ACTIVE_STATUS
                        ? $this->badgeHtml('success', __('admin.status.active'))
                        : $this->badgeHtml('danger', __('admin.status.inactive'));
                })
                ->addColumn('action', function (User $user) {
                    return $this->buildActionButtons($user);
                })
                ->with([
                    'total_count' => $total_count,
                    'active_count' => $active_count,
                    'inactive_count' => $inactive_count,
                    'fixed_count' => $fixed_count,
                    'percentage_count' => $percentage_count,
                    'fisherman_count' => $fisherman_count,
                    'company_count' => $company_count,
                ])
                ->rawColumns(['action', 'status', 'name', 'phone', 'email', 'boats_count', 'payroll_type', 'subscription_status', 'owner_type'])
                ->make(true);
    }

    private function applyPayrollFilter($query, Request $request): void
    {
        $payrollType = $request->input('payroll_type', 'all');
        if ($payrollType === 'all' || $payrollType === '') {
            return;
        }
        if ($payrollType === 'fixed') {
            $this->scopeFixedSalaryOnly($query);
        }
        if ($payrollType === 'percentage') {
            $this->scopePercentageOnly($query);
        }
    }

    private function scopeFixedSalaryOnly($query): void
    {
        $query->whereHas('boats', function ($q) {
            $q->where(function ($subQ) {
                $subQ->whereHas('captain', fn ($c) => $c->where('salary_type', 'salary'))
                    ->orWhereHas('crews', fn ($c) => $c->where('salary_type', 'salary'));
            })
                ->whereDoesntHave('captain', fn ($c) => $c->where('salary_type', 'percentage'))
                ->whereDoesntHave('crews', fn ($c) => $c->where('salary_type', 'percentage'));
        });
    }

    private function scopePercentageOnly($query): void
    {
        $query->whereHas('boats', function ($q) {
            $q->where(function ($subQ) {
                $subQ->whereHas('captain', fn ($c) => $c->where('salary_type', 'percentage'))
                    ->orWhereHas('crews', fn ($c) => $c->where('salary_type', 'percentage'));
            })
                ->whereDoesntHave('captain', fn ($c) => $c->where('salary_type', 'salary'))
                ->whereDoesntHave('crews', fn ($c) => $c->where('salary_type', 'salary'));
        });
    }

    private function getOwnersCountByPayrollType(string $type): int
    {
        $query = User::OwnerRole();
        if ($type === 'fixed') {
            $this->scopeFixedSalaryOnly($query);
        } elseif ($type === 'percentage') {
            $this->scopePercentageOnly($query);
        }

        return $query->count();
    }

    private function getPayrollTypeFromOwner(User $user): string
    {
        if (! $user->relationLoaded('boats')) {
            return 'none';
        }

        $hasFixedBoat = false;
        $hasPercentageBoat = false;

        foreach ($user->boats as $boat) {
            $hasFixed = $this->boatHasSalaryType($boat, 'salary');
            $hasPercentage = $this->boatHasSalaryType($boat, 'percentage');
            if ($hasFixed && $hasPercentage) {
                return 'mixed';
            }
            if ($hasFixed) {
                $hasFixedBoat = true;
            }
            if ($hasPercentage) {
                $hasPercentageBoat = true;
            }
        }

        if ($hasFixedBoat && $hasPercentageBoat) {
            return 'mixed';
        }
        if ($hasFixedBoat) {
            return 'fixed';
        }
        if ($hasPercentageBoat) {
            return 'percentage';
        }

        return 'none';
    }

    private function boatHasSalaryType($boat, string $type): bool
    {
        $captain = $boat->relationLoaded('captain') ? $boat->captain : null;
        $crews = $boat->relationLoaded('crews') ? $boat->crews : collect();

        if ($captain && ($captain->salary_type ?? '') === $type) {
            return true;
        }
        foreach ($crews as $crew) {
            if (($crew->salary_type ?? '') === $type) {
                return true;
            }
        }

        return false;
    }

    private function getPayrollTypeBadge(User $user): string
    {
        $type = $this->getPayrollTypeFromOwner($user);
        if ($type === 'mixed') {
            $type = 'none';
        }
        $label = match ($type) {
            'fixed' => __('admin.owner.payroll_fixed'),
            'percentage' => __('admin.owner.payroll_percentage'),
            default => __('admin.owner.payroll_none'),
        };
        $badgeType = match ($type) {
            'fixed' => 'info',
            'percentage' => 'primary',
            default => 'secondary',
        };

        return $this->badgeHtml($badgeType, $label);
    }

    private function badgeHtml(string $type, string $label): string
    {
        return '<span class="badge bg-' . e($type) . '">' . e($label) . '</span>';
    }

    private function buildActionButtons(User $user): string
    {
        $admin = auth('admin')->user();
        if (! $admin) {
            return '<span class="text-muted">--</span>';
        }

        $showUrl = route('admin.owner.show', $user->id);
        $stockUrl = route('admin.owner-stock.show', $user->id);
        $editUrl = route('admin.owner.edit', $user->id);

        $buttons = '';

        // عرض التفاصيل (يُظهر لجميع المشرفين)
        $buttons .= '<a href="' . e($showUrl) . '" class="btn btn-info btn-sm" title="' . e(__('admin.owner.action_view_details')) . '"><i class="fas fa-eye"></i></a> ';

        // تعديل
        $buttons .= '<a href="' . e($editUrl) . '" class="btn btn-primary btn-sm" title="' . e(__('admin.actions.edit')) . '"><i class="fas fa-edit"></i></a> ';

        // قائمة منسدلة: القوارب، الاشتراكات، الرحلات، المبيعات، العملاء
        $buttons .= '<div class="btn-group dropstart d-inline">
            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" title="' . e(__('admin.owner.action_more')) . '" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="' . e($showUrl) . '#boats-pane"><i class="fas fa-ship me-2"></i>' . e(__('admin.owner.boats_tab')) . '</a></li>
                <li><a class="dropdown-item" href="' . e($showUrl) . '#subscriptions-pane"><i class="fas fa-box me-2"></i>' . e(__('admin.owner.subscriptions_tab')) . '</a></li>
                <li><a class="dropdown-item" href="' . e($showUrl) . '#trips-pane"><i class="fas fa-route me-2"></i>' . e(__('admin.owner.trips_tab')) . '</a></li>
                <li><a class="dropdown-item" href="' . e($showUrl) . '#sales-pane"><i class="fas fa-receipt me-2"></i>' . e(__('admin.owner.sales_tab')) . '</a></li>
                <li><a class="dropdown-item" href="' . e($showUrl) . '#customers-pane"><i class="fas fa-users me-2"></i>' . e(__('admin.owner.customers_tab')) . '</a></li>
                <li><a class="dropdown-item" href="' . e($stockUrl) . '"><i class="fas fa-boxes-stacked me-2"></i>' . e(__('admin.owner.action_stock')) . '</a></li>
            </ul>
        </div> ';

        // حذف
        $buttons .= '<a href="#" onclick="deleteRecord(' . (int) $user->id . ')" class="btn btn-danger btn-sm" title="' . e(__('admin.actions.delete')) . '"><i class="fas fa-trash"></i></a>';

        return $buttons ?: '<span class="text-muted">--</span>';
    }

    public function showData($id)
    {

        $query = Sale::with(['trip', 'details'])
            ->where('seller_id', $id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('invoice_number', fn($row) => $row->number)
            ->addColumn('trip_name', fn($row) => optional($row->trip)->name ?? '---')
            ->addColumn('customer_name', fn($row) => $row->customer_name ?? '---')
            ->addColumn('sale_date', fn($row) => $row->created_at ? $row->created_at->format('Y-m-d H:i') : '---')
            ->addColumn('items_count', fn($row) => $row->details->count())
            ->addColumn('payment_method', function ($row) {
                $color = match ($row->payment_method) {
                    'نقدي' => 'success',
                    'شبكة' => 'info',
                    'شيك' => 'warning',
                    default => 'secondary',
                };

                return '<span class="badge bg-' . $color . '">' . e($row->payment_method ?? '---') . '</span>';
            })
            ->addColumn('items_count', function ($row) {
                return '<a href="#" class="show-sale-details" data-sale-id="' . $row->id . '">' . $row->details->count() . '</a>';
            })
            ->rawColumns(['items_count'])
            ->addColumn('total_weight', function ($row) {
                return '<span class="text-primary fw-bold">' . number_format($row->details->sum('weight'), 2) . ' كغم</span>';
            })
            ->addColumn('total_price', function ($row) {
                return '<span class="text-success fw-bold">' . number_format($row->total_price, 2) . ' ر.س</span>';
            })
            ->rawColumns(['total_weight', 'total_price', 'payment_method', 'items_count']) // إذا كنت تستخدم HTML
            ->make(true);
    }
}
