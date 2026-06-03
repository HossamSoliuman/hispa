<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\UserRequest;
use App\Notifications\NewUserRequestNotification;
use Illuminate\Support\Facades\Notification;

class UserRequestObserver
{
    /**
     * Handle the UserRequest "created" event.
     */
    public function created(UserRequest $userRequest): void
    {
        $user = auth()->user();
        $channels = ['database'];
        $admins = Admin::whereJsonContains('roles_name', 'owner')->get();

        if ($userRequest->status == 'pending') {

            $data = [
                'user_request' => $userRequest->id,
                'sender_name' => $user->name,
                'title' => [
                    'ar' => "قام المستخدم {$user->name} بإرسال طلب تحديث جديد.",
                    'en' => "User {$user->name} has sent a new update request.",
                ],
                'body' => [
                    'ar' => "قام المستخدم {$user->name} بإرسال طلب تحديث جديد.",
                    'en' => "User {$user->name} has sent a new update request.",
                ],
            ];

            Notification::send($admins, new NewUserRequestNotification($data, $channels));

        } elseif ($userRequest->status == 'approved') {
            $data = [
                'user_request' => $userRequest->id,
                'sender_name' => auth()->user()->name ?? 'النظام',
                'title' => [
                    'ar' => 'تمت الموافقة على طلب تعديل البيانات',
                    'en' => 'Approval for data update request',
                ],
                'body' => [
                    'ar' => 'تمت الموافقة على طلبك لتحديث البيانات. يمكنك الآن مشاهدة التعديلات في حسابك.',
                    'en' => 'Your data update request has been approved. You can now view the updates in your account.',
                ],
            ];

            $userRequest->user->notify(new NewUserRequestNotification($data, ['firebase', 'database']));
        } elseif ($userRequest->status == 'rejected') {
            $data = [
                'user_request' => $userRequest->id,
                'sender_name' => auth()->user()->name ?? 'النظام',
                'title' => [
                    'ar' => 'تم رفض طلب تعديل البيانات',
                    'en' => 'Data update request rejected',
                ],
                'body' => [
                    'ar' => 'نأسف، لقد تم رفض طلبك لتحديث البيانات. لمزيد من التفاصيل، يرجى التواصل مع الإدارة.',
                    'en' => 'We apologize, your data update request has been rejected. For more details, please contact the administration.',
                ],
            ];

            $userRequest->user->notify(new NewUserRequestNotification($data, ['firebase', 'database']));
        }
    }

    /**
     * Handle the UserRequest "updated" event.
     */
    public function updated(UserRequest $userRequest): void
    {
        $user = auth()->user();
        $channels = ['database'];
        $admins = Admin::whereJsonContains('roles_name', 'owner')->get();

        if ($userRequest->status == 'pending') {

            $data = [
                'user_request' => $userRequest->id,
                'sender_name' => $user->name,
                'title' => [
                    'ar' => "قام المستخدم {$user->name} بإرسال طلب تحديث جديد.",
                    'en' => "User {$user->name} has sent a new update request.",
                ],
                'body' => [
                    'ar' => "قام المستخدم {$user->name} بإرسال طلب تحديث جديد.",
                    'en' => "User {$user->name} has sent a new update request.",
                ],
                'url' => route('admin.user_request.show', $userRequest->id), // هذا هو رابط الوجهة

            ];

            Notification::send($admins, new NewUserRequestNotification($data, $channels));

        } elseif ($userRequest->status == 'approved') {
            $data = [
                'user_request' => $userRequest->id,
                'sender_name' => auth()->user()->name ?? 'النظام',
                'title' => [
                    'ar' => 'تمت الموافقة على طلب تعديل البيانات',
                    'en' => 'Approval for data update request',
                ],
                'body' => [
                    'ar' => 'تمت الموافقة على طلبك لتحديث البيانات. يمكنك الآن مشاهدة التعديلات في حسابك.',
                    'en' => 'Your data update request has been approved. You can now view the updates in your account.',
                ],
            ];

            $userRequest->user->notify(new NewUserRequestNotification($data, ['firebase', 'database']));
        } elseif ($userRequest->status == 'rejected') {
            $data = [
                'user_request' => $userRequest->id,
                'sender_name' => auth()->user()->name ?? 'النظام',
                'title' => [
                    'ar' => 'تم رفض طلب تعديل البيانات',
                    'en' => 'Data update request rejected',
                ],
                'body' => [
                    'ar' => 'نأسف، لقد تم رفض طلبك لتحديث البيانات. لمزيد من التفاصيل، يرجى التواصل مع الإدارة.',
                    'en' => 'We apologize, your data update request has been rejected. For more details, please contact the administration.',
                ],
            ];

            $userRequest->user->notify(new NewUserRequestNotification($data, ['firebase', 'database']));
        }

    }

    /**
     * Handle the UserRequest "deleted" event.
     */
    public function deleted(UserRequest $userRequest): void
    {
        //
    }

    /**
     * Handle the UserRequest "restored" event.
     */
    public function restored(UserRequest $userRequest): void
    {
        //
    }

    /**
     * Handle the UserRequest "force deleted" event.
     */
    public function forceDeleted(UserRequest $userRequest): void
    {
        //
    }
}
