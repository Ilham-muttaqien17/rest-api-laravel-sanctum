<?php

namespace Tests\Unit\Product;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StoreProductTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_it_should_can_store_new_product(): void
    {
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
            'slug' => 'product-test',
            'description' => 'Description of product test',
            'price' => '12',
        ];

        $response = $this->json('POST', '/api/v1/products', $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$login_data->original['data']['token'],
        ]);

        $this->customLog->info('create product', ['response' => $response]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data',
            ])
            ->assertJson([
                'data' => [
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                ],
            ]);
    }

    public function test_it_should_reject_if_request_is_not_valid(): void
    {
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
            'name' => '',
            'slug' => '',
            'description' => '',
            'price' => '',
        ];

        $response = $this->json('POST', '/api/v1/products', $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$login_data->original['data']['token'],
        ]);

        $this->customLog->info('create product', ['response' => $response]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors',
            ]);
    }

    public function test_it_should_reject_if_user_is_not_authenticated(): void
    {
        $data = [
            'name' => 'Product Test',
            'slug' => 'product-test',
            'description' => 'Description of product test',
            'price' => '12',
        ];

        $response = $this->json('POST', '/api/v1/products', $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => '',
        ]);

        $this->customLog->info('create product', ['response' => $response]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
