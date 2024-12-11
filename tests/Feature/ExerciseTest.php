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
    public function store_key_value_pair_with_missing_key()
    {
        $response = $this->postJson('/api/setKey', [
            'value' => json_encode(['value' => 'value1']),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('key');
    }

    #[Test]
    public function store_key_value_pair_with_missing_value()
    {
        $response = $this->postJson('/api/setKey', [
            'key' => 'mykey',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('value');
    }

    #[Test]
    public function store_key_value_pair_with_invalid_json_value()
    {
        $response = $this->postJson('/api/setKey', [
            'key' => 'mykey',
            'value' => 'invalid-json', // Invalid JSON format
        ]);

        $response->assertStatus(422); // Expecting validation error
        $response->assertJsonValidationErrors('value');
    }


    #[Test]
    public function get_latest_key_value()
    {
        $this->postJson('/api/setKey', [
            'key' => 'mykey',
            'value' => json_encode(['value' => 'value1']),
        ]);

        $response = $this->getJson('/api/getLatest/mykey');

        $response->assertStatus(200);
        $response->assertJson([
            'key' => 'mykey',
            'value' => json_encode(['value' => 'value1']),
        ]);
    }

    #[Test]
    public function get_latest_key_value_with_non_existent_key()
    {
        $response = $this->getJson('/api/getLatest/nonexistentkey');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Key not found.'
        ]);
    }

    #[Test]
    public function get_latest_key_value_when_database_is_empty()
    {
        $response = $this->getJson('/api/getLatest/mykey');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Key not found.'
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
    public function get_key_value_by_timestamp_with_invalid_timestamp()
    {
        $response = $this->getJson('/api/getValueAt/mykey/invalidtimestamp');

        $response->assertStatus(400);
    }

    #[Test]
    public function get_key_value_by_timestamp_with_non_existent_key_at_timestamp()
    {
        $timestamp = now()->timestamp;
        $response = $this->getJson('/api/getValueAt/nonexistentkey/' . $timestamp);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No value found for the specified key and timestamp.'
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

    #[Test]
    public function get_all_records_when_database_is_empty()
    {
        $response = $this->getJson('/api/getAll');

        $response->assertStatus(200); // Should return empty array
        $response->assertJson([]);
    }

    #[Test]
    public function get_all_records_with_multiple_entries()
    {
        $this->postJson('/api/setKey', [
            'key' => 'key1',
            'value' => json_encode(['value' => 'value1']),
        ]);
        $this->postJson('/api/setKey', [
            'key' => 'key2',
            'value' => json_encode(['value' => 'value2']),
        ]);

        $response = $this->getJson('/api/getAll');

        $response->assertStatus(200);
        $response->assertJsonFragment(['key' => 'key1']);
        $response->assertJsonFragment(['key' => 'key2']);
    }

    #[Test]
    public function get_all_records_with_large_data()
    {
        for ($i = 0; $i < 1000; $i++) {
            $this->postJson('/api/setKey', [
                'key' => 'key' . $i,
                'value' => json_encode(['value' => 'value' . $i]),
            ]);
        }

        $response = $this->getJson('/api/getAll');

        $response->assertStatus(200);
        $jsonData = $response->json('data');
        $this->assertCount(1000, $jsonData);
    }

    #[Test]
    public function store_key_value_pair_concurrently()
    {
        $responses = [];
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->postJson('/api/setKey', [
                'key' => 'key' . $i,
                'value' => json_encode(['value' => 'value' . $i]),
            ]);
        }

        foreach ($responses as $response) {
            $response->assertStatus(201);
        }
    }

    #[Test]
    public function get_latest_key_value_with_invalid_method()
    {
        $response = $this->postJson('/api/getLatest/mykey');

        $response->assertStatus(405);
    }

    #[Test]
    public function get_all_records_with_invalid_method()
    {
        $response = $this->postJson('/api/getAll');

        $response->assertStatus(405);
    }
}
