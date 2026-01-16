<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationApiTest extends TestCase
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

    public function test_create_translation_with_tags()
    {
        $token = $this->getJwtToken();
        $locale = Locale::factory()->create();
        $tag = Tag::factory()->create();
        $data = [
            'key' => 'greeting',
            'content' => 'Hello',
            'locale_id' => $locale->id,
            'tag_id' => $tag->id,
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/translations', $data);
        $response->assertStatus(201)
            ->assertJsonFragment(['key' => 'greeting', 'content' => 'Hello'])
            ->assertJsonPath('data.tags.0.id', $tag->id);
    }

    public function test_update_translation_tags()
    {
        $token = $this->getJwtToken();
        $locale = Locale::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $translation = Translation::factory()->create(['locale_id' => $locale->id]);
        $translation->tags()->sync([$tag1->id]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/translations/' . $translation->id, [
            'tags' => [$tag2->id],
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.tags.0.id', $tag2->id);
    }

    public function test_list_translations_with_filters()
    {
        $token = $this->getJwtToken();
        $locale = Locale::factory()->create();
        $tag = Tag::factory()->create();
        $translation = Translation::factory()->create([
            'key' => 'welcome',
            'locale_id' => $locale->id,
        ]);
        $translation->tags()->sync([$tag->id]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/translations?key=welcome&tag=' . $tag->id . '&locale=' . $locale->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['key' => 'welcome']);
    }
}
