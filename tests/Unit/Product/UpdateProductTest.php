<?php

namespace Tests\Unit\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_it_should_can_update_a_product(): void
    {

        $product = Product::factory()->create();

        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $login_data = $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $data = [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Description Product Test',
            'price' => '10',
        ];

        $response = $this->json('PUT', '/api/v1/products/'.$product->id, $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$login_data->original['data']['token'],
        ]);

        $this->customLog->info('update product', ['response' => $response]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                ],

            ]);
    }

    public function test_it_should_reject_if_user_is_not_authenticated(): void
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Product Test',
            'slug' => 'product-test',
            'description' => 'Description of product test',
            'price' => '12',
        ];

        $response = $this->json('PUT', '/api/v1/products/'.$product->id, $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => '',
        ]);

        $this->customLog->info('update product', ['response' => $response]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_it_should_reject_if_request_is_not_valid(): void
    {
        $product = Product::factory()->create();

        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $login_data = $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $data = [
            'name' => 'Product Test',
            'slug' => $product->slug,
            'description' => 'Description of product test',
            'price' => 'asd',
        ];

        $response = $this->json('PUT', '/api/v1/products/'.$product->id, $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$login_data->original['data']['token'],
        ]);

        $this->customLog->info('update product', ['response' => $response]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors',
            ]);
    }

    public function test_it_should_reject_if_product_is_not_found(): void
    {
        $product = Product::factory()->create();

        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $login_data = $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $data = [
            'name' => 'Product Test',
            'slug' => $product->slug,
            'description' => 'Description of product test',
            'price' => 'asd',
        ];

        $response = $this->json('PUT', '/api/v1/products/'.($product->id + 1), $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$login_data->original['data']['token'],
        ]);

        $this->customLog->info('update product', ['response' => $response]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Product is not found',
            ]);
    }
}
