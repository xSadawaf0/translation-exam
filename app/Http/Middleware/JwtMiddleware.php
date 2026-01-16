<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return api_response(null, Response::HTTP_UNAUTHORIZED, 'Unauthorized', false);
        }
        $payload = $this->decodeJwt($token);
        if (!$payload || !isset($payload['exp']) || time() > $payload['exp']) {
            return api_response(null, Response::HTTP_UNAUTHORIZED, 'Token expired or invalid', false);
        }
        return $next($request);
    }

    private function decodeJwt($jwt)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        return $payload;
    }
}
