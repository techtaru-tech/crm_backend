<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\AutomationResource;
use App\Models\Automation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $perPage   = min((int) $request->input('per_page', 15), 100);
        $paginator = Automation::where('tenant_id', $tenantId)->withCount('steps')->paginate($perPage);
        return response()->json(ApiResource::paginatedResponse($paginator, AutomationResource::class));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $automation = Automation::where('tenant_id', $tenantId)->withCount('steps')->findOrFail($id);
        return response()->json(['data' => new AutomationResource($automation)]);
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        $data     = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'enabled'        => 'boolean',
            'trigger_type'   => 'required|string|in:' . implode(',', array_keys(Automation::TRIGGERS)),
            'trigger_config' => 'nullable|array',
        ]);

        $automation = Automation::create(array_merge($data, ['tenant_id' => $tenantId]));

        return response()->json(['data' => new AutomationResource($automation), 'message' => 'Automation created.'], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tenantId   = $request->attributes->get('tenant_id');
        $automation = Automation::where('tenant_id', $tenantId)->findOrFail($id);

        $data = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'description'    => 'nullable|string',
            'enabled'        => 'boolean',
            'trigger_type'   => 'sometimes|string|in:' . implode(',', array_keys(Automation::TRIGGERS)),
            'trigger_config' => 'nullable|array',
        ]);

        $automation->update($data);

        return response()->json(['data' => new AutomationResource($automation), 'message' => 'Automation updated.']);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenantId   = $request->attributes->get('tenant_id');
        $automation = Automation::where('tenant_id', $tenantId)->findOrFail($id);
        $automation->delete();
        return response()->json(['message' => 'Automation deleted.']);
    }
}
