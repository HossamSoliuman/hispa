<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\Boat;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\FishQuantityStock;
use App\Models\Invoice;
use App\Models\PayrollModel;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // ========== إحصائيات الاشتراكات ==========
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('is_suspended', false)
            ->where('end_date', '>=', Carbon::today())
            ->count();
        $expiredSubscriptions = Subscription::where('status', 'expired')
            ->orWhere(function ($query) {
                $query->where('status', 'active')
                    ->where('end_date', '<', Carbon::today());
            })
            ->count();
        $trialSubscriptions = Subscription::where('status', 'trial')->count();
        $suspendedSubscriptions = Subscription::where('is_suspended', true)->count();

        // ========== إحصائيات الفواتير ==========
        // Check if invoices table exists
        $hasInvoicesTable = Schema::hasTable('invoices');

        $totalInvoices = $hasInvoicesTable ? Invoice::count() : 0;
        $paidInvoices = $hasInvoicesTable ? Invoice::where('payment_status', 'paid')->count() : 0;
        $pendingInvoices = $hasInvoicesTable ? Invoice::where('payment_status', 'pending')->count() : 0;
        $totalRevenue = $hasInvoicesTable ? Invoice::where('payment_status', 'paid')->sum('total_amount') : 0;
        $pendingRevenue = $hasInvoicesTable ? Invoice::where('payment_status', 'pending')->sum('total_amount') : 0;
        $pendingBankTransfers = $hasInvoicesTable ? Invoice::where('payment_method', 'bank_transfer')
            ->where('payment_status', 'pending')
            ->count() : 0;

        // ========== إحصائيات الصيادين ==========
        // المالكين من نوع "صيّاد فرد"
        $totalFishermen = User::where('role', 'owner')
            ->where('owner_type', 'fisherman')
            ->count();
        $activeFishermen = User::where('role', 'owner')
            ->where('owner_type', 'fisherman')
            ->whereHas('subscriptions', function ($query) {
                $query->where('status', 'active')
                    ->where('is_suspended', false)
                    ->where('end_date', '>=', Carbon::today());
            })
            ->count();

        // المالكين من نوع "مؤسسة / شركة"
        $totalOwnerCompanies = User::where('role', 'owner')
            ->where('owner_type', 'company')
            ->count();

        // ========== إحصائيات الرحلات ==========
        $totalTrips = Trip::count();
        $completedTrips = Trip::where('status', 8)->count(); // status 8 = completed
        $activeTrips = Trip::where('status', '!=', 8)->whereNull('deleted_at')->count();
        $cancelledTrips = Trip::whereNotNull('cancel_reason')->count();

        // ========== إحصائيات القوارب ==========
        $totalBoats = Boat::count();
        $activeBoats = Boat::where('status', 1)->count();
        $inactiveBoats = Boat::where('status', 0)->count();

        // ========== إحصائيات الكباتن ==========
        $totalCaptains = User::where('role', 'captain')->count();
        $activeCaptains = User::where('role', 'captain')->where('status', 1)->count();

        // ========== إحصائيات الطواقم ==========
        $totalCrews = User::where('role', 'crew')->count();
        $activeCrews = User::where('role', 'crew')->where('status', 1)->count();

        // ========== إحصائيات الموظفين ==========
        $totalEmployees = User::where('role', 'employee')->count();
        $activeEmployees = User::where('role', 'employee')->where('status', 1)->count();

        // ========== إحصائيات العملاء ==========
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 1)->count();

        // ========== إحصائيات الموردين ==========
        $totalVendors = User::where('role', 'vendor')->count();
        $activeVendors = User::where('role', 'vendor')->where('status', 1)->count();

        // ========== إحصائيات المصيد ==========
        $totalCatchRecords = CatchModel::count();
        $totalCatchWeight = CatchDetail::sum('weight') ?? 0;
        $totalCatchRecordsThisMonth = CatchModel::whereMonth('catch_date', Carbon::now()->month)
            ->whereYear('catch_date', Carbon::now()->year)
            ->count();

        // ========== إحصائيات المبيعات ==========
        $totalSales = Sale::count();
        $completedSales = Sale::where('status', 2)->count(); // status 2 = completed
        $totalSalesAmount = Sale::sum('total_price') ?? 0;
        $totalSalesThisMonth = Sale::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price') ?? 0;
        $totalSalesWeight = SaleDetail::sum('weight') ?? 0;

        // ========== إحصائيات النفقات ==========
        // إزالة global scope للـ owner في حالة admin
        $totalExpenses = Expense::withoutGlobalScopes()->count();
        $totalExpensesAmount = Expense::withoutGlobalScopes()->sum('final_price') ?? 0;
        $paidExpenses = Expense::withoutGlobalScopes()->where('status', 'paid')->count();
        $pendingExpenses = Expense::withoutGlobalScopes()->where('status', '!=', 'paid')->count();
        $totalExpensesThisMonth = Expense::withoutGlobalScopes()
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('final_price') ?? 0;

        // ========== إحصائيات الدفع السريع ==========
        // ملاحظة: جدول payments تم حذفه، لذلك نستخدم 0 كقيم افتراضية
        $totalQuickPayments = 0;
        $totalQuickPaymentsAmount = 0;
        $totalQuickPaymentsThisMonth = 0;

        // ========== إحصائيات الأصول ==========
        $totalAssets = Asset::count();
        $totalAssetsValue = Asset::sum('purchase_cost') ?? 0;
        $totalAssetsCurrentValue = Asset::with('depreciations')->get()->sum(function ($asset) {
            $latestDepreciation = $asset->depreciations()->latest('year')->first();

            return $latestDepreciation ? ($latestDepreciation->book_value ?? 0) : ($asset->purchase_cost ?? 0);
        });

        // ========== إحصائيات الإهلاك ==========
        $totalDepreciations = AssetDepreciation::count();
        $totalDepreciationAmount = AssetDepreciation::sum('depreciation_amount') ?? 0;
        $totalDepreciationThisYear = AssetDepreciation::where('year', Carbon::now()->year)
            ->sum('depreciation_amount') ?? 0;

        // ========== إحصائيات مخزون الأسماك ==========
        $totalFishStock = FishQuantityStock::sum('quantity') ?? 0;
        // ملاحظة: جدول fish_quantity_stocks لا يحتوي على عمود weight، فقط quantity
        $totalFishStockWeight = 0; // لا يوجد عمود weight في هذا الجدول
        $totalFishTypes = FishQuantityStock::distinct('fish_id')->count('fish_id');

        // ========== حساب الأرباح والخسارة ==========
        $totalProfit = $totalSalesAmount - $totalExpensesAmount - $totalDepreciationAmount;
        $profitMargin = $totalSalesAmount > 0 ? (($totalProfit / $totalSalesAmount) * 100) : 0;

        // ========== الإيرادات الشهرية ==========
        $currentMonthRevenue = $hasInvoicesTable ? Invoice::where('payment_status', 'paid')
            ->whereBetween('paid_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->sum('total_amount') ?? 0 : 0;

        $previousMonthRevenue = $hasInvoicesTable ? Invoice::where('payment_status', 'paid')
            ->whereBetween('paid_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ])
            ->sum('total_amount') ?? 0 : 0;

        $revenueGrowth = 0;
        if ($previousMonthRevenue > 0) {
            $revenueGrowth = (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100;
        }

        // ========== إحصائيات الباقات ==========
        $totalPackages = SubscriptionPackage::where('is_active', true)->count();

        // ========== التحليلات المالية (Admin Analytics) ==========

        // 1. MRR (Monthly Recurring Revenue) - الإيرادات الشهرية المتكررة
        // حساب الإيرادات من الاشتراكات النشطة الشهرية
        $activeMonthlySubscriptions = Subscription::where('status', 'active')
            ->where('is_suspended', false)
            ->where('end_date', '>=', Carbon::today())
            ->with('package')
            ->get();

        $mrr = 0;
        foreach ($activeMonthlySubscriptions as $subscription) {
            if ($subscription->package) {
                // حساب السعر الشهري حسب نوع المدة
                $packagePrice = $subscription->package->effective_price ?? 0;
                $durationType = $subscription->package->duration_type ?? 'monthly';

                switch ($durationType) {
                    case 'monthly':
                        $mrr += $packagePrice;
                        break;
                    case 'quarterly':
                        $mrr += $packagePrice / 3; // تقسيم على 3 أشهر
                        break;
                    case 'yearly':
                        $mrr += $packagePrice / 12; // تقسيم على 12 شهر
                        break;
                }
            }
        }

        // 2. أكثر الباقات مبيعاً
        $packageSales = Subscription::select('package_id', DB::raw('COUNT(*) as count'))
            ->whereNotNull('package_id')
            ->groupBy('package_id')
            ->orderBy('count', 'desc')
            ->with('package:id,name_ar,name_en,boats_count')
            ->get()
            ->map(function ($item) {
                return [
                    'package_id' => $item->package_id,
                    'package_name' => $item->package->name ?? 'غير معروف',
                    'boats_count' => $item->package->boats_count ?? 0,
                    'sales_count' => $item->count,
                ];
            });

        // 3. معدل الإلغاء (Churn Rate)
        // الصيادين الذين انتهت اشتراكاتهم في آخر 30 يوم ولم يجددوا
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // الصيادين الذين انتهت اشتراكاتهم في آخر 30 يوم
        $expiredSubscriptionsUsers = Subscription::where(function ($query) {
            $query->where('status', 'expired')
                ->orWhere(function ($q) {
                    $q->where('status', 'active')
                        ->where('end_date', '<', Carbon::today());
                });
        })
            ->where('end_date', '>=', $thirtyDaysAgo)
            ->where('end_date', '<=', Carbon::today())
            ->pluck('user_id')
            ->unique();

        // الصيادين الذين لم يجددوا (ليس لديهم اشتراك نشط جديد بعد انتهاء الاشتراك القديم)
        $churnedUsers = 0;
        foreach ($expiredSubscriptionsUsers as $userId) {
            $lastExpiredSubscription = Subscription::where('user_id', $userId)
                ->where(function ($query) {
                    $query->where('status', 'expired')
                        ->orWhere(function ($q) {
                            $q->where('status', 'active')
                                ->where('end_date', '<', Carbon::today());
                        });
                })
                ->where('end_date', '>=', $thirtyDaysAgo)
                ->where('end_date', '<=', Carbon::today())
                ->orderBy('end_date', 'desc')
                ->first();

            if ($lastExpiredSubscription) {
                // التحقق من عدم وجود اشتراك جديد بعد انتهاء القديم
                $hasRenewal = Subscription::where('user_id', $userId)
                    ->where('status', 'active')
                    ->where('is_suspended', false)
                    ->where('end_date', '>=', Carbon::today())
                    ->where('start_date', '>', $lastExpiredSubscription->end_date)
                    ->exists();

                if (! $hasRenewal) {
                    $churnedUsers++;
                }
            }
        }

        // إجمالي الصيادين النشطين قبل 30 يوم
        $totalActiveUsers30DaysAgo = Subscription::where('status', 'active')
            ->where('is_suspended', false)
            ->where('end_date', '>=', $thirtyDaysAgo)
            ->where('start_date', '<=', $thirtyDaysAgo)
            ->distinct('user_id')
            ->count('user_id');

        $churnRate = $totalActiveUsers30DaysAgo > 0
            ? ($churnedUsers / $totalActiveUsers30DaysAgo) * 100
            : 0;

        // بيانات MRR للآخر 6 أشهر (للرسم البياني)
        $mrrHistory = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $monthSubscriptions = Subscription::where('status', 'active')
                ->where('is_suspended', false)
                ->where(function ($query) use ($monthStart, $monthEnd) {
                    $query->whereBetween('start_date', [$monthStart, $monthEnd])
                        ->orWhere(function ($q) use ($monthStart, $monthEnd) {
                            $q->where('start_date', '<=', $monthEnd)
                                ->where('end_date', '>=', $monthStart);
                        });
                })
                ->with('package')
                ->get();

            $monthMRR = 0;
            foreach ($monthSubscriptions as $sub) {
                if ($sub->package) {
                    $packagePrice = $sub->package->effective_price ?? 0;
                    $durationType = $sub->package->duration_type ?? 'monthly';

                    switch ($durationType) {
                        case 'monthly':
                            $monthMRR += $packagePrice;
                            break;
                        case 'quarterly':
                            $monthMRR += $packagePrice / 3;
                            break;
                        case 'yearly':
                            $monthMRR += $packagePrice / 12;
                            break;
                    }
                }
            }

            $mrrHistory[] = [
                'month' => $monthStart->format('Y-m'),
                'month_label' => $monthStart->format('M Y'),
                'mrr' => round($monthMRR, 2),
            ];
        }

        // ========== تقارير الإيرادات حسب طرق الدفع ==========
        $revenueByMada = $hasInvoicesTable ? Invoice::where('payment_status', 'paid')
            ->where('payment_method', 'mada')
            ->sum('total_amount') ?? 0 : 0;

        $revenueByVisa = $hasInvoicesTable ? Invoice::where('payment_status', 'paid')
            ->where('payment_method', 'visa')
            ->sum('total_amount') ?? 0 : 0;

        $revenueByBankTransfer = $hasInvoicesTable ? Invoice::where('payment_status', 'paid')
            ->where('payment_method', 'bank_transfer')
            ->sum('total_amount') ?? 0 : 0;

        $revenueByPaymentMethod = [
            'mada' => $revenueByMada,
            'visa' => $revenueByVisa,
            'bank_transfer' => $revenueByBankTransfer,
        ];

        // ========== تنبيهات التجديد ==========
        // الاشتراكات التي ستنتهي خلال 7 أيام
        $expiringSoon = Subscription::where('status', 'active')
            ->where('is_suspended', false)
            ->whereBetween('end_date', [
                Carbon::today(),
                Carbon::today()->addDays(7),
            ])
            ->with(['user:id,name,phone', 'package:id,name'])
            ->orderBy('end_date', 'asc')
            ->limit(10)
            ->get();

        // الاشتراكات التي انتهت ولكن لم يتم تجديدها
        $expiredNotRenewed = Subscription::where(function ($query) {
            $query->where('status', 'expired')
                ->orWhere(function ($q) {
                    $q->where('status', 'active')
                        ->where('end_date', '<', Carbon::today());
                });
        })
            ->with(['user:id,name,phone', 'package:id,name'])
            ->orderBy('end_date', 'desc')
            ->limit(10)
            ->get();

        // ========== فلترة حسب نوع التعاقد ==========

        // ========== فلترة حسب نوع التعاقد ==========
        // تبسيط: حساب الصيادين الذين لديهم قوارب مع كباتن/طواقم بنوع معين
        // الصيادين الذين لديهم قوارب مع كباتن/طواقم بالراتب الثابت
        $ownersFixedSalary = User::where('role', 'owner')
            ->whereHas('boats', function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('captain', function ($subQ) {
                        $subQ->where('salary_type', 'salary');
                    })
                        ->orWhereHas('crews', function ($subQ) {
                            $subQ->where('salary_type', 'salary');
                        });
                })
                    ->whereDoesntHave('captain', function ($q) {
                        $q->where('salary_type', 'percentage');
                    })
                    ->whereDoesntHave('crews', function ($q) {
                        $q->where('salary_type', 'percentage');
                    });
            })
            ->count();

        // الصيادين الذين لديهم قوارب مع كباتن/طواقم بالنسبة
        $ownersPercentage = User::where('role', 'owner')
            ->whereHas('boats', function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('captain', function ($subQ) {
                        $subQ->where('salary_type', 'percentage');
                    })
                        ->orWhereHas('crews', function ($subQ) {
                            $subQ->where('salary_type', 'percentage');
                        });
                })
                    ->whereDoesntHave('captain', function ($q) {
                        $q->where('salary_type', 'salary');
                    })
                    ->whereDoesntHave('crews', function ($q) {
                        $q->where('salary_type', 'salary');
                    });
            })
            ->count();

        // النموذج المختلط (لديهم كباتن/طواقم بـ salary_type = 'salary' و 'percentage' معاً)
        $ownersMixed = User::where('role', 'owner')
            ->whereHas('boats', function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('captain', function ($subQ) {
                        $subQ->where('salary_type', 'salary');
                    })
                        ->orWhereHas('crews', function ($subQ) {
                            $subQ->where('salary_type', 'salary');
                        });
                })
                    ->where(function ($q) {
                        $q->whereHas('captain', function ($subQ) {
                            $subQ->where('salary_type', 'percentage');
                        })
                            ->orWhereHas('crews', function ($subQ) {
                                $subQ->where('salary_type', 'percentage');
                            });
                    });
            })
            ->count();

        // ========== فلترة الرواتب ==========
        // الموظفين الذين ينتظرون تصفية النسبة
        $pendingPercentagePayroll = PayrollModel::where('type', 'percentage')
            ->where('is_paid', 0)
            ->withCount('details')
            ->get()
            ->sum('details_count');

        // ========== إحصائيات الرحلات للفلترة ==========
        // الحصول على قائمة القوارب والكباتن الفريدة
        $boatsList = Boat::whereHas('trips')
            ->with(['owner:id,name'])
            ->select('id', 'name_ar', 'name_en', 'owner_id')
            ->get();

        $captainsList = User::where('role', 'captain')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('trips')
                    ->whereColumn('trips.captain_id', 'users.id');
            })
            ->select('id', 'name')
            ->get();

        return view('admin.dashboard.index', compact(
            // Subscriptions
            'totalSubscriptions',
            'activeSubscriptions',
            'expiredSubscriptions',
            'trialSubscriptions',
            'suspendedSubscriptions',
            // Invoices
            'totalInvoices',
            'paidInvoices',
            'pendingInvoices',
            'totalRevenue',
            'pendingRevenue',
            'pendingBankTransfers',
            // Fishermen
            'totalFishermen',
            'activeFishermen',
            // Trips
            'totalTrips',
            'completedTrips',
            'activeTrips',
            'cancelledTrips',
            // Boats
            'totalBoats',
            'activeBoats',
            'inactiveBoats',
            // Captains
            'totalCaptains',
            'activeCaptains',
            // Crews
            'totalCrews',
            'activeCrews',
            // Employees
            'totalEmployees',
            'activeEmployees',
            // Customers
            'totalCustomers',
            'activeCustomers',
            // Vendors
            'totalVendors',
            'activeVendors',
            // Catch
            'totalCatchRecords',
            'totalCatchWeight',
            'totalCatchRecordsThisMonth',
            // Sales
            'totalSales',
            'completedSales',
            'totalSalesAmount',
            'totalSalesThisMonth',
            'totalSalesWeight',
            // Expenses
            'totalExpenses',
            'totalExpensesAmount',
            'paidExpenses',
            'pendingExpenses',
            'totalExpensesThisMonth',
            // Quick Payments
            'totalQuickPayments',
            'totalQuickPaymentsAmount',
            'totalQuickPaymentsThisMonth',
            // Assets
            'totalAssets',
            'totalAssetsValue',
            'totalAssetsCurrentValue',
            // Depreciation
            'totalDepreciations',
            'totalDepreciationAmount',
            'totalDepreciationThisYear',
            // Fish Stock
            'totalFishStock',
            'totalFishStockWeight',
            'totalFishTypes',
            // Profit & Loss
            'totalProfit',
            'profitMargin',
            // Revenue Growth
            'currentMonthRevenue',
            'previousMonthRevenue',
            'revenueGrowth',
            // Packages
            'totalPackages',
            // Revenue by Payment Method
            'revenueByPaymentMethod',
            'revenueByMada',
            'revenueByVisa',
            'revenueByBankTransfer',
            // Renewal Alerts
            'expiringSoon',
            'expiredNotRenewed',
            // Contract Type Filtering
            'ownersFixedSalary',
            'ownersPercentage',
            'ownersMixed',
            // Payroll Filtering
            'pendingPercentagePayroll',
            // Trip Filtering Data
            'boatsList',
            'captainsList',
            // Admin Analytics
            'mrr',
            'packageSales',
            'churnRate',
            'churnedUsers',
            'mrrHistory'
        ));
    }

    public function statistics()
    {
        return redirect()->route('admin.dashboard');
    }
}
