<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ItemsTableSeeder;

class ItemLikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_like_an_item_and_like_count_increases()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();
        $item = Item::first();

        $this->assertDatabaseCount('likes', 0);

        $response = $this->actingAs($user)->postJson("/item/{$item->id}/like");

        $response->assertStatus(200);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $likesCount = \DB::table('likes')->where('item_id', $item->id)->count();
        $this->assertEquals(1, $likesCount);

        $response->assertJson([
            'status' => 'added',
            'likes_count' => 1,
        ]);
    }

    /** @test */
    public function liked_item_displays_colored_icon()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $item = Item::first();

        $user->likedItems()->attach($item->id);

        $response = $this->actingAs($user)->get("/item/{$item->id}");

        $response->assertStatus(200);

        $response->assertSee('liked');
    }

    /** @test */
    public function user_can_unlike_an_item_and_like_count_decreases()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();
        $item = Item::first();

        $user->likedItems()->attach($item->id);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->postJson("/item/{$item->id}/like");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response->assertJson([
            'status' => 'removed',
            'likes_count' => 0,
        ]);
    }
}
