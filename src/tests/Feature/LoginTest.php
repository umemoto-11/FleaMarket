<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_email_field()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);

        $errors = session('errors');
        $this->assertEquals('メールアドレスを入力してください', $errors->first('email'));
    }

    /** @test */
    public function it_requires_password_field()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);

        $errors = session('errors');
        $this->assertEquals('パスワードを入力してください', $errors->first('password'));
    }

    /** @test */
    public function login_fails_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct_password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'password' => 'ログイン情報が登録されていません',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function login_succeeds_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct_password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'correct_password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
    }
}
