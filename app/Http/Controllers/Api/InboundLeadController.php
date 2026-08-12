<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Inbound lead ingestion endpoint.
 * Accepts POSTed lead payloads authenticated by API key (Bearer token).
 * Used by Zapier, n8n, Make, Pabbly, and any custom webhook source.
 *
 * POST /api/inbound/leads?tenant={slug}
 * Authorization: Bearer {api_key}
 */
class InboundLeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $apiKey = $this->extractBearerToken($request);
        if (! $apiKey) {
            return response()->json(['error' => 'Missing API key.'], 401);
        }

        $tenantSlug = $request->query('tenant');
        if (! $tenantSlug) {
            return response()->json(['error' => 'Missing tenant parameter.'], 422);
        }

        $tenant = Tenant::where('slug', $tenantSlug)->where('active', true)->first();
        if (! $tenant) {
            return response()->json(['error' => 'Tenant not found or inactive.'], 404);
        }

        $tenantApiKey = $tenant->api_key ?? '';
        if (empty($tenantApiKey) || ! hash_equals($tenantApiKey, $apiKey)) {
            return response()->json(['error' => 'Invalid API key.'], 401);
        }

        // This endpoint is intentionally unauthenticated (API-key only, no
        // ResolveTenant middleware), so bind the resolved tenant explicitly.
        // Without it the BelongsToTenant global scope fails closed and the
        // duplicate-email lookup below always misses — every repeated POST
        // would create a duplicate lead and the dedup branch was dead code.
        app()->instance('current_tenant', $tenant);

        $validator = Validator::make($request->all(), [
            'email'      => 'required|email',
            'first_name' => 'sometimes|string|max:100',
            'last_name'  => 'sometimes|string|max:100',
            'phone'      => 'sometimes|string|max:30',
            'company'    => 'sometimes|string|max:150',
            'source'     => 'sometimes|string|max:50',
            'notes'      => 'sometimes|string|max:5000',
            'tags'       => 'sometimes|array',
            'tags.*'     => 'string|max:50',
            'custom_fields' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $data    = $validator->validated();
        $source  = $data['source'] ?? 'api';

        $existing = Lead::where('tenant_id', $tenant->id)
            ->where('email', $data['email'])
            ->first();

        if ($existing) {
            return response()->json([
                'status'  => 'duplicate',
                'lead_id' => $existing->id,
                'message' => 'Lead with this email already exists.',
            ], 200);
        }

        $lead = Lead::create([
            'tenant_id'     => $tenant->id,
            'source'        => $source,
            'first_name'    => $data['first_name'] ?? null,
            'last_name'     => $data['last_name'] ?? null,
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'company'       => $data['company'] ?? null,
            'notes'         => $data['notes'] ?? null,
            'status'        => 'new',
            'lead_score'    => 0,
            'custom_fields' => $data['custom_fields'] ?? null,
        ]);

        if (! empty($data['tags'])) {
            foreach ($data['tags'] as $tagName) {
                $tag = \App\Models\Tag::firstOrCreate(
                    ['tenant_id' => $tenant->id, 'name' => trim($tagName)],
                    ['tenant_id' => $tenant->id, 'name' => trim($tagName)]
                );
                $lead->tags()->syncWithoutDetaching([$tag->id]);
            }
        }

        return response()->json([
            'status'  => 'created',
            'lead_id' => $lead->id,
            'message' => 'Lead created successfully.',
        ], 201);
    }

    private function extractBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return $request->query('api_key');
    }
}
