<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_models', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('id');
        });

        // Backfill owner_id from the staff users referenced by each payroll's details.
        $owners = DB::table('payroll_details_models')
            ->join('users', 'payroll_details_models.user_id', '=', 'users.id')
            ->select('payroll_details_models.payroll_id', 'users.owner_id')
            ->whereNotNull('users.owner_id')
            ->get()
            ->groupBy('payroll_id');

        foreach ($owners as $payrollId => $rows) {
            $ownerId = $rows->pluck('owner_id')->filter()->first();
            if ($ownerId) {
                DB::table('payroll_models')->where('id', $payrollId)->update(['owner_id' => $ownerId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('payroll_models', function (Blueprint $table) {
            $table->dropColumn('owner_id');
        });
    }
};
