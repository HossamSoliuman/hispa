<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function fetch()
    {
        $admin = Auth::guard('admin')->user();
        $notifications = $admin->unreadNotifications()->latest()->take(10)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? __('admin.notifications.title'),
                'body' => $notification->data['body'] ?? '',
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        });

        return response()->json($notifications);
    }

    public function readAll()
    {
        $admin = Auth::guard('admin')->user();
        $admin->unreadNotifications->markAsRead();

        return response()->json(['status' => 'success']);
    }

    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $notifications = $admin->notifications()->latest()->paginate(20);
        
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $admin = Auth::guard('admin')->user();
        $notification = $admin->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back();
    }
}
