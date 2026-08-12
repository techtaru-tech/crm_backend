<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint'        => 'required|string|max:1000',
            'keys.p256dh'     => 'required|string',
            'keys.auth'       => 'required|string',
            'contentEncoding' => 'nullable|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id'          => Auth::id(),
                'p256dh_key'       => $data['keys']['p256dh'],
                'auth_token'       => $data['keys']['auth'],
                'content_encoding' => $data['contentEncoding'] ?? 'aesgcm',
            ]
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $endpoint = $request->input('endpoint');
        PushSubscription::where('user_id', Auth::id())->where('endpoint', $endpoint)->delete();
        return response()->json(['success' => true]);
    }
}
