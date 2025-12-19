<?php

namespace App\Services;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserService
{

    public function index()
    {
        if (!Gate::allows('viewAny', User::class)) {
            return to_route('home')->withErrors(['message' => 'ليس مسموحا لك بهذا']);
        };
        $admins = User::paginate(100);
        return view('admins.index', compact('admins'));
    }

    public function create()
    {
        if (!Gate::allows('create', User::class)) {
            return to_route('home')->withErrors(['message' => 'ليس مسموحا لك بهذا']);
        };
        $permissions = Permission::all();
        return view('admins.create', compact('permissions'));
    }

    public function store(CreateUserRequest $request)
    {
        if (!Gate::allows('create', User::class)) {
            return to_route('home')->withErrors(['message' => 'ليس مسموحا لك بهذا']);
        };
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        $user->permissions()->sync($validated['per_ids']);
        return to_route("admins.index")->with("success", "تم إضافة المدير بنجاح");
    }

    public function edit(string $id)
    {
        $admin = User::findOrFail($id);
        if (!Gate::allows('update', $admin)) {
            return to_route('home')->withErrors(['message' => 'ليس مسموحا لك بهذا']);
        };
        $permissions = Permission::all();
        return view('admins.edit', compact('admin', 'permissions'));
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $validated = $request->validated();
        $user = User::findOrFail($id);
        if (!Gate::allows('update', $user)) {
            return to_route('home')->withErrors(['message' => 'ليس مسموحا لك بهذا']);
        };
        $validated['password'] = $request->has('password') && $request->password != "" ? bcrypt($request->password) : $user->password;
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password']
        ]);
        if (isset($validated['per_ids']) && $user->id !== Auth::id()) {
            $user->permissions()->sync($validated['per_ids']);
        }
        return to_route("admins.index")->with("success", "تم تعديل بيانات المدير بنجاح");
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if (!Gate::allows('delete', $user)) {
            return response()->json(['success' => false, 'message' => 'ليس مسموحا لك بهذا']);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' =>  "تم حذف المدير بنجاح"]);
    }
}
