<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Permission;
use Image;
use Storage;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_users')->only('create');
        $this->middleware('permission:read_users')->only('index');
        $this->middleware('permission:update_users')->only('edit');
        $this->middleware('permission:delete_users')->only('destroy');
    } // end of constructor

    public function index(Request $request)
    {
        $users = User::whereRoleIs('admin')->where(function($q) use($request) {
          return $q->when($request->search, function ($query) use ($request) {
            return $query->where('first_name', 'like', '%' . $request->search . '%')->orWhere('last_name', 'like', '%' . $request->search . '%');
        });
      })->latest()->paginate(5);
        return view('dashboard.users.index', compact('users'));
    } // end of index
    public function create()
    {
        $permissions = Permission::all();
        return view('dashboard.users.create', compact('permissions'));
    } // end of Create

    public function store(Request $request)
    {
        $request->validate([
          'first_name'  => 'required',
          'last_name'   => 'required',
          'email'       => 'required|email|unique:users,email',
          'image'       => 'image',
          'password'    => 'required|confirmed',
          'permissions' => 'required|min:1',
        ]);

        $request_data = $request->except(['password', 'password_confirmation', 'permissions', 'image']);
        $request_data['password'] = bcrypt($request->password);

        if ($request->hasFile('image')) {
            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/users_images/' . $request->image->hashName()));
        } // end of if

        $request_data['image'] = $request->image->hashName();

        $user = User::create($request_data);
        $user->attachRole('admin');
        $user->attachPermissions($request->permissions);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.users.index');
    } // end of Store

    public function edit($id)
    {
        $permissions = Permission::all();
        $user = User::find($id);
        return view('dashboard.users.edit', compact('user', 'permissions'));
    } // end of Edit

    public function update(Request $request, User $user)
    {
        $request->validate([

        'first_name'  => 'required',
        'last_name'   => 'required',
        'email'       => ['required', Rule::unique('users')->ignore($user->id)],
        'image'       => 'image',
        'permissions' => 'required',
      ]);

        $request_data = $request->except(['permissions', 'image']);

        if ($request->hasFile('image')) {
            if ($user->image != 'default.png') {
                Storage::disk('public_uploads')->delete("/users_images/" . $user->image);
            } // end of if

            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/users_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        } // end of if

        $user->update($request_data);

        $user->syncPermissions($request->permissions);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.users.index');
    } // end of Update

    public function destroy(User $user)
    {
        if ($user->image != 'default.png') {
            Storage::disk('public_uploads')->delete("/users_images/" . $user->image);
        } // end of if
        $user->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.users.index');
    } // end of Destroy
}
