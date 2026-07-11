<?php

namespace App\Service\Owner\Startup;

use App\Models\Startup\Partner;
use App\Models\Startup\Project;
use Illuminate\Validation\ValidationException;

class OwnershipAllocationService
{
    private const REQUIRED_BASIS_POINTS = 10000;

    public function totalBasisPoints(Project $project, ?Partner $except = null, mixed $replacementShare = null): int
    {
        $shares = $project->partners()
            ->when($except, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->pluck('share_percent');

        if ($replacementShare !== null) {
            $shares->push($replacementShare);
        }

        return $shares->sum(fn (mixed $share): int => $this->toMinorUnits($share));
    }

    public function isComplete(Project $project): bool
    {
        return $this->totalBasisPoints($project) === self::REQUIRED_BASIS_POINTS;
    }

    public function percentage(Project $project): string
    {
        return number_format($this->totalBasisPoints($project) / 100, 2, '.', '');
    }

    public function ensureComplete(Project $project): void
    {
        if (! $this->isComplete($project)) {
            throw ValidationException::withMessages([
                'ownership' => __('owner.startup.validation.shares_incomplete'),
            ]);
        }
    }

    public function hasFinancialActivity(Project $project): bool
    {
        return $project->expenses()->exists()
            || $project->contributions()->exists()
            || $project->loans()->exists();
    }

    private function toMinorUnits(mixed $value): int
    {
        $normalized = number_format((float) $value, 2, '.', '');
        [$whole, $fraction] = explode('.', $normalized);

        return ((int) $whole * 100) + (int) $fraction;
    }
}
