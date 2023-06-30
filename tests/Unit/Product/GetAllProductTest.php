<?php

namespace Tests\Unit\Product;

use App\Models\Product;
use Tests\TestCase;

class GetAllProductTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_it_should_can_get_all_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->json('GET', '/api/v1/products', [], [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ]);

        $this->customLog->info("get all products", ["response" => $response]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "message",
                "data"
            ])
            ->assertJsonIsArray("data");
    }
}
