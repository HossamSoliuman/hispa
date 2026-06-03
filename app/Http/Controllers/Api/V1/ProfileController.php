<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProfileUserRequest;
use App\Http\Requests\Api\UpdatePasswordRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Traits\RespondsWithHttpStatus;
use App\Traits\Upload;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use RespondsWithHttpStatus, Upload;

    public function profile()
    {

        $user_id = request()->user()->id;
        $user = User::find($user_id);

        return $this->success(trans('site.getData'), new ProfileResource($user), 200);

    }

    public function updateProfile(ProfileUserRequest $request)
    {
        try {
            $user = $request->user();

            $data = collect($request->all())
                ->filter(fn ($value, $key) => $request->has($key))
                ->toArray();

            if ($request->hasFile('logo')) {
                if (! is_null($user->getRawOriginal('logo'))) {
                    $this->deleteFile($user->getRawOriginal('logo'));
                }
                $data['logo'] = $this->UploadFile($request->file('logo'), 'uploads/users');
            }

            if ($request->hasFile('attachment')) {
                if (! is_null($user->getRawOriginal('attachment'))) {
                    $this->deleteFile($user->getRawOriginal('attachment'));
                }
                $data['attachment'] = $this->UploadFile($request->file('attachment'), 'uploads/users/attachments');
            }

            $user->update($data);

            return $this->success(trans('site.updated_successfully'), new ProfileResource($user), 200);

        } catch (\Exception $ex) {
            if (app()->environment('local')) {
                return $this->failure(trans('site.something_error'), $ex->getMessage(), 404);
            }

            return $this->failure(trans('site.something_error'), [], 404);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {

        try {

            $user = request()->user();
            $user_id = $user->id;

            $user = User::find($user_id);
            if (! Hash::check($request->old_password, $user->password)) {
                return $this->failure(trans('site.password_not_match'), [], 404);
            }

            $data['password'] = bcrypt($request->password);
            $user->update($data);

            return $this->success(trans('site.updated_password_successfully'), [], 200);

        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return $this->failure(trans('site.something_error'), $ex->getMessage(), 404);
            }

            return $this->failure(trans('site.something_error'), [], 404);

        }
    }
}
