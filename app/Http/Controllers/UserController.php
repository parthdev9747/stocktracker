<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Models\User;
use Hash;
use DB;
use Illuminate\Support\Arr;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;



class UserController extends Controller
{
    protected $moduleName;
    protected $moduleRoute;
    protected $moduleView = "users";
    protected $model;


    function __construct(User $model)
    {

        $this->moduleName = 'Users';
        $this->moduleRoute = url('user');
        $this->model = $model;

        $this->middleware('permission:list-user|add-user|edit-user|delete-user', ['only' => ['index']]);
        $this->middleware('permission:list-user|add-user', ['only' => ['create', 'store']]);
        $this->middleware('permission:list-user|edit-user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:list-user|delete-user', ['only' => ['destroy']]);


        View::share('module_name', $this->moduleName);
        View::share('module_route', $this->moduleRoute);
        View::share('module_view', $this->moduleView);
    }

    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    public function create()
    {
        $data['roles'] = Role::pluck('name', 'name')->all();
        return view('general.create', $data);
    }

    public function store(UserRequest $request)
    {
        try {

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);

            $user = $this->model->create($input);
            $user->assignRole($request->input('role'));

            if ($user) {
                return redirect($this->moduleRoute)->with('success', 'User created successfully');
            } else {
                return redirect($this->moduleRoute)->with('error', 'Something went wrong');
            }
        } catch (\Exception $e) {
            return redirect($this->moduleRoute)->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::find($id);
        $data['result'] = User::find($id);
        $data['roles'] = Role::pluck('name', 'name')->all();
        $data['userRole'] = $data['result']->roles->pluck('name', 'name')->all();

        return view('general.edit', $data);
    }

    public function update(UserRequest $request, User $user)
    {
        try {

            $input = $request->all();
            if (!empty($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            } else {
                $input = Arr::except($input, array('password'));
            }

            $user->update($input);
            DB::table('model_has_roles')->where('model_id', $user->id)->delete();

            $user->assignRole($request->input('role'));

            if ($user) {
                return redirect($this->moduleRoute)->with('success', 'User updated successfully');
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
            $response['message'] = 'User deleted successfully';
            $response['status'] = true;
        } else {
            $response['message'] = $this->moduleName . " not Found!";
            $response['status'] = false;
        }
        return response()->json($response);
    }
}
