<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ItemsTableSeeder;

class PaymentMenthodSelectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function selected_payment_method_is_saved_correctly()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first(); $user->forceFill([
            'email_verified_at' => now(), 'is_profile_setup' => true,
        ])->save();
        $item = Item::first();

        $user->profile()->create([
            'postcode' => '123-4567', 'address' => '東京都新宿区西新宿1-1-1', 'building' => 'テストビル', 'name' => 'テストユーザー',
        ]);

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);

        $response->assertSee('name="payment_method"', false);

        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'item_id' => $item->id, 'payment_method' => 'credit',
        ], ['HTTP_X_TEST_MODE' => 'true']);

        $response->assertStatus(302);

        $response->assertRedirect("/purchase/{$item->id}");
    }
}
