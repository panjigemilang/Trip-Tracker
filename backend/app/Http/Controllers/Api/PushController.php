<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PushController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'nullable|string',
            'keys.auth' => 'nullable|string',
            'content_encoding' => 'nullable|string',
        ]);

        $endpoint = $request->input('endpoint');
        $key = $request->input('keys.p256dh');
        $token = $request->input('keys.auth');
        $contentEncoding = $request->input('content_encoding', 'aesgcm');

        $request->user()->updatePushSubscription($endpoint, $key, $token, $contentEncoding);

        return response()->json([
            'success' => true,
            'message' => 'Subscribed successfully'
        ]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $endpoint = $request->input('endpoint');
        
        $request->user()->deletePushSubscription($endpoint);

        return response()->json([
            'success' => true,
            'message' => 'Unsubscribed successfully'
        ]);
    }
}
