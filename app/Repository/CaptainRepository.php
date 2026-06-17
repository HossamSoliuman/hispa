<?php

namespace App\Repository;

use App\Interfaces\CRUD;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CaptainRepository implements CRUD
{
    public function getList($request)
    {

        if ($request['guard'] == 'owner') {
            return view('owner.captain.index');

        } else {
            return view('admin.captain.index');

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

            $data['role'] = 'captain';
            $data['custom_share_percent'] = ($data['salary_type'] ?? null) === 'percentage'
                ? ($data['custom_share_percent'] ?? null)
                : null;
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            User::create($data);

            DB::commit();
            session()->flash('success', trans('api.captain_added'));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => trans('api.captain_added')]);
            }

            if ($request['guard'] == 'owner') {
                return redirect()->route('owner.captain.index');

            } else {
                return redirect()->route('admin.captain.index');

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

            $captain = User::findOrFail($id);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Optionally delete old logo file if exists
                if (! is_null($captain->getRawOriginal('logo'))) {
                    deleteFile($captain->getRawOriginal('logo'));
                }
                $path = UploadFile($request->file('logo'), 'uploads/users');
                $data['logo'] = $path;
            }

            // Handle general attachment upload
            if ($request->hasFile('attachment')) {
                if (! is_null($captain->getRawOriginal('attachment'))) {
                    deleteFile($captain->getRawOriginal('attachment'));
                }
                $path = UploadFile($request->file('attachment'), 'uploads/users/attachments');
                $data['attachment'] = $path;
            }

            // Handle id_attachment upload
            if ($request->hasFile('id_attachment')) {
                if (! is_null($captain->getRawOriginal('id_attachment'))) {
                    deleteFile($captain->getRawOriginal('id_attachment'));
                }
                $path = UploadFile($request->file('id_attachment'), 'uploads/users/id_attachments');
                $data['id_attachment'] = $path; // fix key to singular 'id_attachment'
            }

            if (isset($data['salary_type'])) {
                $data['custom_share_percent'] = $data['salary_type'] === 'percentage'
                    ? ($data['custom_share_percent'] ?? null)
                    : null;
            }

            $captain->update($data);

            DB::commit();
            session()->flash('success', trans('api.captain_updated'));
            if ($request['guard'] == 'owner') {
                return redirect()->route('owner.captain.index');

            } else {
                return redirect()->route('admin.captain.index');

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
            $captain = User::findOrFail($id);

            if (Trip::withTrashed()->where('captain_id', $captain->id)->exists()) {
                DB::rollBack();

                return response()->json(['message' => trans('api.captain_has_trips')], 422);
            }

            User::where('captain_id', $captain->id)->update(['captain_id' => null]);

            if (! is_null($captain->getRawOriginal('logo'))) {
                deleteFile($captain->getRawOriginal('logo'));
            }
            if (! is_null($captain->getRawOriginal('id_attachment'))) {
                deleteFile($captain->getRawOriginal('id_attachment'));
            }
            if (! is_null($captain->getRawOriginal('attachment'))) {
                deleteFile($captain->getRawOriginal('attachment'));
            }
            $captain->delete();

            DB::commit();
            session()->flash('success', trans('api.captain_deleted'));

            return response()->json(['message' => trans('api.captain_deleted')], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['message' => trans('api.error_deleting').$e->getMessage()], 403);
        }
    }
}
