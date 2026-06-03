<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactRequest;
use App\Models\Contact;
use App\Traits\RespondsWithHttpStatus;

class ContactController extends Controller
{
    use RespondsWithHttpStatus;

    public function createContact(ContactRequest $request)
    {
        $user = $request->user();
        $data['subject'] = $request->subject;
        $data['message'] = $request->message;
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;
        $data['user_id'] = $user->id;
        $contact = Contact::create($data);
        if ($contact) {
            return $this->success(trans('site.added_success'), [], 200);

        } else {
            return $this->failure(trans('site.something_error'), [], 404);

        }

    }
}
