<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\ProfileRequest;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('web')->user();

        return view('owner.profile.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $regions = Region::Active()->get();

        return view('owner.profile.edit', compact('user', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $data = $request->validated();

            if ($request->hasFile('logo')) {
                if (! is_null($user->getRawOriginal('logo'))) {
                    deleteFile($user->getRawOriginal('logo'));
                }
                $data['logo'] = UploadFile($request->file('logo'), 'uploads/users');
            }
            if ($request->hasFile('attachment')) {
                $path = UploadFile($request->file('attachment'), 'uploads/users/attachments');
                $data['attachment'] = $path;
            }
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            } else {
                unset($data['password']);
            }

            $isUpdated = $user->update($data);

            if ($isUpdated) {
                DB::commit();

                return redirect()->back()->with('success', 'تم تحديث الملف الشخصي بنجاح.');
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->with(['error' => $ex->getmessage()]);
            }

            return redirect()->back()->with(['error' => 'حدث خطأ ما']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
