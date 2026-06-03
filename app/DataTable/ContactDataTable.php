<?php

namespace App\DataTable;

use App\Models\Contact;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ContactDataTable extends DataTables
{
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Contact::orderBy('created_at', 'desc');

            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-primary">'.__('admin.contacts.new').'</span>';
                    } else {
                        return '<span class="badge bg-success">'.__('admin.contacts.replied').'</span>';
                    }
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('Y-m-d') : '-';
                })
                ->addColumn('action', function (Contact $contact) {
                    $btn = '';

                    if (auth()->user()->can('update_contacts')) {

                        $btn .= '<a data-bs-effect="effect-scale" data-bs-toggle="modal" href="#modelEdit"
            data-id="'.$contact->id.'"
            data-name="'.$contact->name.'"
            data-phone="'.$contact->phone.'"
            data-email="'.$contact->email.'"
            data-subject="'.$contact->subject.'"
            data-message="'.$contact->message.'"
            data-response="'.$contact->response.'"
            class="edit btn btn-primary btn-sm editBtn">
            <li class="fas fa-edit"></li>
        </a>';
                    }
                    if (auth()->user()->can('delete_contacts')) {

                        $btn .= '<a href="#" onclick="deleteRecord('.$contact->id.')" class="edit btn btn-danger btn-sm"><li class="fas fa-trash"></li></a>';
                    }

                    return $btn;

                })
                ->rawColumns(['action', 'updated_at', 'status'])
                ->make(true);
        }

    }
}
