<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('permission:read_admins', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_admins', ['only' => ['create', 'store']]);
        $this->middleware('permission:update_admins', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_admins', ['only' => ['destroy']]);

    }

    public function index(Request $request)
    {
        $query = Admin::orderBy('id', 'DESC')->where('roles_name', '!=', '["owner"]');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }
        if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
            $query->where('status', (int) $request->status);
        }

        $data = $query->get();

        return view('admin.admins.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->pluck('name', 'name')->all();

        return view('admin.admins.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminRequest $request)
    {

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $input['status'] = $request->has('status') ? 1 : 0;

            $admin = Admin::create($input);
            $admin->assignRole($request->input('roles_name'));

            DB::commit();

            return redirect()->route('admin.admins.index')
                ->with('success', 'تم إضافة المستخدم بنجاح');

        } catch (\Exception $ex) {
            DB::rollBack();

            if (App::environment('local')) {
                return redirect()->back()->withInput()->with([
                    'error' => $ex->getMessage(),
                ]);
            }

            return redirect()->back()->withInput()->with([
                'error' => 'حدث خطأ ما أثناء إضافة المستخدم',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admin = Admin::find($id);
        if (! $admin) {
            // Redirect to the index page with an error message
            return redirect()->route('admins.index')->with('error', 'الصفحة غير موحودة.');
        }

        return view('admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Admin::find($id);
        if (! $data) {
            // Redirect to the index page with an error message
            return redirect()->route('admin.admins.index')->with('error', 'الصفحة غير موحودة.');
        }
        $roles = Role::where('guard_name', 'admin')->pluck('name', 'name')->all();
        $adminRoles = $data->roles->pluck('name', 'name')->all();

        return view('admin.admins.edit', compact('data', 'roles', 'adminRoles'));
    }

    public function profile($id)
    {

        $admin = Admin::where('id', $id)->first();

        return view('admin.profile.index', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminRequest $request, Admin $admin)
    {

        //        $request->validate([
        //            'password' => 'required|string|min:8|confirmed',
        //        ]);
        DB::beginTransaction();
        try {

            $id = $admin->id;

            $input = $request->except('password');

            if (trim($request->password) != '') {
                $input['password'] = bcrypt($request->password);
            }

            $input['status'] = $request->has('status') ? 1 : 0;

            // Update admin
            $admin->update($input);

            // Remove old roles and assign new
            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $admin->assignRole($request->input('roles_name'));

            DB::commit();
            session()->flash('success', 'تم تحديث البيانات بنجاح');

            return redirect()->route('admin.admins.index');

        } catch (\Exception $ex) {
            DB::rollBack();
            if (App::environment('local')) {
                return redirect()->back()->withInput()->with(['error' => $ex->getMessage()]);
            }

            return redirect()->back()->withInput()->with(['error' => 'حدث خطأ ما']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $admin = Admin::findOrFail($id);
            $admin->delete();
            session()->flash('success', 'تمت عملية الحذف بنجاح');

            return redirect()->route('admin.admins.index');

        } catch (\Exception $ex) {
            // في حالة البيئة المحلية إظهار الخطأ، وإلا رسالة عامة
            if (app()->environment('local')) {
                return redirect()->back()->with('error', $ex->getMessage());
            }

            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف المستخدم، يرجى المحاولة لاحقاً.');
        }
    }

    /**
     * Printable admins list (HTML)
     */
    public function print(Request $request)
    {
        $query = Admin::query();

        // exclude owners
        $query->where('roles_name', '!=', '["owner"]');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->email.'%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $admins = $query->orderByDesc('id')->get();

        $total = $admins->count();
        $active = $admins->where('status', 1)->count();

        $settings = $this->getCompanySettings();

        return view('admin.report.admins_print', compact('admins', 'total', 'active', 'settings'));
    }

    private function getCompanySettings()
    {
        $companyName = Setting::where('key', 'site_name')->value('value') ?? 'حسبة';

        return [
            'name' => $companyName,
            'company_name' => $companyName,
            'address' => Setting::where('key', 'address')->value('value') ?? '',
            'phone' => Setting::where('key', 'phone')->value('value') ?? '',
            'email' => Setting::where('key', 'email')->value('value') ?? '',
            'logo' => Setting::where('key', 'logo')->value('value') ?? '',
            'qr_code' => null,
        ];
    }
}
