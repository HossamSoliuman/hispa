<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Boat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::where('owner_id', $this->ownerId())
            ->with('boat')
            ->latest()
            ->paginate(20);

        return view('owner.assets.index', compact('assets'));
    }

    public function create()
    {
        $boats = $this->ownerBoatsWithoutAsset()->get();

        return view('owner.assets.create', compact('boats'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['owner_id'] = $this->ownerId();
        Asset::create($data);

        return redirect()->route('owner.assets.index')
            ->with('success', 'تم إضافة الأصل بنجاح');
    }

    public function edit(Asset $asset)
    {
        $this->authorizeOwner($asset);

        $boats = $this->ownerBoatsWithoutAsset()
            ->orWhere(function ($query) use ($asset) {
                $query->where('owner_id', $this->ownerId())->where('id', $asset->boat_id);
            })
            ->get();

        return view('owner.assets.edit', compact('asset', 'boats'));
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorizeOwner($asset);

        $data = $this->validateData($request);
        $asset->update($data);

        return redirect()->route('owner.assets.index')
            ->with('success', 'تم تحديث الأصل بنجاح');
    }

    public function show(Asset $asset)
    {
        $this->authorizeOwner($asset);

        $asset->load('boat', 'depreciations');

        return view('owner.assets.show', compact('asset'));
    }

    public function destroy($id)
    {
        $asset = Asset::where('owner_id', $this->ownerId())->find($id);

        if (! $asset) {
            return response()->json(['message' => 'not found'], 404);
        }

        $asset->delete();

        return response()->json(['message' => 'تم حذف الأصل بنجاح'], 200);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateData(Request $request): array
    {
        $ownerId = $this->ownerId();

        $data = $request->validate([
            'asset_type' => 'required|in:boat,fishing_equipment,other',
            'boat_id' => [
                'nullable',
                Rule::exists('boats', 'id')->where('owner_id', $ownerId),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0|lte:purchase_cost',
            'useful_life_years' => 'required|integer|min:1',
            'status' => 'required|in:active,sold,damaged',
            'notes' => 'nullable|string',
        ]);

        $data['depreciation_method'] = 'straight_line';
        $data['depreciation_rate'] = 0;

        return $data;
    }

    public function getAssetsData(Request $request)
    {
        $query = Asset::where('owner_id', $this->ownerId())->orderBy('created_at', 'desc');
        if ($request->filled('asset_type')) {
            $query = $query->where('asset_type', $request->asset_type);
        }
        $data = $query->get();

        $summary = [
            'total_assets' => $data->count(),
            'total_boat' => $data->where('asset_type', 'boat')->count(),
            'total_fishing_equipment' => $data->where('asset_type', 'fishing_equipment')->count(),
            'total_other' => $data->where('asset_type', 'other')->count(),
        ];

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return $row->name ?? '';
            })
            ->addColumn('type', function ($row) {
                if ($row->asset_type == 'boat') {
                    return '<span class="badge bg-success">'.__('owner.assets.boat').'</span>';
                } elseif ($row->asset_type == 'fishing_equipment') {
                    return '<span class="badge bg-success">'.__('owner.assets.fishing_equipment').'</span>';
                } else {
                    return '<span class="badge bg-danger">'.__('owner.assets.other').'</span>';
                }
            })

            ->addColumn('status', function ($row) {
                if ($row->status == 'active') {
                    return '<span class="badge bg-success">'.__('owner.assets.active').'</span>';
                } elseif ($row->status == 'sold') {
                    return '<span class="badge bg-success">'.__('owner.assets.sold').'</span>';
                } else {
                    return '<span class="badge bg-danger">'.__('owner.assets.damaged').'</span>';
                }
            })

            ->addColumn('action', function ($row) {
                $editUrl = route('owner.assets.edit', $row->id);
                $deleteUrl = route('owner.assets.destroy', $row->id);

                return '
                <a href="'.$editUrl.'" class="btn btn-sm btn-outline-success me-1"><i class="bi bi-pencil"></i></a>
                <a href="#" onclick="deleteRecord('.$row->id.')" class="btn btn-outline-danger btn-sm" title="حذف"><i class="bi bi-trash"></i></a>
                ';
            })

            ->rawColumns(['status', 'action', 'type']) // أضف 'action' هنا لأن به HTML
            ->with(['summary' => $summary])
            ->make(true);
    }

    private function ownerId(): int
    {
        $ownerId = Auth::guard('owner')->id();
        abort_if(! $ownerId, 403, 'غير مصرح');

        return (int) $ownerId;
    }

    private function authorizeOwner(Asset $asset): void
    {
        abort_if($asset->owner_id !== $this->ownerId(), 403);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Boat>
     */
    private function ownerBoatsWithoutAsset()
    {
        return Boat::where('owner_id', $this->ownerId())->whereDoesntHave('asset');
    }
}
