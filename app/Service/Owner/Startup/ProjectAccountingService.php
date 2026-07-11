<?php

namespace App\Service\Owner\Startup;

use App\Models\Startup\Partner;
use App\Models\Startup\Project;

class ProjectAccountingService
{
    /** @return array<string, float|int|string> */
    public function summary(Project $project): array
    {
        $totalCost = (float) $project->expenses()->sum('amount');
        $sharedCost = (float) $project->expenses()->where('is_shared', true)->sum('amount');
        $loansTotal = (float) $project->loans()->sum('amount');
        $loansPaid = (float) $project->loans()->withSum('payments', 'amount')->get()->sum('payments_sum_amount');

        return [
            'total_cost' => $totalCost,
            'project_expenses' => $totalCost,
            'shared_cost' => $sharedCost,
            'contributions' => (float) $project->contributions()->sum('amount'),
            'loans_total' => $loansTotal,
            'loans_paid' => $loansPaid,
            'loans_remaining' => max(0, $loansTotal - $loansPaid),
            'partners_count' => $project->partners()->count(),
            'status' => $project->status,
        ];
    }

    /** @return array{required: float, paid: float, balance: float} */
    public function partner(Project $project, Partner $partner): array
    {
        $required = (float) $project->expenses()->where('is_shared', true)->sum('amount') * (float) $partner->share_percent / 100;
        $paid = (float) $partner->contributions()->sum('amount') + (float) $partner->expenses()->where('project_id', $project->id)->where('is_shared', true)->where('payer_type', 'partner')->sum('amount');
        $paid += (float) $partner->loanPayments()->whereHas('loan', fn ($query) => $query->where('project_id', $project->id)->where('borne_by', 'project'))->sum('amount');

        return ['required' => $required, 'paid' => $paid, 'balance' => $paid - $required];
    }
}
