<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\NotificationDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationRequest;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    private $datatable;

    public function __construct()
    {
        $this->datatable = new NotificationDataTable;

    }

    public function fetch()
    {
        $notifications = Auth::user()->unreadNotifications()->latest()->take(10)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'عنوان',
                'body' => $notification->data['body'] ?? '',
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        });

        return response()->json($notifications);
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['status' => 'success']);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->unreadNotifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect('/owner/notifications');
    }

    public function fetchUsers(Request $request)
    {
        $user = auth()->user();
        $owner_id = $user->id;
        $role = $request->role;

        if ($role === 'dalal') {
            $users = DB::table('dalal_stocks')
                ->join('users', 'dalal_stocks.dalal_id', '=', 'users.id')
                ->where('dalal_stocks.owner_id', $owner_id)
                ->select('users.id', 'users.name', 'users.role')
                ->get();
        } else {
            $users = User::query()
                ->whereHas('boat', function ($query) use ($owner_id) {
                    $query->where('owner_id', $owner_id);
                });

            if ($role) {
                $users->where('role', $role);
            }

            $users = $users->select('id', 'name', 'role', 'boat_id')->get();
        }

        return response()->json($users);
    }

    public function index()
    {
        return view('owner.notification.index');
    }

    public function getNotificationData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function sendNotifications(NotificationRequest $request)
    {

        $data = [
            'user_id' => auth()->id(),
            'sender_name' => auth()->user()->name ?? 'النظام',
            'sender_id' => auth()->user()->id,
            'title' => $request->title,
            'body' => $request->body,
        ];

        $query = User::query();

        if ($request->recipientType == 'all') {

            if ($request->userType == 'user' && $request->role) {
                $role = $request->role;

                if ($role === 'dalal') {
                    $owner_id = auth()->id();
                    $query = User::query()
                        ->whereIn('id', function ($q) use ($owner_id) {
                            $q->select('dalal_id')
                                ->from('dalal_stocks')
                                ->where('owner_id', $owner_id);
                        });
                } else {
                    $owner_id = auth()->id();
                    $query = User::query()
                        ->where('role', $role)
                        ->whereHas('boat', function ($q) use ($owner_id) {
                            $q->where('owner_id', $owner_id);
                        });
                }
            }

        } elseif ($request->recipientType == 'specific' && $request->filled('recipient_ids')) {
            $query->whereIn('id', $request->recipient_ids);
        }

        $channels = $request->input('channels');
        if (! $channels || ! is_array($channels) || empty($channels)) {
            $channels = ['database'];
        }

        $query->chunk(500, function ($users) use ($data, $channels) {
            Notification::send($users, new GeneralNotification($data, $channels));
            sleep(1);
        });

        return back()->with('success', 'تم إرسال الإشعارات بنجاح.');
    }
}
