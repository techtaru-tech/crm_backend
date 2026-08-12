<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $perPage   = min((int) $request->input('per_page', 50), 200);
        $paginator = User::where('tenant_id', $tenantId)
            ->with('roles')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json(ApiResource::paginatedResponse($paginator, UserResource::class));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $user     = User::where('tenant_id', $tenantId)->with('roles')->findOrFail($id);
        return response()->json(['data' => new UserResource($user)]);
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $data     = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'timezone' => 'nullable|string|max:100',
            'role'     => 'nullable|string|in:admin,member',
        ]);

        $user = User::create([
            'tenant_id' => $tenantId,
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'timezone'  => $data['timezone'] ?? null,
        ]);

        if (! empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        return response()->json(['data' => new UserResource($user->load('roles')), 'message' => 'User created.'], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $user     = User::where('tenant_id', $tenantId)->findOrFail($id);

        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'timezone' => 'nullable|string|max:100',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json(['data' => new UserResource($user->load('roles')), 'message' => 'User updated.']);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $apiUserId = $request->attributes->get('user_id');
        $user      = User::where('tenant_id', $tenantId)->findOrFail($id);

        if ($apiUserId && (int) $apiUserId === $user->id) {
            return response()->json(['message' => 'Cannot delete your own account.'], 422);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted.']);
    }
}
