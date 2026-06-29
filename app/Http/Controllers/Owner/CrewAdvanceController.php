<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CrewAdvanceRequest;
use App\Models\CrewAdvance;
use Illuminate\Http\RedirectResponse;

class CrewAdvanceController extends Controller
{
    /**
     * Record a cash advance (سلفة) for a crew member / captain / employee.
     */
    public function store(CrewAdvanceRequest $request): RedirectResponse
    {
        CrewAdvance::create([
            'user_id' => $request->integer('user_id'),
            'owner_id' => (int) auth()->id(),
            'amount' => $request->input('amount'),
            'date' => $request->date('date'),
            'notes' => $request->input('notes'),
        ]);

        return redirect()->back()->with('success', __('owner.crew_advances.created'));
    }

    /**
     * Delete an advance (owner-scoped to prevent cross-tenant deletion).
     */
    public function destroy(CrewAdvance $crewAdvance): RedirectResponse
    {
        abort_if($crewAdvance->owner_id !== (int) auth()->id(), 403);

        $crewAdvance->delete();

        return redirect()->back()->with('success', __('owner.crew_advances.deleted'));
    }
}
