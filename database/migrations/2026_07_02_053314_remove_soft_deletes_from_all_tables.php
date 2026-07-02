<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that previously used soft deletes. Deletes are now permanent, so any
     * row already flagged as soft-deleted is purged for good and the `deleted_at`
     * column is dropped everywhere.
     *
     * @var array<int, string>
     */
    private array $tables = [
        'sales',
        'maintenances',
        'expenses',
        'categories',
        'payment_methods',
        'units',
        'fish',
        'trips',
        'payroll_details',
        'payrolls',
        'subscription_packages',
        'coupons',
        'invoices',
        'subscriptions',
        'admins',
        'expenseables',
    ];

    public function up(): void
    {
        Schema::withoutForeignKeyConstraints(function (): void {
            foreach ($this->tables as $table) {
                if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'deleted_at')) {
                    continue;
                }

                DB::table($table)->whereNotNull('deleted_at')->delete();

                Schema::table($table, function (Blueprint $blueprint): void {
                    $blueprint->dropSoftDeletes();
                });
            }
        });
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->softDeletes();
            });
        }
    }
};
