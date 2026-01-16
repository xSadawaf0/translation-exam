<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiResponseHelperTest extends TestCase
{
    public function test_api_response_structure()
    {
        $response = api_response(['foo' => 'bar'], 200, 'Test message', true);
        $json = $response->getData(true);
        $this->assertEquals(200, $json['code']);
        $this->assertEquals('Test message', $json['message']);
        $this->assertTrue($json['success']);
        $this->assertEquals(['foo' => 'bar'], $json['data']);
    }
}
