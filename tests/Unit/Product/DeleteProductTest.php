<?php

namespace Tests\Unit\Product;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DeleteProductTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_it_should_can_delete_a_product(): void
    {
        $product = Product::factory()->create();

        $user = User::factory()->create([
            "password" => Hash::make('password')
        ]);

        $login_data = $this->json('POST', '/api/login', [
            "email" => $user->email,
            "password" => "password"
        ], [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ]);

        $response = $this->json('DELETE', '/api/v1/products/' . $product->id, [], [
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . $login_data->original["data"]["token"]
        ]);

        $this->customLog->info("delete product", ["response" => $response]);

        $response->assertStatus(200)
            ->assertJson([
                "message" => "Product deleted successfully",
                "data" => null
            ]);
    }

    public function test_it_should_reject_if_product_is_not_found(): void
    {
        $product = Product::factory()->create();

        $user = User::factory()->create([
            "password" => Hash::make('password')
        ]);

        $login_data = $this->json('POST', '/api/login', [
            "email" => $user->email,
            "password" => "password"
        ], [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ]);

        $response = $this->json('DELETE', '/api/v1/products/' . ($product->id + 1), [], [
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . $login_data->original["data"]["token"]
        ]);

        $this->customLog->info("delete product", ["response" => $response]);

        $response->assertStatus(404)
            ->assertJson([
                "message" => "Product is not found",
            ]);
    }

    public function test_it_should_reject_if_user_is_not_authenticated(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('DELETE', '/api/v1/products/' . $product->id, [], [
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "Authorization" => ""
        ]);

        $this->customLog->info("delete product", ["response" => $response]);

        $response->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated.",
            ]);
    }
}
