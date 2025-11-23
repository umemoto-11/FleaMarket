<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;

class UserProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_edit_page_displays_existing_user_information()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->forceFill([
            'is_profile_setup' => true,
            'email_verified_at' => now(),
        ])->save();

        $profile = Profile::create([
            'user_id'  => $user->id,
            'name'     => 'テストユーザー',
            'image'    => 'test.jpg',
            'postcode' => '123-4567',
            'address'  => '東京都新宿区西新宿1-1-1',
            'building' => 'テストビル',
        ]);

        $user->profile_id = $profile->id;
        $user->save();

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);

        $response->assertSee('test.jpg');
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区西新宿1-1-1');
        $response->assertSee('テストビル');
    }
}