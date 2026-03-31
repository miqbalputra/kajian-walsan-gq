<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Simpan subscription baru dari browser.
     */
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            [
                'user_id'  => auth()->id(),
                'endpoint' => $request->endpoint,
            ],
            [
                'p256dh' => $request->input('keys.p256dh'),
                'auth'   => $request->input('keys.auth'),
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Hapus subscription (user cabut izin).
     */
    public function destroy(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('user_id', auth()->id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['status' => 'deleted']);
    }
}
