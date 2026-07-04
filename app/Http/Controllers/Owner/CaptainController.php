<?php

namespace App\Http\Controllers\Owner;

use App\DataTable\Owner\CaptainDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CaptainRequest;
use App\Models\Boat;
use App\Models\Region;
use App\Models\Trip;
use App\Models\User;
use App\Repository\CaptainRepository;
use Illuminate\Http\Request;

class CaptainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $datatable;

    private $rep;

    public function __construct()
    {
        $this->datatable = new CaptainDataTable;
        $this->rep = new CaptainRepository;

        //
        //        $this->middleware('permission:read_settings', ['only' => ['index', 'show']]);
        //        $this->middleware('permission:create_settings', ['only' => ['create', 'store']]);
        //        $this->middleware('permission:update_settings', ['only' => ['edit', 'update']]);
        //        $this->middleware('permission:delete_settings', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $advancePeople = User::CaptainRole()
            ->where('owner_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('owner.captain.index', compact('advancePeople'));
    }

    public function getCaptainData(Request $request)
    {
        return $this->datatable->getData($request);
    }

    public function showCaptainData(Request $request, $id)
    {
        return $this->datatable->showData($request, $id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $owner_id = auth()->user()->id;
        $regions = Region::Active()->select('id', 'name')->get();
        $boats = Boat::Active()->where('owner_id', $owner_id)->select('id', 'name_ar', 'name_en')->get();

        return view('owner.captain.create', compact('regions', 'boats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CaptainRequest $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed', // دعم password_confirmation
        ]);
        $request['guard'] = 'owner';

        return $this->rep->saveData($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (! $user) {
            return redirect()->back()->with(['error' => 'الصفحة غير موجودة']);
        }
        $tripCount = Trip::where('captain_id', $id)->count();

        $query = User::where('id', $id)->with(['boat', 'boat.stocks'])->first();

        $stats = (object) [
            'total_trips' => $tripCount,
            'corrected_items' => $query->boat->stocks->count(),
        ];

        $user->load(['advances' => fn ($q) => $q->where('owner_id', auth()->id())->latest('date')]);

        return view('owner.captain.show', compact('user', 'stats'));
    }

    /**
     * Render the captain's data card as a printable PDF (government-facing).
     */
    public function print(Request $request, $id): \Illuminate\Http\Response
    {
        $user = User::CaptainRole()->where('owner_id', auth()->id())
            ->with(['boat', 'region', 'governorate', 'port'])
            ->findOrFail($id);

        $settings = $this->reportSettings();
        $title = __('owner.reports.personnel_captain_title');
        $filename = 'captain-'.$user->id.'.pdf';
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $owner_id = auth()->user()->id;
        $regions = Region::Active()->select('id', 'name')->get();
        $boats = Boat::Active()->where('owner_id', $owner_id)->select('id', 'name_ar', 'name_en')->get();
        $data = User::where('owner_id', $owner_id)->find($id);

        if (! $data) {
            return redirect()->back()->with(['error' => 'حدث خطأ ما']);
        }

        return view('owner.captain.edit', compact('regions', 'data', 'boats'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(CaptainRequest $request, int $id)
    {
        $request['guard'] = 'owner';

        return $this->rep->updateData($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        return $this->rep->deleteData($id);
    }
}
