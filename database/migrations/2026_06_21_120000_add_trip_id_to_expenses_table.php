<?php

use App\Models\Expense;
use App\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('trip_id')->nullable()->after('boat_id')->constrained('trips')->nullOnDelete();
        });

        // Backfill: quick-trip expenses were stamped with the trip number in their
        // notes (e.g. "… - TR-2026-004"). Link them to their trip so per-trip cost
        // totals are computed from a real relation going forward.
        Trip::withoutGlobalScopes()->select('id', 'number')->chunkById(200, function ($trips) {
            foreach ($trips as $trip) {
                if (empty($trip->number)) {
                    continue;
                }

                Expense::withoutGlobalScopes()
                    ->whereNull('trip_id')
                    ->where('notes', 'like', '%- '.$trip->number)
                    ->update(['trip_id' => $trip->id]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['trip_id']);
            $table->dropColumn('trip_id');
        });
    }
};
