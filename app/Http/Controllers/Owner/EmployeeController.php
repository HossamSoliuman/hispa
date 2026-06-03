<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\EmployeeDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\EmployeeRequest;
use App\Models\Boat;
use App\Models\User;
use App\Repository\Owner\EmployeeRepository;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    private $datatable;

    private $rep;

    public function __construct()
    {
        $this->datatable = new EmployeeDataTable;
        $this->rep = new EmployeeRepository;
    }

    public function index()
    {

        $request['guard'] = 'owner';

        return $this->rep->getList($request);
    }

    public function getEmployeeData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function create()
    {
        $boats = Boat::Active()->select('id', 'name_ar', 'name_en')->get();

        return view('owner.employee.create', compact('boats'));
    }

    public function store(EmployeeRequest $request)
    {
        return $this->rep->saveData($request);
    }

    public function show($id)
    {
        $user = User::where('owner_id', auth()->user()->id)->EmployeeRole()->find($id);
        if (! $user) {
            return redirect()->back()->with(['error' => 'الصفحة غير موجودة']);
        }

        return view('owner.employee.show', compact('user'));
    }

    public function edit($id)
    {
        $boats = Boat::Active()->select('id', 'name_ar', 'name_en')->get();
        $data = User::where('owner_id', auth()->user()->id)->EmployeeRole()->find($id);
        if (! $data) {
            return redirect()->back()->with(['error' => 'حدث خطأ ما']);
        }

        return view('owner.employee.edit', compact('boats', 'data'));
    }

    public function update(EmployeeRequest $request, $id)
    {
        return $this->rep->updateData($request, $id);
    }

    public function destroy($id)
    {
        return $this->rep->deleteData($id);
    }
}
