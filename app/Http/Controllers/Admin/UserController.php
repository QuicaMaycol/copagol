<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin'); // Apply admin middleware to all methods
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
        }

        $users = $query->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:255'],
            'is_verified' => ['boolean'],
            'plan_type' => ['required', 'in:basic,premium'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->phone_number = $validatedData['phone_number'];
        $user->country = $validatedData['country'];
        $user->is_verified = $validatedData['is_verified'] ?? false;
        $user->plan_type = $validatedData['plan_type'];

        if (isset($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado con éxito.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $usuario)
    {
        Log::info('Attempting to delete user: ' . $usuario->id . ' with attributes: ' . json_encode($usuario->toArray()));
        DB::beginTransaction();
        try {
            // Delete related Campeonatos
            $usuario->campeonatos()->delete();

            // Delete related Equipos
            $usuario->equipos()->delete();

            // Detach from delegated Campeonatos
            $usuario->delegatedCampeonatos()->detach();

            $usuario->delete();
            DB::commit();
            Log::info('User ' . $usuario->id . ' deleted successfully.');
            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user ' . $usuario->id . ': ' . $e->getMessage());
            return redirect()->route('admin.usuarios.index')->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
