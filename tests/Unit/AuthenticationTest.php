<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class AuthenticationTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_it_should_can_register_user()
    {
        $data = [
            "name" => "Tester",
            "email" => "tester@gmail.com",
            "password" => "tester123",
            "password_confirmation" => "tester123"
        ];

        $this->json('POST', '/api/register', $data, [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                "data" => [
                    "user",
                    "token"
                ]
            ])
            ->assertJson([
                'data' => [
                    "user" => [
                        "name" => "Tester",
                        "email" => "tester@gmail.com",
                    ]
                ]
            ])
            ->assertDontSee([
                "password"
            ]);
    }

    public function test_it_should_reject_if_request_is_invalid()
    {
        $data = [
            "name" => "",
            "email" => "",
            "password" => "",
            "password_confirmation" => ""
        ];

        $this->json('POST', '/api/register', $data, [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                "errors"
            ]);
    }

    public function test_it_should_reject_if_password_is_not_valid_format()
    {
        $data = [
            "name" => "Tester",
            "email" => "tester@gmail.com",
            "password" => "tester",
            "password_confirmation" => "tester"
        ];

        $this->json('POST', '/api/register', $data, [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ])
            ->assertStatus(422)
            ->assertJsonFragment([
                "password" => [
                    'The password field must be at least 8 characters.'
                ]
            ])
            ->assertJsonStructure([
                "errors" => [
                    "password"
                ]
            ]);
    }

    public function test_it_should_reject_if_password_confirmation_not_matched()
    {
        $data = [
            "name" => "Tester",
            "email" => "tester@gmail.com",
            "password" => "tester1234",
            "password_confirmation" => "tester123"
        ];

        $this->json('POST', '/api/register', $data, [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ])
            ->assertStatus(422)
            ->assertJsonFragment([
                "password" => [
                    'The password field confirmation does not match.'
                ]
            ])
            ->assertJsonStructure([
                "errors" => [
                    "password"
                ]
            ]);
    }

    public function test_it_should_reject_if_email_already_registered()
    {

        User::factory()->create([
            "email" => "tester@gmail.com"
        ]);

        $data = [
            "name" => "Tester",
            "email" => "tester@gmail.com",
            "password" => "tester123",
            "password_confirmation" => "tester123"
        ];

        $this->json('POST', '/api/register', $data, [
            "Accept" => "application/json",
            "Content-Type" => "application/json"
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                "errors" => [
                    "email"
                ]
            ]);
    }
}
