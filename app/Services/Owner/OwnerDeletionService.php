<?php

namespace App\Services\Owner;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\Boat;
use App\Models\BoatType;
use App\Models\CatchDetail;
use App\Models\CatchModel;
use App\Models\Category;
use App\Models\Contact;
use App\Models\CrewAdvance;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Expenseable;
use App\Models\Fish;
use App\Models\FishQuantityStock;
use App\Models\Governorate;
use App\Models\Inspection;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\MonthClosing;
use App\Models\MonthClosingDue;
use App\Models\PaymentMethod;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\PayrollDetailsModel;
use App\Models\PayrollModel;
use App\Models\Port;
use App\Models\Region;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Subscription;
use App\Models\Trip;
use App\Models\Unit;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OwnerDeletionService
{
    /**
     * Permanently remove all data that belongs to an owner, including records
     * that are only connected through boats, trips, catches, sales, or staff.
     */
    public function purge(User $owner): void
    {
        $staffIds = User::query()
            ->where('owner_id', $owner->id)
            ->pluck('id');
        $userIds = $staffIds->prepend($owner->id)->unique()->values();

        $boatIds = Boat::query()
            ->where('owner_id', $owner->id)
            ->pluck('id')
            ->merge(User::query()->whereIn('id', $staffIds)->whereNotNull('boat_id')->pluck('boat_id'))
            ->unique()
            ->values();
        $tripIds = Trip::query()
            ->where('owner_id', $owner->id)
            ->orWhereIn('boat_id', $boatIds)
            ->orWhereIn('captain_id', $staffIds)
            ->orWhereIn('counter_id', $staffIds)
            ->orWhereIn('dalal_id', $staffIds)
            ->pluck('id');
        $customerIds = Customer::query()
            ->where('owner_id', $owner->id)
            ->orWhereIn('dalal_id', $staffIds)
            ->pluck('id');
        $catchesQuery = CatchModel::query()
            ->where('owner_id', $owner->id)
            ->orWhereIn('trip_id', $tripIds);

        if (Schema::hasColumn('catch_models', 'boat_id')) {
            $catchesQuery->orWhereIn('boat_id', $boatIds);
        }

        $catchIds = $catchesQuery->pluck('id');

        $saleIds = Sale::query()
            ->where(function ($query) use ($userIds, $tripIds, $catchIds, $boatIds, $customerIds): void {
                $query->whereIn('seller_id', $userIds)
                    ->orWhereIn('trip_id', $tripIds)
                    ->orWhereIn('catch_id', $catchIds)
                    ->orWhereIn('boat_id', $boatIds)
                    ->orWhereIn('customer_id', $customerIds);
            })
            ->pluck('id');
        SaleDetail::query()->whereIn('sale_id', $saleIds)->delete();
        Sale::query()->whereIn('id', $saleIds)->delete();

        FishQuantityStock::query()
            ->where(function ($query) use ($tripIds, $catchIds, $boatIds): void {
                $query->whereIn('trip_id', $tripIds)
                    ->orWhereIn('catch_id', $catchIds)
                    ->orWhereIn('boat_id', $boatIds);
            })
            ->delete();
        CatchDetail::query()->whereIn('catch_id', $catchIds)->delete();
        CatchModel::query()->whereIn('id', $catchIds)->delete();
        Violation::query()->whereIn('trip_id', $tripIds)->delete();

        $expenseIds = Expense::query()
            ->where(function ($query) use ($owner, $tripIds, $boatIds): void {
                $query->where('owner_id', $owner->id)
                    ->orWhereIn('trip_id', $tripIds)
                    ->orWhereIn('boat_id', $boatIds);
            })
            ->pluck('id');
        Expenseable::query()->whereIn('expense_id', $expenseIds)->delete();
        Expense::withoutGlobalScopes()->whereIn('id', $expenseIds)->delete();

        $assetIds = Asset::query()
            ->where('owner_id', $owner->id)
            ->orWhereIn('boat_id', $boatIds)
            ->pluck('id');
        AssetDepreciation::query()->whereIn('asset_id', $assetIds)->delete();
        Asset::query()->whereIn('id', $assetIds)->delete();

        $monthClosingIds = MonthClosing::query()
            ->where('owner_id', $owner->id)
            ->orWhereIn('boat_id', $boatIds)
            ->pluck('id');
        MonthClosingDue::query()->whereIn('month_closing_id', $monthClosingIds)->delete();
        MonthClosing::query()->whereIn('id', $monthClosingIds)->delete();

        $payrollIds = Payroll::query()
            ->where('owner_id', $owner->id)
            ->orWhereIn('boat_id', $boatIds)
            ->pluck('id');
        PayrollDetail::query()
            ->whereIn('payroll_id', $payrollIds)
            ->orWhereIn('user_id', $staffIds)
            ->delete();
        Payroll::query()->whereIn('id', $payrollIds)->delete();

        $payrollModelIds = PayrollModel::query()->where('owner_id', $owner->id)->pluck('id');
        PayrollDetailsModel::query()
            ->whereIn('payroll_id', $payrollModelIds)
            ->orWhereIn('user_id', $staffIds)
            ->delete();
        PayrollModel::query()->whereIn('id', $payrollModelIds)->delete();

        CrewAdvance::query()
            ->where('owner_id', $owner->id)
            ->orWhereIn('user_id', $staffIds)
            ->delete();
        Maintenance::withoutGlobalScopes()
            ->where('owner_id', $owner->id)
            ->orWhereIn('boat_id', $boatIds)
            ->delete();
        Inspection::withoutGlobalScopes()
            ->where('owner_id', $owner->id)
            ->orWhereIn('boat_id', $boatIds)
            ->delete();

        Trip::query()->whereIn('id', $tripIds)->delete();
        Boat::query()->whereIn('id', $boatIds)->delete();

        $subscriptionIds = Subscription::query()->where('user_id', $owner->id)->pluck('id');
        Invoice::query()
            ->where('user_id', $owner->id)
            ->orWhereIn('subscription_id', $subscriptionIds)
            ->delete();
        Subscription::query()->whereIn('id', $subscriptionIds)->delete();

        Customer::query()->whereIn('id', $customerIds)->delete();

        $this->purgeStartupRecords($owner);

        PaymentMethod::query()->where('owner_id', $owner->id)->delete();
        Fish::query()->where('owner_id', $owner->id)->delete();
        Unit::query()->where('owner_id', $owner->id)->delete();
        Category::query()->where('owner_id', $owner->id)->delete();
        BoatType::query()->where('owner_id', $owner->id)->delete();
        Port::query()->where('owner_id', $owner->id)->delete();
        Governorate::query()->where('owner_id', $owner->id)->delete();
        Region::query()->where('owner_id', $owner->id)->delete();

        $this->purgeUserRecords($userIds);

        User::query()->whereIn('id', $staffIds)->delete();
    }

    /**
     * Remove non-relational records that Eloquent cannot cascade automatically.
     *
     * @param  Collection<int, int>  $userIds
     */
    private function purgeUserRecords(Collection $userIds): void
    {
        Contact::query()->whereIn('user_id', $userIds)->delete();

        $morphType = (new User)->getMorphClass();

        DB::table('notifications')
            ->where('notifiable_type', $morphType)
            ->whereIn('notifiable_id', $userIds)
            ->delete();
        DB::table('personal_access_tokens')
            ->where('tokenable_type', $morphType)
            ->whereIn('tokenable_id', $userIds)
            ->delete();
        DB::table('model_has_permissions')
            ->where('model_type', $morphType)
            ->whereIn('model_id', $userIds)
            ->delete();
        DB::table('model_has_roles')
            ->where('model_type', $morphType)
            ->whereIn('model_id', $userIds)
            ->delete();
        DB::table('sessions')->whereIn('user_id', $userIds)->delete();
    }

    /**
     * Delete the startup module from the leaves upward because some of its
     * foreign keys intentionally restrict deletion of their parent records.
     */
    private function purgeStartupRecords(User $owner): void
    {
        DB::table('startup_loan_payments')->where('owner_id', $owner->id)->delete();
        DB::table('startup_contributions')->where('owner_id', $owner->id)->delete();
        DB::table('startup_expenses')->where('owner_id', $owner->id)->delete();
        DB::table('startup_loans')->where('owner_id', $owner->id)->delete();
        DB::table('startup_partners')->where('owner_id', $owner->id)->delete();
        DB::table('startup_projects')->where('owner_id', $owner->id)->delete();
        DB::table('startup_expense_categories')->where('owner_id', $owner->id)->delete();
    }
}
