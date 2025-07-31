<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function destroy(User $user)
    {
        // Medida de seguridad: Un administrador no puede eliminarse a sí mismo.
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', "El usuario '{$userName}' ha sido eliminado con éxito.");
    }


    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.users.index', ['users' => $users]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'role' => ['required', Rule::in(['user', 'admin'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make('password'),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Usuario creado con éxito, La contraseña por defecto es "password".');
    }

    public function update(Request $request, User $user)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:user,admin'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Solo actualiza la contraseña si se proporcionó una nueva
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'Usuario actualizado con éxito.');
    }
}
