<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKey
{
    private const SESSION_KEY = 'api_key_verified';
    private const HEADER_NAME = 'X-API-Key';

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isAuthRoute($request)) {
            return $next($request);
        }

        $validKey = config('services.whatsapp.api_key');

        if (!$validKey) {
            return response()->view('auth.apikey', [
                'error' => 'API Key não configurada no servidor.',
            ], 500);
        }

        if ($this->isVerified($request)) {
            return $next($request);
        }

        if ($this->attemptVerify($request, $validKey)) {
            $request->session()->put(self::SESSION_KEY, true);
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'API Key inválida.'], 401);
        }

        return redirect()->route('auth.apikey.form');
    }

    private function isAuthRoute(Request $request): bool
    {
        $route = $request->route();

        if (!$route) {
            return false;
        }

        $name = $route->getName();

        return in_array($name, [
            'auth.apikey.form',
            'auth.apikey.verify',
        ]);
    }

    private function isVerified(Request $request): bool
    {
        return $request->session()->get(self::SESSION_KEY, false);
    }

    private function attemptVerify(Request $request, string $validKey): bool
    {
        $provided = $request->header(self::HEADER_NAME)
            ?? $request->query('api_key')
            ?? $request->input('api_key');

        if (!$provided) {
            return false;
        }

        return hash_equals($validKey, $provided);
    }
}
