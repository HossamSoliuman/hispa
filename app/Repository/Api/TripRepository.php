<?php

namespace App\Repository\Api;

use App\Enums\TripStatus;
use App\Http\Resources\TripResource;
use App\Interfaces\CRUD;
use App\Models\Trip;
use App\Services\TripTransitionService;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TripRepository implements CRUD
{
    use RespondsWithHttpStatus;

    public function getList($request)
    {
        $role = request()->user()->role;

        $status = request()->input('status');
        $search = request()->input('search');
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        if (is_string($status) && str_starts_with($status, '[')) {
            $status = json_decode($status, true);
        }

        switch ($role) {
            case 'captain':
                $data = Trip::CaptainId();
                break;

            case 'counter':
                $data = Trip::CounterId();
                break;

            case 'owner':
                $data = Trip::OwnerId();
                break;

            case 'dalal':
                $data = Trip::DalalId();
                break;

            default:
                $data = Trip::query();
                break;
        }

        $data = $data->when($status, function ($query, $status) {
            if (is_array($status)) {
                return $query->whereIn('status', $status);
            }

            return $query->where('status', $status);
        });

        $data = $data->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%");
            });
        });

        $data = $data->when($startDate, function ($query, $startDate) {
            return $query->whereDate('start_date', '>=', $startDate);
        });

        $data = $data->when($endDate, function ($query, $endDate) {
            return $query->whereDate('end_date', '<=', $endDate);
        });

        $data = $data->orderByDesc('created_at')->paginate(10);

        return $this->success(
            trans('site.getData'),
            paginationResult(TripResource::collection($data)),
            200
        );
    }

    public function getDetail($id)
    {
        $user = request()->user();
        $query = Trip::query();

        switch ($user->role) {
            case 'captain':
                $query->where('captain_id', $user->id);
                break;

            case 'counter':
                $query->where('counter_id', $user->id);
                break;

            case 'owner':
                $query->where('owner_id', $user->id);
                break;

            case 'dalal':
                $query->where('dalal_id', $user->id);
                break;

            default:
                return $this->success(trans('site.not_authorization'), [], 403);
        }

        $trip = $query->find($id);
        if (! $trip) {
            return $this->failure(trans('site.page_not_found'), [], 404);
        }

        return $this->success(trans('site.getData'), new TripResource($trip), 200);
    }

    public function saveData($request)
    {
        // TODO: Implement saveData() method.
    }

    public function updateData($request, $id)
    {
        try {
            $newStatus = (int) $request->status;
            $cancelReason = $request->cancel_reason ?? null;
            $targetStatus = TripStatus::tryFrom($newStatus);

            if (! $targetStatus) {
                return $this->failure(trans('api.status_not_authorized'), [], 403);
            }

            $service = new TripTransitionService;

            return DB::transaction(function () use ($id, $targetStatus, $cancelReason, $service) {
                $trip = Trip::where('id', $id)->lockForUpdate()->first();

                if (! $trip) {
                    throw new ModelNotFoundException(trans('api.trip_not_found'));
                }

                $service->transition($trip, $targetStatus, $cancelReason);

                return $this->success(trans('api.trip_updated'), new TripResource($trip->fresh()), 200);
            });

        } catch (ModelNotFoundException $e) {
            return $this->failure(trans('api.trip_not_found'), [], 404);
        } catch (\DomainException $e) {
            return $this->failure($e->getMessage(), [], 422);
        } catch (\Throwable $e) {
            return $this->failure(trans('api.error'), [], 500);
        }
    }

    public function deleteData($id)
    {
        // TODO: Implement deleteData() method.
    }
}
