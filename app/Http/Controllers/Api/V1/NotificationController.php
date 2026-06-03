<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReadNotifcationApiRequest;
use App\Http\Resources\NotificationApiResource;
use App\Service\Firebase\FirebaseNotification;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use RespondsWithHttpStatus;

    private $firebase;

    public function __construct()
    {
        $this->firebase = new FirebaseNotification;
    }

    public function getNotification(Request $request)
    {
        $user_id = auth()->user()->id;

        $readable = $request->query('readable');

        $query = DB::table('notifications')->where('notifiable_id', $user_id);

        if (! is_null($readable)) {
            if ($readable == 1) {
                $query->whereNotNull('read_at');
            } elseif ($readable == 0) {
                // Fetch only unread notifications (read_at is null)
                $query->whereNull('read_at');
            }
        }

        // Paginate the notifications (10 items per page by default)
        $notifications = $query->orderBy('created_at', 'desc')->paginate(10);

        // Return the paginated result using your paginationResult helper function
        return $this->success('تم جلب البيانات بنجاح', paginationResult(NotificationApiResource::collection($notifications)), 200);
    }

    public function readNotification(ReadNotifcationApiRequest $request)
    {
        try {
            $user_id = auth()->user()->id;
            $id = $request->id;

            // Fetch the notification and ensure it belongs to the user
            $notification = DB::table('notifications')->where('id', $id)->where('notifiable_id', $user_id)->first();

            if ($notification) {
                if ($notification->read_at != null) {
                    return $this->success('تم قراءة ألإشعار مسبقاً', [], 200);

                }
                // Update the notification's 'read_at' timestamp using the query builder
                DB::table('notifications')
                    ->where('id', $id)
                    ->where('notifiable_id', $user_id)
                    ->update(['read_at' => now()]);

                return $this->success('تم قراءة ألإشعار بنجاح', [], 200);
            } else {
                return $this->failure('حدث خطأ ما !!!', [], 404);
            }
        } catch (\Exception $ex) {
            if (App::environment('local')) {
                return $this->failure('حدث خطأ ما !!!', $ex->getMessage(), 404);
            }

            return $this->failure('حدث خطأ ما !!!', [], 404);
        }
    }

    public function sendNotificationFirebase(Request $request)
    {
        $fcm_token = $request->fcm_token;
        $title = $request->title;
        $message = $request->message;

        $notification = $this->firebase->sendNotificationFirebase($fcm_token, $title, $message);

        if (isset($notification['error']) || isset($notification['success']) && ! $notification['success']) {
            return $this->failure('فشل ارسال الاشعار !!!!', $notification, 403);
        }

        return $this->success('تم إرسال ألإشعار  بنجاح', $notification, 200);

    }
}
