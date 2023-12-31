<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_it_should_can_register_user()
    {
        $data = [
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => 'tester123',
            'password_confirmation' => 'tester123',
        ];

        $response = $this->json('POST', '/api/register', $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $this->customLog->info('register', ['response' => $response]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token',
                ],
            ])
            ->assertJson([
                'data' => [
                    'user' => [
                        'name' => 'Tester',
                        'email' => 'tester@gmail.com',
                    ],
                ],
            ])
            ->assertDontSee([
                'password',
            ]);
    }

    public function test_it_should_reject_if_request_is_invalid()
    {
        $data = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->json('POST', '/api/register', $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $this->customLog->info('register', ['response' => $response]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors',
            ]);
    }

    public function test_it_should_reject_if_password_is_not_valid_format()
    {
        $data = [
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => 'tester',
            'password_confirmation' => 'tester',
        ];

        $response = $this->json('POST', '/api/register', $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $this->customLog->info('register', ['response' => $response]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'password' => [
                    'The password field must be at least 8 characters.',
                ],
            ])
            ->assertJsonStructure([
                'errors' => [
                    'password',
                ],
            ]);
    }

    public function test_it_should_reject_if_password_confirmation_not_matched()
    {
        $data = [
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => 'tester1234',
            'password_confirmation' => 'tester123',
        ];

        $response = $this->json('POST', '/api/register', $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $this->customLog->info('register', ['response' => $response]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'password' => [
                    'The password field confirmation does not match.',
                ],
            ])
            ->assertJsonStructure([
                'errors' => [
                    'password',
                ],
            ]);
    }

    public function test_it_should_reject_if_email_already_registered()
    {

        User::factory()->create([
            'email' => 'tester@gmail.com',
        ]);

        $data = [
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => 'tester123',
            'password_confirmation' => 'tester123',
        ];

        $response = $this->json('POST', '/api/register', $data, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $this->customLog->info('register', ['response' => $response]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'email',
                ],
            ]);
    }

    public function test_it_should_can_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $this->customLog->info('login', ['response' => $response]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user',
                    'token',
                ],
            ]);
    }

    public function test_it_should_reject_if_login_request_is_invalid()
    {

        $response = $this->json('POST', '/api/login', [
            'email' => '',
            'password' => '',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $this->customLog->info('login', ['response' => $response]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors',
            ]);
    }

    public function test_it_should_reject_if_credentials_is_invalid()
    {

        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => '123123123',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);

        $this->customLog->info('login', ['response' => $response]);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_it_should_can_logout()
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

        $response = $this->json('DELETE', '/api/logout', [], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$login_data->original['data']['token'],
        ]);

        $this->customLog->info('logout', ['response' => $response]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Logged Out',
            ]);
    }

    public function test_it_should_reject_if_not_authorized()
    {
        $response = $this->json('DELETE', '/api/logout', [], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => '',
        ]);

        $this->customLog->info('logout', ['response' => $response]);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.',
            ]);
    }
}
