<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $perPage  = min((int) $request->input('per_page', 50), 200);
        $paginator = Tag::where('tenant_id', $tenantId)
            ->withCount('leads')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json(ApiResource::paginatedResponse($paginator, TagResource::class));
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $validated = $request->validate([
            'name'  => 'required|string|max:80',
            'color' => 'nullable|string|max:20',
        ]);
        $tag = Tag::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => $validated['name']],
            ['color' => $validated['color'] ?? null]
        );
        return response()->json(['data' => new TagResource($tag)], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $tag      = Tag::where('tenant_id', $tenantId)->withCount('leads')->findOrFail($id);
        return response()->json(['data' => new TagResource($tag)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $tag       = Tag::where('tenant_id', $tenantId)->findOrFail($id);
        $validated = $request->validate([
            'name'  => 'sometimes|string|max:80',
            'color' => 'sometimes|nullable|string|max:20',
        ]);
        $tag->update($validated);
        return response()->json(['data' => new TagResource($tag)]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $tag      = Tag::where('tenant_id', $tenantId)->findOrFail($id);
        $tag->leads()->detach();
        $tag->delete();
        return response()->json(['message' => 'Tag deleted.']);
    }
}
