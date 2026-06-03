<?php

namespace App\Repository\Owner;

use App\Interfaces\CRUD;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmployeeRepository implements CRUD
{
    public function getList($request)
    {
        return view('owner.employee.index');
    }

    public function getDetail($id)
    {
        // TODO: Implement getDetail() method.
    }

    public function saveData($request)
    {

        DB::beginTransaction();
        try {
            $data = $request->validated();

            $data['status'] = $request->status == 1 ? 1 : 0;
            $data['owner_id'] = auth()->user()->id;
            $data['salary_type'] = 'salary';

            if ($request->hasFile('logo')) {
                $path = UploadFile($request->file('logo'), 'uploads/users');
                $data['logo'] = $path;
            }

            $data['role'] = 'employee';
            User::create($data);

            DB::commit();
            session()->flash('success', trans('api.employee_added'));

            return redirect()->route('owner.employee.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['error' => trans('api.error_saving').$e->getMessage()])->withInput();
        }
    }

    public function updateData($request, $id)
    {

        DB::beginTransaction();
        try {
            $data = $request->validated();

            $data['status'] = $request->status == 1 ? 1 : 0;

            $employee = User::where('owner_id', auth()->user()->id)->EmployeeRole()->findOrFail($id);
            if (! $employee) {
                return redirect()->back()->with(['error' => 'حدث خطأ ما']);
            }

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Optionally delete old logo file if exists
                if (! is_null($employee->getRawOriginal('logo'))) {
                    deleteFile($employee->getRawOriginal('logo'));
                }
                $path = UploadFile($request->file('logo'), 'uploads/users');
                $data['logo'] = $path;
            }

            $employee->update($data);

            DB::commit();
            session()->flash('success', trans('api.employee_updated'));

            return redirect()->route('owner.employee.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['error' => trans('api.error_updating').$e->getMessage()])->withInput();
        }
    }

    public function deleteData($id)
    {
        DB::beginTransaction();
        try {
            $employee = User::findOrFail($id);

            if (! is_null($employee->getRawOriginal('logo'))) {
                deleteFile($employee->getRawOriginal('logo'));
            }

            $employee->delete();

            DB::commit();
            session()->flash('success', trans('api.employee_deleted'));

            return response()->json(['message' => 'Data saved successfully'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['message' => trans('api.error_deleting').$e->getMessage()], 403);
        }
    }
}
