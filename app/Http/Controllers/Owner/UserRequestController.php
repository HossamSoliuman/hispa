<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class UserRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('owner.user_request.index');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {

        try {
            $user = auth()->user();
            $validated = $request->validate([
                'fields' => 'required|array',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $path = null;

            if ($request->hasFile('attachment')) {
                if (! is_null($user->getRawOriginal('attachment'))) {
                    deleteFile($user->getRawOriginal('attachment'));
                }
                $data['attachment'] = UploadFile($request->file('attachment'), 'uploads/users/attachments');
            }

            $user_request = UserRequest::create([
                'user_id' => auth()->id(),
                'type' => 'update',
                'data' => $validated['fields'],
                'attachment' => $path,
            ]);

            session()->flash('info', ' طلبك قيد المراجعة');

            return redirect()->route('owner.basic_data.index');

        } catch (\Exception $ex) {

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
