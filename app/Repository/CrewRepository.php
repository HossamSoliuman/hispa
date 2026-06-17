<?php

namespace App\Repository;

use App\Interfaces\CRUD;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CrewRepository implements CRUD
{
    public function getList($request)
    {

        if ($request['guard'] == 'owner') {
            return view('owner.crew.index');
        } else {
            return view('admin.crew.index');
        }
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

            if ($request['guard'] == 'owner') {
                $data['owner_id'] = auth()->user()->id;
            }

            if ($request['guard'] == 'admin') {
                $request->validate(['owner_id' => 'required|integer|exists:users,id']);
            }
            $data['status'] = $request->status == 1 ? 1 : 0;
            $data['owner_id'] = $request->owner_id ?? auth()->user()->id;

            if ($request->hasFile('logo')) {
                $path = UploadFile($request->file('logo'), 'uploads/users');
                $data['logo'] = $path;
            }

            if ($request->hasFile('attachment')) {
                $path = UploadFile($request->file('attachment'), 'uploads/users/attachments');
                $data['attachment'] = $path;
            }

            if ($request->hasFile('id_attachment')) {
                $path = UploadFile($request->file('id_attachment'), 'uploads/users/id_attachments');
                $data['id_attachments'] = $path;
            }

            $data['role'] = 'crew';
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // معالجة بيانات الراتب حسب النوع
            if ($data['salary_type'] === 'mixed') {
                // للنوع المختلط، نحفظ الراتب الثابت في salary_amount والنسبة في حقل منفصل
                // يمكن استخدام JSON أو حقل منفصل حسب التصميم
                $data['salary_amount'] = $data['fixed_salary_amount'] ?? 0;
                // يمكن إضافة حقل percentage_amount في جدول users لاحقاً
                // أو استخدام JSON في salary_amount: {"fixed": 1000, "percentage": 5}
            } elseif ($data['salary_type'] === 'percentage') {
                // للنسبة، نحفظ النسبة في salary_amount
                $data['salary_amount'] = $data['percentage_amount'] ?? 0;
            }
            // للنوع salary، salary_amount موجود بالفعل في $data

            $data['custom_share_percent'] = $data['salary_type'] === 'percentage'
                ? ($data['custom_share_percent'] ?? null)
                : null;

            User::create($data);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => trans('api.crew_added')]);
            }

            session()->flash('success', trans('api.crew_added'));
            if ($request['guard'] == 'owner') {
                return redirect()->route('owner.crew.index');
            } else {
                return redirect()->route('admin.crew.index');
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => trans('api.error_saving'), 'error' => $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => trans('api.error_saving').$e->getMessage()])->withInput();
        }
    }

    public function updateData($request, $id)
    {

        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request['guard'] == 'owner') {
                $data['owner_id'] = auth()->user()->id;
            }

            if ($request['guard'] == 'admin') {
                $request->validate(['owner_id' => 'required|integer|exists:users,id']);
            }

            $data['status'] = $request->status == 1 ? 1 : 0;
            $data['owner_id'] = $request->owner_id ?? auth()->user()->id;

            $crew = User::findOrFail($id);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Optionally delete old logo file if exists
                if (! is_null($crew->getRawOriginal('logo'))) {
                    deleteFile($crew->getRawOriginal('logo'));
                }
                $path = UploadFile($request->file('logo'), 'uploads/users');
                $data['logo'] = $path;
            }

            // Handle general attachment upload
            if ($request->hasFile('attachment')) {
                if (! is_null($crew->getRawOriginal('attachment'))) {
                    deleteFile($crew->getRawOriginal('attachment'));
                }
                $path = UploadFile($request->file('attachment'), 'uploads/users/attachments');
                $data['attachment'] = $path;
            }

            // Handle id_attachment upload
            if ($request->hasFile('id_attachment')) {
                if (! is_null($crew->getRawOriginal('id_attachment'))) {
                    deleteFile($crew->getRawOriginal('id_attachment'));
                }
                $path = UploadFile($request->file('id_attachment'), 'uploads/users/id_attachments');
                $data['id_attachment'] = $path; // fix key to singular 'id_attachment'
            }

            // معالجة بيانات الراتب حسب النوع
            if (isset($data['salary_type'])) {
                if ($data['salary_type'] === 'mixed') {
                    $data['salary_amount'] = $data['fixed_salary_amount'] ?? 0;
                } elseif ($data['salary_type'] === 'percentage') {
                    $data['salary_amount'] = $data['percentage_amount'] ?? 0;
                }

                $data['custom_share_percent'] = $data['salary_type'] === 'percentage'
                    ? ($data['custom_share_percent'] ?? null)
                    : null;
            }

            $crew->update($data);

            DB::commit();
            session()->flash('success', trans('api.crew_updated'));

            if ($request['guard'] == 'owner') {
                return redirect()->route('owner.crew.index');
            } else {
                return redirect()->route('admin.crew.index');
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['error' => trans('api.error_updating').$e->getMessage()])->withInput();
        }
    }

    public function deleteData($id)
    {
        DB::beginTransaction();
        try {
            $crew = User::findOrFail($id);

            if (! is_null($crew->getRawOriginal('logo'))) {
                deleteFile($crew->getRawOriginal('logo'));
            }
            if (! is_null($crew->getRawOriginal('id_attachment'))) {
                deleteFile($crew->getRawOriginal('id_attachment'));
            }
            if (! is_null($crew->getRawOriginal('attachment'))) {
                deleteFile($crew->getRawOriginal('attachment'));
            }

            $crew->delete();

            DB::commit();
            session()->flash('success', trans('api.crew_deleted'));

            return response()->json(['message' => 'Data saved successfully'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['message' => trans('api.error_deleting').$e->getMessage()], 403);
        }
    }
}
