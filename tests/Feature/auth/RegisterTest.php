<?php

namespace Tests\Feature\auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\CustomTest;

class RegisterTest extends CustomTest
{
    use DatabaseMigrations;

    /** @test */
    public function register_user()
    {
        $this->FakeData();

        $data = [
            'name' => 'test',
            'email' => 'test2@test.com',
            'password' => 'password'
        ];
        $res = $this->post(route('register'), $data)
            ->assertCreated();

        $this->assertArrayHasKey('status', $res);
    }

    /** @test */
    public function handle_length_name_when_register_user()
    {
        $this->FakeData();

        $data = [
            'email' => 'test@test.com',
            'password' => 'password'
        ];

        $this->post(route('register'), $data)
            ->assertStatus(422);
    }

    /** @test */
    public function unique_email_when_register_user()
    {
        $this->FakeData();

        $data = [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password'
        ];

        $data2 = [
            'name' => 'test2',
            'email' => 'test@test.com',
            'phone' => "09121234567",
            'password' => 'password'
        ];
        $this->post(route('register'), $data);

        $this->post(route('register'), $data2)
            ->assertStatus(422);
    }

    /** @test */
    public function handle_length_password_when_register_user()
    {
        $this->FakeData();

        $data = [
            'name' => 'test',
            'email' => 'test@test.com',
            'phone' => "09121234567",
            'password' => 'pass'
        ];

        $this->post(route('register'), $data)
            ->assertStatus(422);
    }
}
