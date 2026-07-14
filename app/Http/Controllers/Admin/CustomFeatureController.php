<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CustomFeature;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchCustomFeatureOwnerRequest;
use App\Http\Requests\Admin\StoreCustomFeatureAccessRequest;
use App\Models\CustomFeatureAccess;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomFeatureController extends Controller
{
    public function index(): View
    {
        $features = collect(CustomFeature::cases())->map(function (CustomFeature $feature): array {
            $accesses = CustomFeatureAccess::query()->forFeature($feature);

            return [
                'feature' => $feature,
                'active_count' => (clone $accesses)->where('status', 'active')->count(),
                'paused_count' => (clone $accesses)->where('status', 'paused')->count(),
            ];
        });

        return view('admin.custom-features.index', compact('features'));
    }

    public function show(Request $request, CustomFeature $feature): View
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        $accesses = CustomFeatureAccess::query()
            ->forFeature($feature)
            ->with(['user:id,name,email,phone,status', 'grantedByAdmin:id,name'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('user', function ($userQuery) use ($search): void {
                    $userQuery->where(function ($searchQuery) use ($search): void {
                        $searchQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                });
            })
            ->when(in_array($status, ['active', 'paused'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $activeCount = CustomFeatureAccess::query()->forFeature($feature)->where('status', 'active')->count();
        $pausedCount = CustomFeatureAccess::query()->forFeature($feature)->where('status', 'paused')->count();

        return view('admin.custom-features.show', compact('feature', 'accesses', 'activeCount', 'pausedCount'));
    }

    public function store(StoreCustomFeatureAccessRequest $request, CustomFeature $feature): RedirectResponse
    {
        $owner = User::query()
            ->where('role', 'owner')
            ->where('email', $request->validated('email'))
            ->firstOrFail();

        CustomFeatureAccess::query()->updateOrCreate(
            ['user_id' => $owner->id, 'feature' => $feature->value],
            [
                'status' => 'active',
                'paused_at' => null,
                'granted_by_admin_id' => auth('admin')->id(),
            ],
        );

        return back()->with('success', __('admin.custom_features.messages.access_granted', ['name' => $owner->name]));
    }

    public function searchOwners(SearchCustomFeatureOwnerRequest $request, CustomFeature $feature): JsonResponse
    {
        $search = $request->validated('query');

        $owners = User::query()
            ->ownerRole()
            ->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->whereDoesntHave('customFeatureAccesses', fn ($query) => $query->forFeature($feature))
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json(['owners' => $owners]);
    }

    public function pause(CustomFeature $feature, CustomFeatureAccess $access): RedirectResponse
    {
        $this->ensureAccessMatchesFeature($access, $feature);

        $access->update(['status' => 'paused', 'paused_at' => now()]);

        return back()->with('success', __('admin.custom_features.messages.access_paused'));
    }

    public function resume(CustomFeature $feature, CustomFeatureAccess $access): RedirectResponse
    {
        $this->ensureAccessMatchesFeature($access, $feature);

        $access->update(['status' => 'active', 'paused_at' => null]);

        return back()->with('success', __('admin.custom_features.messages.access_resumed'));
    }

    public function destroy(CustomFeature $feature, CustomFeatureAccess $access): RedirectResponse
    {
        $this->ensureAccessMatchesFeature($access, $feature);
        $access->delete();

        return back()->with('success', __('admin.custom_features.messages.access_deleted'));
    }

    private function ensureAccessMatchesFeature(CustomFeatureAccess $access, CustomFeature $feature): void
    {
        abort_unless($access->feature === $feature, 404);
    }
}
