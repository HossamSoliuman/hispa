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
        $advancePeople = User::EmployeeRole()
            ->where('owner_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('owner.employee.index', compact('advancePeople'));
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

        $user->load(['advances' => fn ($q) => $q->where('owner_id', auth()->id())->latest('date')]);

        return view('owner.employee.show', compact('user'));
    }

    /**
     * Render the employee's data card as a printable PDF (government-facing).
     */
    public function print(Request $request, $id): \Illuminate\Http\Response
    {
        $user = User::EmployeeRole()->where('owner_id', auth()->id())
            ->with(['boat', 'region', 'governorate', 'port'])
            ->findOrFail($id);

        $settings = $this->reportSettings();
        $title = __('owner.reports.personnel_employee_title');
        $filename = 'employee-'.$user->id.'.pdf';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return pdf_report(view('owner.reports.print.personnel-card', compact('user', 'settings', 'title')), [], $filename, $disposition);
    }

    /**
     * Build the company settings array shared by the personnel PDF report.
     *
     * @return array<string, mixed>
     */
    private function reportSettings(): array
    {
        $companyName = currentCompany()?->name ?: 'حسبة';

        return ownerCompanySettings([
            'qr_code' => app(\App\Service\Owner\ReportQrService::class)->dataUri("Company: {$companyName}"),
        ]);
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
