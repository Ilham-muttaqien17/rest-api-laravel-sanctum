<?php

namespace Tests\Unit\Product;

use App\Models\Product;
use Tests\TestCase;

class GetSingleProductTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_it_should_can_get_a_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('GET', '/api/v1/products/' . $product->id, [], [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ]);

        $this->customLog->info('get single product', ["response" => $response]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "message",
                "data"
            ]);
    }

    public function test_it_should_reject_if_product_is_not_found(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('GET', '/api/v1/products/' . ($product->id + 1), [], [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ]);

        $this->customLog->info('get single product', ["response" => $response]);

        $response->assertStatus(404)
            ->assertJson([
                "message" => "Product is not found"
            ]);
    }
}
