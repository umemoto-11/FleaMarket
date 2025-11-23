<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\MOdels\User;
use App\Models\Item;
use App\Models\Comment;
use Database\Seeders\ItemsTableSeeder;

class ItemCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logged_in_user_can_post_a_comment()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $user->forceFill([
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ])->save();
        $item = Item::first();

        $this->assertDatabaseCount('comments', 0);

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => 'テストコメントです',
            'item_id' => $item->id,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメントです',
        ]);

        $commentCount = Comment::where('item_id', $item->id)->count();
        $this->assertEquals(1, $commentCount);
    }

    /** @test */
    public function guest_cannot_post_a_comment()
    {
        $this->seed(ItemsTableSeeder::class);
        $item = Item::first();

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => 'テストコメントです',
            'item_id' => $item->id,
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function comment_id_required()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $user->forceFill([
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ])->save();
        $item = Item::first();

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => '',
            'item_id' => $item->id,
        ]);

        $response->assertSessionHasErrors(['comment']);

        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function comment_cannot_exceed_255_characters()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $user->forceFill([
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ])->save();
        $item = Item::first();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => $longComment,
            'item_id' => $item->id,
        ]);

        $response->assertSessionHasErrors(['comment']);

        $this->assertDatabaseCount('comments', 0);
    }
}
