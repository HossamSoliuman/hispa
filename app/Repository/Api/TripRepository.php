<?php

namespace App\Repository\Api;

use App\Http\Resources\TripResource;
use App\Interfaces\CRUD;
use App\Models\FishStock;
use App\Models\Trip;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TripRepository implements CRUD
{
    use RespondsWithHttpStatus;

    public function getList($request)
    {
        $role = request()->user()->role;

        // استلام جميع الفلاتر
        $status = request()->input('status'); // يقبل array أو single
        $search = request()->input('search');
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        if (is_string($status) && str_starts_with($status, '[')) {
            $status = json_decode($status, true);
        }
        // اختيار الرحلات حسب نوع المستخدم
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
            $user = $request->user();
            $role = $user->role;
            $newStatus = (int) $request->status;
            $cancelReason = $request->cancel_reason ?? null;

            return DB::transaction(function () use ($id, $role, $newStatus, $user, $cancelReason) {
                $trip = Trip::where('id', $id)->lockForUpdate()->first();

                if (! $trip) {
                    throw new ModelNotFoundException(trans('api.trip_not_found'), [], 404);
                }

                if ($trip->status == 3) {
                    return $this->failure(trans('api.trip_canceled'), [], 422);
                }

                $allowedStatuses = Trip::allowedStatusUpdatesByRole();
                if (! in_array($newStatus, $allowedStatuses[$role] ?? [])) {
                    return $this->failure(trans('api.status_not_authorized'), [], 403);
                }

                if (! Trip::isValidTransition($role, $trip->status, $newStatus)) {
                    return $this->failure(trans('api.trip_invalid_transition'), [], 422);
                }

                // ✅ Allow only owner to update from 6 ➜ 7
                if ($trip->status == 6 && $newStatus == 7 && $role != 'owner') {
                    return $this->failure(trans('api.trip_invalid_owner_transition'), [], 403);
                }

                // شروط خاصة بالكابتن
                if ($role == 'captain') {
                    if ($newStatus == 3) {
                        if ($trip->actual_start_datetime) {
                            return $this->failure(trans('api.trip_invalid_captain_cancel'), [], 403);
                        }

                        if (empty($cancelReason)) {
                            return $this->failure(trans('api.cancel_reason_required'), [], 422);
                        }

                        $trip->cancel_reason = $cancelReason;
                    }

                    if ($newStatus == 4) {
                        $hasFish = FishStock::where('trip_id', $trip->id)
                            ->where('added_by', $user->id)
                            ->exists();

                        if (! $hasFish) {
                            return $this->failure(trans('api.trip_invalid_captain_end'), [], 422);
                        }
                    }
                }

                // شروط خاصة بالعداد
                if ($role == 'counter') {
                    if ($trip->status == 4 && ! $trip->counter_id) {
                        $trip->counter_id = $user->id;
                    } elseif ($trip->counter_id != $user->id) {
                        return $this->failure(trans('api.trip_counter_assigned'), [], 403);
                    }
                }

                $trip->status = $newStatus;
                $trip->save();

                return $this->success(trans('api.trip_updated'), new TripResource($trip), 200);
            });

        } catch (ModelNotFoundException $e) {
            return $this->failure(trans('api.trip_not_found'), [], 404);
        } catch (\Throwable $e) {
            return $this->failure(trans('api.error'), [], 500);
        }
    }

    public function deleteData($id)
    {
        // TODO: Implement deleteData() method.
    }
}
