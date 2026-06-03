<?php

namespace App\DataTable\Owner;

use App\Models\Notification;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class NotificationDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $user_id = auth()->user()->id;

            $query = Notification::orderBy('created_at', 'desc')
                ->where(function ($q) use ($user_id) {
                    $q->where('notifiable_id', $user_id)
                        ->orWhereJsonContains('data->sender_id', $user_id);
                });

            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('title', fn ($row) => $row->data['title'] ?? '-')
                ->addColumn('body', fn ($row) => $row->data['body'] ?? '-')
                ->addColumn('channels', fn ($row) => is_array($row->channels) ? implode(', ', $row->channels) : 'firebase, database')
                ->addColumn('recipient_type', fn ($row) => class_basename($row->notifiable_type))
                ->addColumn('recipient_name', function ($row) {
                    $modelClass = $row->notifiable_type;
                    $user = $modelClass::find($row->notifiable_id);

                    return $user->name ?? '-';
                })
                ->addColumn('sender_name', fn ($row) => $row->data['sender_name'] ?? 'النظام')
                ->addColumn('notification_type', fn ($row) => class_basename($row->type) ?? '-')
                ->addColumn('created_at', fn ($row) => $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-')
                ->rawColumns(['channels', 'title', 'body'])
                ->make(true);
        }
    }
}
