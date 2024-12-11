<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ExerciseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function store_key_value_pair()
    {
        $response = $this->postJson('/api/setKey', [
            'key' => 'mykey',
            'value' => json_encode(['value' => 'value1']),
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'key' => 'mykey',
                'value' => json_encode(['value' => 'value1']),
            ],
        ]);
    }

    #[Test]
    public function get_latest_key_value()
    {
        $this->postJson('/api/setKey', [
            'key' => 'mykey',
            'value' => json_encode(['value' => 'value1']),
        ]);

        $response = $this->getJson('/api/getLatest/mykey');

        $response->assertStatus(200); // Check if the response status is 200
        $response->assertJson([
            'key' => 'mykey',
            'value' => json_encode(['value' => 'value1']),
        ]);
    }

    #[Test]
    public function get_key_value_by_timestamp()
    {
        $this->postJson('/api/setKey', [
            'key' => 'mykey',
            'value' => json_encode(['value' => 'value1']),
        ]);

        $timestamp = now()->timestamp;

        $response = $this->getJson('/api/getValueAt/mykey/' . $timestamp);

        $response->assertStatus(200);
        $response->assertJson([
            'key' => 'mykey',
            'value' => json_encode(['value' => 'value1']),
        ]);
    }

    #[Test]
    public function get_all_records()
    {
        $this->postJson('/api/setKey', [
            'key' => 'mykey',
            'value' => json_encode(['value' => 'value1']),
        ]);

        $response = $this->getJson('/api/getAll');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'key' => 'mykey',
            'value' => json_encode(['value' => 'value1']),
        ]);
    }
}
