<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ContactController extends Controller
{
    public function store(Request $request)
    {

        try {
            $data = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);
            $data['name'] = $request->first_name.' '.$request->last_name;

            Contact::create($data);

            if ($request->expectsJson()) {
                return response()->json(['status' => 'success']);
            }

            return redirect()->back()->with('success', __('site.contact_sent'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                throw $e;
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $ex) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error'], 422);
            }
            return redirect()->back()->with('error', __('site.contact_error'))->withInput();
        }
    }
}
