<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use App\DataTables\RolesDataTable;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;


class RoleController extends Controller
{
    protected $moduleName;
    protected $moduleRoute;
    protected $moduleView = "roles";
    protected $model;

    function __construct(Role $model)
    {
        $this->moduleName = 'Role';
        $this->moduleRoute = url('role');
        $this->model = $model;

        // Add middleware here instead of using the HasMiddleware interface
        $this->middleware('permission:list-role|add-role|edit-role|delete-role', ['only' => ['index']]);
        $this->middleware('permission:add-role', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-role', ['only' => ['destroy']]);

        View::share('module_name', $this->moduleName);
        View::share('module_route', $this->moduleRoute);
        View::share('module_view', $this->moduleView);
    }

    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render($this->moduleView . '.index');
    }

    public function create()
    {
        $permission = Permission::get();
        $data['groupedPermissions'] = $permission->groupBy('group_name')->all();
        return view('general.create', $data);
    }

    public function store(RoleRequest $request)
    {
        try {

            $permissionsID = array_map(
                function ($value) {
                    return (int)$value;
                },
                $request->input('permission')
            );

            $role = $this->model->create(['name' => $request->input('name')]);
            $role->syncPermissions($permissionsID);

            if ($role) {
                return redirect($this->moduleRoute)->with('success', 'Role created successfully');
            } else {
                return redirect($this->moduleRoute)->with('error', 'Something went wrong');
            }
        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        $data['result'] = $role;
        $permission = Permission::get();
        $data['groupedPermissions'] = $permission->groupBy('group_name')->all();
        $data['rolePermissions'] = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('general.edit', $data);
    }

    public function update(RoleRequest $request, Role $role)
    {
        try {

            $role->update(['name' => trim($request->name)]);

            $permissionsID = array_map(
                function ($value) {
                    return (int)$value;
                },
                $request->input('permission')
            );

            $role->syncPermissions($permissionsID);

            if ($role) {
                return redirect($this->moduleRoute)->with('success', 'Role updated successfully');
            } else {
                return redirect($this->moduleRoute)->with('error', 'Something went wrong');
            }
        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $response = [];
        $data = $this->model->where('id', $id)->first();
        if ($data) {
            $data->delete();
            $response['message'] = 'Role deleted successfully';
            $response['status'] = true;
        } else {
            $response['message'] = 'Role not Found!';
            $response['status'] = false;
        }
        return response()->json($response);
    }
}
