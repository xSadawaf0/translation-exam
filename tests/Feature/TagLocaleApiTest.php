<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagLocaleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_tag()
    {
        $token = $this->getJwtToken();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/tags', ['name' => 'General']);
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'General']);
    }

    public function test_create_locale()
    {
        $token = $this->getJwtToken();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/locales', [
            'code' => 'en',
            'name' => 'English',
        ]);
        $response->assertStatus(201)
            ->assertJsonFragment(['code' => 'en', 'name' => 'English']);
    }

    public function test_list_tags_and_locales()
    {
        $token = $this->getJwtToken();
        Tag::factory()->create(['name' => 'General']);
        Locale::factory()->create(['code' => 'en', 'name' => 'English']);
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/tags')->assertStatus(200)->assertJsonFragment(['name' => 'General']);
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/locales')->assertStatus(200)->assertJsonFragment(['code' => 'en']);
    }

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
}
