<?php
namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::whereNull('deleted_at')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);

        Role::create([
            'name' => $request->name,
            'created_at' => time(),
        ]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|unique:roles,name,' . $id]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->updated_at = time();
        $role->save();

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->deleted_at = time();
        $role->save();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }
}

