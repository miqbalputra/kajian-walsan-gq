<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Endpoint khusus untuk reset password dari n8n.
     * Menggunakan secret key agar tidak bisa ditembak sembarang orang.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'secret' => 'required',
        ]);

        // Ganti 'RAHASIA-N8N-WALSAN' dengan kode apa saja yang Anda suka
        if ($request->secret !== 'RAHASIA-N8N-WALSAN') {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        try {
            $user = User::findOrFail($request->user_id);
            $user->password = Hash::make('PasswordSementara123');
            $user->save();

            // Opsional: Hapus cache user jika ada
            // Cache::forget('user_'.$user->id);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset untuk ' . $user->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
