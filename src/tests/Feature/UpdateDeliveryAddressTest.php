<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use Database\Seeders\ItemsTableSeeder;

class UpdateDeliveryAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function updated_address_is_reflected_on_purchase_page()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $user->forceFill([
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ])->save();

        $profile = $user->profile()->create([
            'postcode' => '000-0000',
            'address'  => 'ダミー住所',
            'building' => 'ダミービル',
            'name'     => 'テストユーザー',
        ]);

        $user->profile_id = $profile->id;
        $user->save();

        $item = Item::first();

        $response = $this->actingAs($user)->patch("/purchase/address/{$item->id}", [
            'postcode' => '123-4567',
            'address' => '東京都新宿区西新宿1-1-1',
            'building' => 'テストビル',
        ]);

        $response->assertRedirect("/purchase/{$item->id}");

        $user->refresh();
        $profile->refresh();

        $this->assertDatabaseHas('profiles', [
            'id'  => $profile->id,
            'postcode' => '123-4567',
            'address'  => '東京都新宿区西新宿1-1-1',
            'building' => 'テストビル',
        ]);

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");

        $response->assertStatus(200);

        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区西新宿1-1-1');
        $response->assertSee('テストビル');
    }

    /** @test */
    public function purchased_item_has_buyer_and_delivery_address()
    {
        $this->seed(ItemsTableSeeder::class);

        $buyer = User::first();
        $buyer->forceFill([
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ])->save();

        $profile = $buyer->profile()->create([
            'postcode' => '123-4567',
            'address'  => '東京都新宿区西新宿1-1-1',
            'building' => 'テストビル',
            'name'     => 'テストユーザー',
        ]);
        $buyer->profile_id = $profile->id;
        $buyer->save();

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ]);

        $item = Item::first();
        $item->update([
            'user_id' => $seller->id,
            'is_sold' => false,
            'buyer_id' => null,
        ]);

        $response = $this->actingAs($buyer)->post("/purchase/{$item->id}", [
            'item_id' => $item->id,
            'payment_method' => 'credit',
        ], ['HTTP_X_TEST_MODE' => 'true']);

        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('/payment/success', $redirectUrl);

        $this->actingAs($buyer)->get($redirectUrl);

        $item->refresh();

        $this->assertTrue((bool)$item->is_sold, 'Item should be marked as sold.');
        $this->assertEquals($buyer->id, $item->buyer_id, 'Buyer ID should match logged-in user.');
        $this->assertEquals('123-4567', $buyer->profile->postcode);
        $this->assertEquals('東京都新宿区西新宿1-1-1', $buyer->profile->address);
        $this->assertEquals('テストビル', $buyer->profile->building);
    }
}