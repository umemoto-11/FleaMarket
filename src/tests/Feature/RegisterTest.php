<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_name_field()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['name']);

        $errors = session('errors');
        $this->assertEquals('お名前を入力してください', $errors->first('name'));
    }

    /** @test */
    public function it_requires_email_field()
    {
        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);

        $errors = session('errors');
        $this->assertEquals('メールアドレスを入力してください', $errors->first('email'));
    }

    /** @test */
    public function it_requires_password_field()
    {
        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['password']);

        $errors = session('errors');
        $this->assertEquals('パスワードを入力してください', $errors->first('password'));
    }

    /** @test */
    public function it_requires_password_to_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors(['password']);

        $errors = session('errors');
        $this->assertEquals('パスワードは8文字以上で入力してください', $errors->first('password'));
    }

    /** @test */
    public function password_and_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['password_confirmation']);

        $errors = session('errors');
        $this->assertEquals('パスワードと一致しません', $errors->first('password_confirmation'));
    }

    /** @test */
    public function user_can_register_and_redirect_to_profile()
    {
        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect('/first-login');
    }
}
