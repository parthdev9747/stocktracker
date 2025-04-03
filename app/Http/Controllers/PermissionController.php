<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use App\DataTables\PermissionsDataTable;
use App\Http\Requests\PermissionRequest;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    protected $moduleName;
    protected $moduleRoute;
    protected $moduleView = "permissions";
    protected $model;

    public function __construct(Permission $model)
    {

        $this->moduleName = 'Permission';
        $this->moduleRoute = url('permission');
        $this->model = $model;

        View::share('module_name', $this->moduleName);
        View::share('module_route', $this->moduleRoute);
        View::share('module_view', $this->moduleView);
    }

    public function index(PermissionsDataTable $dataTable)
    {
        return $dataTable->render($this->moduleView . '.index');
    }

    public function create()
    {
        return view('general.create');
    }

    public function store(PermissionRequest $request)
    {
        try {
            $permission = $this->model->create(["name" => strtolower(trim($request->name)), 'group_name' => strtolower(trim($request->group_name))]);
            if ($permission) {
                return redirect($this->moduleRoute)->with('success', 'Permission created successfully!');
            } else {
                return redirect($this->moduleRoute)->with('error', 'Something went wrong!');
            }
        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $data['result'] = $this->model->where('id', $id)->first();
        return view('general.edit', $data);
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        try {
            $permission = $permission->update(["name" => strtolower(trim($request->name)), 'group_name' => strtolower(trim($request->group_name))]);
            if ($permission) {
                return redirect($this->moduleRoute)->with('success', 'Permission updated successfully!');
            } else {
                return redirect($this->moduleRoute)->with('error', 'Something went wrong!');
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
            $response['message'] = 'Permission deleted successfully!';
            $response['status'] = true;
        } else {
            $response['message'] = $this->moduleName . " not Found!";
            $response['status'] = false;
        }
        return response()->json($response);
    }
}
