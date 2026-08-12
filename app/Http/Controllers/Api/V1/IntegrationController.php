<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\IntegrationResource;
use App\Http\Responses\ApiErrorResponse;
use App\Integrations\IntegrationRegistry;
use App\Models\Integration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId  = $request->attributes->get('tenant_id');
        $perPage   = min((int) $request->input('per_page', 15), 100);
        $query     = Integration::where('tenant_id', $tenantId);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('enabled')) {
            $query->where('enabled', filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $paginator = $query->orderBy('name')->paginate($perPage);
        return response()->json(ApiResource::paginatedResponse($paginator, IntegrationResource::class));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId    = $request->attributes->get('tenant_id');
        $integration = Integration::where('tenant_id', $tenantId)->find($id);

        if (! $integration) {
            return ApiErrorResponse::notFound('Integration not found.');
        }

        return response()->json(['data' => new IntegrationResource($integration)]);
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');

        $data = $request->validate([
            'type'          => 'required|string|max:60',
            'name'          => 'required|string|max:120',
            'enabled'       => 'boolean',
            'config'        => 'sometimes|array',
            'field_mapping' => 'sometimes|array',
            'filter_config' => 'sometimes|array',
        ]);

        if (! array_key_exists($data['type'], IntegrationRegistry::INTEGRATIONS)) {
            return ApiErrorResponse::validation(
                ['type' => ['Unknown integration type: ' . $data['type']]],
                'Unsupported integration type.'
            );
        }

        $integration = new Integration();
        $integration->tenant_id    = $tenantId;
        $integration->type         = $data['type'];
        $integration->name         = $data['name'];
        $integration->enabled      = $data['enabled'] ?? false;
        $integration->status       = Integration::STATUS_DISCONNECTED;
        $integration->field_mapping = $data['field_mapping'] ?? null;
        $integration->filter_config = $data['filter_config'] ?? null;

        if (! empty($data['config'])) {
            $integration->setConfig($data['config']);
        }

        $integration->save();

        return response()->json(['data' => new IntegrationResource($integration)], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tenantId    = $request->attributes->get('tenant_id');
        $integration = Integration::where('tenant_id', $tenantId)->find($id);

        if (! $integration) {
            return ApiErrorResponse::notFound('Integration not found.');
        }

        $data = $request->validate([
            'name'          => 'sometimes|string|max:120',
            'enabled'       => 'sometimes|boolean',
            'config'        => 'sometimes|array',
            'field_mapping' => 'sometimes|array',
            'filter_config' => 'sometimes|array',
        ]);

        if (isset($data['name']))         $integration->name          = $data['name'];
        if (isset($data['enabled']))      $integration->enabled       = $data['enabled'];
        if (isset($data['field_mapping'])) $integration->field_mapping = $data['field_mapping'];
        if (isset($data['filter_config'])) $integration->filter_config = $data['filter_config'];

        if (isset($data['config'])) {
            $integration->setConfig($data['config']);
        }

        $integration->save();

        return response()->json(['data' => new IntegrationResource($integration)]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenantId    = $request->attributes->get('tenant_id');
        $integration = Integration::where('tenant_id', $tenantId)->find($id);

        if (! $integration) {
            return ApiErrorResponse::notFound('Integration not found.');
        }

        $integration->delete();
        return response()->json(['status' => 'ok', 'message' => 'Integration deleted.']);
    }

    public function types(): JsonResponse
    {
        $types = collect(IntegrationRegistry::INTEGRATIONS)->keys()->map(function ($type) {
            return [
                'type'        => $type,
                'label'       => IntegrationRegistry::getLabel($type),
                'description' => IntegrationRegistry::getDescription($type),
            ];
        });

        return response()->json(['data' => $types]);
    }
}
