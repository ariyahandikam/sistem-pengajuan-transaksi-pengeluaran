<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['role_id' => $data['role_id'] ?? null, 'status' => $data['status'] ?? null, 'module' => 'User Management'])
            ->log('Created user');

        if (!empty($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if ($role) {
                $user->role()->associate($role);
                $user->save();
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole($role->name);
                }
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User created');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $old = Arr::except($user->getOriginal(), ['password']);
        $user->update($data);
        
        $new = Arr::except($data, ['password']);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['old' => $old, 'new' => $new, 'module' => 'User Management'])
            ->log('Updated user');

        if (!empty($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if ($role) {
                $user->role()->associate($role);
                $user->save();
                if (method_exists($user, 'syncRoles')) {
                    $user->syncRoles([$role->name]);
                }
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['module' => 'User Management'])
            ->log('Deleted user');

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
