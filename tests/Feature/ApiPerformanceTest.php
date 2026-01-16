<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private function getJwtToken(): string
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => 'api-client',
        ];
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payloadEncoded = base64_encode(json_encode($payload));
        $base = $header . '.' . $payloadEncoded;
        $signature = hash_hmac('sha256', $base, config('jwt.secret'), true);
        $signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        return $base . '.' . $signature;
    }

    public function test_translations_index_performance()
    {
        Artisan::call('db:seed');
        $token = $this->getJwtToken();
        $start = microtime(true);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/translations');
        $duration = (microtime(true) - $start) * 1000;
        $response->assertStatus(200);
        $this->assertLessThan(200, $duration, 'API took too long: ' . $duration . 'ms');
    }
}
