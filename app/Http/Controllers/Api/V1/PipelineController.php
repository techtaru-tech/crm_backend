<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\PipelineResource;
use App\Models\Pipeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $perPage  = min((int) $request->input('per_page', 50), 200);
        $paginator = Pipeline::where('tenant_id', $tenantId)
            ->withCount('stages')
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json(ApiResource::paginatedResponse($paginator, PipelineResource::class));
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_default'  => 'nullable|boolean',
        ]);
        $pipeline = Pipeline::create(array_merge($validated, ['tenant_id' => $tenantId]));
        return response()->json(['data' => new PipelineResource($pipeline)], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $pipeline = Pipeline::where('tenant_id', $tenantId)->with('stages')->findOrFail($id);
        return response()->json(['data' => new PipelineResource($pipeline)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $pipeline  = Pipeline::where('tenant_id', $tenantId)->findOrFail($id);
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:100',
            'description' => 'sometimes|nullable|string',
            'is_default'  => 'sometimes|boolean',
        ]);
        $pipeline->update($validated);
        return response()->json(['data' => new PipelineResource($pipeline)]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $pipeline = Pipeline::where('tenant_id', $tenantId)->findOrFail($id);
        if ($pipeline->is_default) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete the default pipeline.'], 422);
        }
        $pipeline->delete();
        return response()->json(['message' => 'Pipeline deleted.']);
    }
}
