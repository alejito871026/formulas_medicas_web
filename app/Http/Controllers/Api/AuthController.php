<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol' => ['nullable', 'in:paciente,despachador,administrativo'],
        ]);

        $roleName = $validated['rol'] ?? 'paciente';
        $role = Role::query()->firstWhere('nombre', $roleName);

        if (! $role) {
            return response()->json(['error' => 'Rol no configurado en base de datos.'], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $role->id,
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me(): JsonResponse
    {
        $user = JWTAuth::user();

        return response()->json($user?->load('role'));
    }

    public function logout(): JsonResponse
    {
        if ($token = JWTAuth::getToken()) {
            JWTAuth::invalidate($token);
        }

        return response()->json(['message' => 'Sesion cerrada correctamente']);
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => ((int) config('jwt.ttl', 60)) * 60,
        ]);
    }
}
