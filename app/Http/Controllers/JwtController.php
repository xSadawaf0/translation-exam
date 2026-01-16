<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JwtController extends Controller
{
    public function issue(Request $request)
    {
        $ttl = (int)($request->input('ttl', 3600)); 
        $payload = [
            'iat' => time(),
            'exp' => time() + $ttl,
            'sub' => 'api-client',
        ];
        $jwt = $this->encodeJwt($payload);
        return response()->json(['token' => $jwt, 'expires_in' => $ttl]);
    }

    private function encodeJwt(array $payload): string
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode($payload));
        $base = $header . '.' . $payload;
        $signature = hash_hmac('sha256', $base, config('jwt.secret'), true);
        $signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        return $base . '.' . $signature;
    }
}
