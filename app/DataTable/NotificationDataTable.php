<?php

namespace App\DataTable;

use App\Models\Notification;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class NotificationDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Notification::orderBy('created_at', 'desc');
            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()

                ->addColumn('title', function ($row) {
                    return $row->data['title'] ?? '-';
                })
                ->addColumn('body', function ($row) {
                    return $row->data['body'] ?? '-';
                })
                ->addColumn('channels', function ($row) {
                    return is_array($row->channels) ? implode(', ', $row->channels) : 'firebase, database';
                })

                ->addColumn('recipient_name', function ($row) {
                    $modelClass = $row->notifiable_type;
                    $user = $modelClass::find($row->notifiable_id);

                    return $user->name ?? '-';
                })
                ->addColumn('sender_name', function ($row) {
                    return $row->data['sender_name'] ?? 'النظام';
                })

                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-';
                })
                ->rawColumns(['channels', 'title', 'body'])
                ->make(true);
        }
    }
}
