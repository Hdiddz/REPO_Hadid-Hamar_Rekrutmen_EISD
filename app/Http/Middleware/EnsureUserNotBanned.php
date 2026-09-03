<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNotBanned
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            $user = Auth::user();
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $duration = $user->banned_until
                ? 'sampai '.$user->banned_until->translatedFormat('d F Y, H:i').' WIB'
                : 'secara permanen';
            $reason = $user->ban_reason ?: 'Pelanggaran standar etika rekrutmen KerjaLokal.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Akun Anda sedang dibekukan oleh Administrator {$duration}. Alasan: {$reason}",
                ], 403);
            }

            return redirect()->route('login')->with('error', "Akun Anda sedang dibekukan oleh Administrator {$duration}. Alasan: {$reason}");
        }

        return $next($request);
    }
}
