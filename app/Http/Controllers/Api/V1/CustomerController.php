<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CustomerRequest;
use App\Models\Customer;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        $role = $user->role;

        $customers = Customer::query()
            ->when($role == 'owner', fn ($q) => $q->where('owner_id', $user->id))
            ->when($role != 'owner', fn ($q) => $q->where('dalal_id', $user->id))
            ->paginate(10);

        return $this->success(trans('site.getData'), paginationResult($customers), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        try {
            $user = request()->user();
            $role = $user->role;

            $data = [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'notes' => $request->notes,
            ];

            if ($role === 'owner') {
                $data['owner_id'] = $user->id;
            } else {
                $data['dalal_id'] = $user->id;
            }

            $customer = Customer::create($data);

            return $this->success(trans('site.save'), null, 200);

        } catch (\Exception $e) {
            return $this->failure(
                trans('site.something_error'),
                ['message' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = request()->user();
            $role = $user->role;

            $customer = Customer::query()
                ->when($role == 'owner', fn ($q) => $q->where('owner_id', $user->id))
                ->when($role != 'owner', fn ($q) => $q->where('dalal_id', $user->id))
                ->where('id', $id)
                ->first();

            if (! $customer) {
                return $this->failure(trans('site.page_not_found'), null, 404);
            }
            $customer->update($request->only(['name', 'phone', 'email', 'notes']));

            return $this->success(trans('site.updated_successfully'), null, 200);

        } catch (\Exception $e) {
            return $this->failure(
                trans('site.something_error'),
                ['message' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = request()->user();
            $role = $user->role;

            $customer = Customer::query()
                ->when($role == 'owner', fn ($q) => $q->where('owner_id', $user->id))
                ->when($role != 'owner', fn ($q) => $q->where('dalal_id', $user->id))
                ->where('id', $id)
                ->first();

            if (! $customer) {
                return $this->failure(trans('site.page_not_found'), null, 404);
            }
            $customer->delete();

            return $this->success(trans('site.deleted_success'), null, 200);

        } catch (\Exception $e) {
            return $this->failure(
                trans('site.something_error'),
                ['message' => $e->getMessage()],
                500
            );
        }
    }
}
