<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Services\Voice\TwilioVoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoiceController extends Controller
{
    public function initiate(Request $request, TwilioVoiceService $service): JsonResponse
    {
        $data = $request->validate([
            'lead_id' => 'required|integer',
            'user_id' => 'nullable|integer',
        ]);

        $lead = Lead::query()->find($data['lead_id']);
        if (! $lead) {
            return response()->json(['error' => __('controllers/voice.lead_not_found')], 404);
        }

        // Tenant authorization happens via BelongsToTenant global scope —
        // a user from another tenant cannot even resolve this Lead row.
        $caller = null;
        if (! empty($data['user_id'])) {
            $caller = User::query()->where('tenant_id', $lead->tenant_id)->find($data['user_id']);
        }
        $caller = $caller ?: $request->user();

        if (! $caller) {
            return response()->json(['error' => __('controllers/voice.no_authenticated_caller')], 401);
        }

        $call = $service->initiateCall($lead, $caller);

        return response()->json([
            'call_id'     => $call->id,
            'external_id' => $call->external_id,
            'status'      => $call->status,
        ]);
    }
}
