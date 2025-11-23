<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ItemsTableSeeder;

class UserProfileAndItemsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_profile_and_items_can_be_retrieved()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_id' => null,
        ]);

        $profile = $user->profile()->create([
            'name' => 'テストユーザー',
            'image' => 'test.jpg',
            'postcode' => '123-4567',
            'address' => '東京都新宿区西新宿1-1-1',
            'building' => 'テストビル',
        ]);
        $user->profile_id = $profile->id;
        $user->save();

        $item1 = Item::create([
            'user_id' => $user->id,
            'name' => '出品商品1',
            'price' => 1000,
            'brand' => 'ブランドA',
            'image' => 'item1.jpg',
            'condition' => 1,
            'description' => '説明1',
            'is_sold' => false,
        ]);

        $item2 = Item::create([
            'user_id' => $user->id,
            'name' => '出品商品2',
            'price' => 2000,
            'brand' => 'ブランドB',
            'image' => 'item2.jpg',
            'condition' => 2,
            'description' => '説明2',
            'is_sold' => false,
        ]);

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ]);

        $purchaseItem = Item::create([
            'user_id' => $seller->id,
            'name' => '購入商品1',
            'price' => 3000,
            'brand' => 'ブランドC',
            'image' => 'item3.jpg',
            'condition' => 1,
            'description' => '説明3',
            'is_sold' => true,
            'buyer_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get("/mypage");

        $response->assertStatus(200);

        $response->assertSee($profile->name);
        $response->assertSee($profile->image);

        $response->assertSee($item1->name);
        $response->assertSee($item2->name);

        $response = $this->actingAs($user)->get('/mypage?page=buy');

        $response->assertSee($purchaseItem->name);
    }
}
