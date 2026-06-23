<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Owner\OwnerMasterDataService;
use Illuminate\Database\Seeder;

class OwnerMasterDataSeeder extends Seeder
{
    public function __construct(private readonly OwnerMasterDataService $masterDataService) {}

    /**
     * Seed the default master data (ports, fish, units, payment methods,
     * categories, ...) for every existing owner. Idempotent per owner.
     */
    public function run(): void
    {
        User::where('role', 'owner')->orderBy('id')->each(function (User $owner): void {
            $this->masterDataService->seedFor($owner);
        });
    }
}
