<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::where('tenant_id', session('tenant_id'))
            ->where('rol', '!=', 'superadmin')
            ->orderBy('name')
            ->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function crear()
    {
        return view('usuarios.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:191',
            'email' => 'required|email|unique:users,email',
            'rol'   => 'required|in:dueno,auxiliar',
        ]);

        $password = Str::random(10);

        $usuario = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($password),
            'tenant_id' => session('tenant_id'),
            'rol'       => $request->rol,
            'activo'    => true,
        ]);

        // Enviar credenciales por correo
        Mail::raw(
            "Hola {$request->name},\n\n" .
            "El administrador de " . auth()->user()->tenant->nombre . " te ha creado una cuenta en POS Ferretero.\n\n" .
            "Accede en: https://pos-ferretero.avanzas.digital/login\n" .
            "Usuario: {$request->email}\n" .
            "Contrasena temporal: {$password}\n\n" .
            "Te recomendamos cambiar tu contrasena al ingresar.\n\n" .
            "Avanzas Digital - Tu exito es nuestro objetivo",
            function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Acceso a POS Ferretero - ' . auth()->user()->tenant->nombre);
            }
        );

        return response()->json([
            'success' => true,
            'mensaje' => 'Usuario creado y credenciales enviadas a ' . $request->email,
        ]);
    }

    public function toggleActivo(User $usuario)
    {
        // Verificar que el usuario pertenece al mismo tenant
        if ($usuario->tenant_id !== session('tenant_id')) {
            return response()->json(['success' => false, 'mensaje' => 'No autorizado'], 403);
        }

        // No permitir desactivar al propio usuario
        if ($usuario->id === auth()->id()) {
            return response()->json(['success' => false, 'mensaje' => 'No puedes desactivarte a ti mismo'], 422);
        }

        $usuario->update(['activo' => !$usuario->activo]);

        return response()->json([
            'success' => true,
            'activo'  => $usuario->activo,
            'mensaje' => $usuario->activo ? 'Usuario activado' : 'Usuario desactivado',
        ]);
    }

    public function eliminar(User $usuario)
    {
        if ($usuario->tenant_id !== session('tenant_id')) {
            return response()->json(['success' => false, 'mensaje' => 'No autorizado'], 403);
        }

        if ($usuario->id === auth()->id()) {
            return response()->json(['success' => false, 'mensaje' => 'No puedes eliminarte a ti mismo'], 422);
        }

        $usuario->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Usuario eliminado correctamente',
        ]);
    }

    public function cambiarPassword(Request $request, User $usuario)
    {
        if ($usuario->tenant_id !== session('tenant_id')) {
            return response()->json(['success' => false, 'mensaje' => 'No autorizado'], 403);
        }

        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $usuario->update([
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Contrasena actualizada correctamente',
        ]);
    }
}