<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\StageResource;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StageController extends Controller
{
    public function index(Request $request, int $pipelineId): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        Pipeline::where('tenant_id', $tenantId)->findOrFail($pipelineId);
        $stages = PipelineStage::where('pipeline_id', $pipelineId)
            ->where('tenant_id', $tenantId)
            ->withCount('leads')
            ->orderBy('sort_order')
            ->get();
        return response()->json(['data' => StageResource::collection($stages)]);
    }

    public function store(Request $request, int $pipelineId): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        Pipeline::where('tenant_id', $tenantId)->findOrFail($pipelineId);
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'color'      => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'is_won'     => 'nullable|boolean',
            'is_lost'    => 'nullable|boolean',
        ]);
        $stage = PipelineStage::create(array_merge($validated, [
            'pipeline_id' => $pipelineId,
            'tenant_id'   => $tenantId,
        ]));
        return response()->json(['data' => new StageResource($stage)], 201);
    }

    public function show(Request $request, int $pipelineId, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $stage    = PipelineStage::where('pipeline_id', $pipelineId)
            ->where('tenant_id', $tenantId)
            ->withCount('leads')
            ->findOrFail($id);
        return response()->json(['data' => new StageResource($stage)]);
    }

    public function update(Request $request, int $pipelineId, int $id): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $stage     = PipelineStage::where('pipeline_id', $pipelineId)
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);
        $validated = $request->validate([
            'name'       => 'sometimes|string|max:100',
            'color'      => 'sometimes|nullable|string|max:20',
            'sort_order' => 'sometimes|integer',
            'is_won'     => 'sometimes|boolean',
            'is_lost'    => 'sometimes|boolean',
        ]);
        $stage->update($validated);
        return response()->json(['data' => new StageResource($stage)]);
    }

    public function destroy(Request $request, int $pipelineId, int $id): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $stage    = PipelineStage::where('pipeline_id', $pipelineId)
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);
        if ($stage->leads()->count() > 0) {
            return response()->json(['message' => 'Cannot delete stage with leads. Move leads first.'], 422);
        }
        $stage->delete();
        return response()->json(['message' => 'Stage deleted.']);
    }
}
