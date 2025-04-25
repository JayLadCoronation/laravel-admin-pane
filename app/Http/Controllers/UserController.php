<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereNull('deleted_at')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $countries = Country::all();
        return view('users.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'gender' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'profile_pic' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('profile_pic')) {
            $imageName = time() . '.' . $request->profile_pic->extension();
            $request->profile_pic->move(public_path('uploads'), $imageName);
        }

        $validated['profile_pic'] = $imageName ?? null;
        $validated['password'] = Hash::make($validated['password']);
        $validated['created_at'] = time();
        $validated['updated_at'] = time();

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $countries = Country::all();
        $states = State::where('country_id', $user->country_id)->get();
        $cities = City::where('state_id', $user->state_id)->get();

        return view('users.edit', compact('user', 'roles', 'countries', 'states', 'cities'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::findOrFail($id);

        $user->fill($request->except('profile_pic'));
        $user->update(
            [
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'updated_at' => time()
            ]
        );

        if ($request->hasFile('profile_pic')) {
            $imageName = time() . '.' . $request->profile_pic->extension();
            $request->profile_pic->move(public_path('uploads'), $imageName);
            $user->profile_pic = $imageName;
        }

        $user->updated_at = time();
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->deleted_at = time(); // soft delete
        $user->save();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

}
