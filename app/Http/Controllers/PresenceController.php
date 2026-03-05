<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PresenceController extends Controller
{
    public function ping(): JsonResponse
    {
        $userId = auth()->id();
        Cache::put("presence:user:{$userId}", now()->timestamp, now()->addMinutes(3));

        return response()->json(['ok' => true]);
    }

    public function status(int $userId): JsonResponse
    {
        $last = (int) (Cache::get("presence:user:{$userId}") ?? 0);
        $online = $last > 0 && (now()->timestamp - $last) <= 45;

        return response()->json([
            'ok' => true,
            'online' => $online,
            'last_seen' => $last,
        ]);
    }
}
