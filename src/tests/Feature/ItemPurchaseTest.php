<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ItemsTableSeeder;

class ItemPurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logged_in_user_can_purchase_an_item()
    {
        config(['app.env' => 'testing']);

        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $user->forceFill([
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ])->save();

        $profile = $user->profile()->create([
            'postcode' => '123-4567',
            'address' => '東京都新宿区西新宿1-1-1',
            'building' => 'テストビル',
            'name' => 'テストユーザー',
        ]);
        $user->profile_id = $profile->id;
        $user->save();
        $user->refresh();

        $item = Item::first();

        $response = $this->actingAs($user)
        ->followingRedirects()
        ->post("/purchase/{$item->id}", [
            'item_id' => $item->id,
            'payment_method' => 'credit',
        ], ['HTTP_X_TEST_MODE' => 'true']);

        $response->assertStatus(200);

        $item->refresh();

        $this->assertEquals($user->id, $item->buyer_id);
        $this->assertTrue((bool)$item->is_sold);
    }

    /** @test */
    public function purchased_item_shows_sold_in_list()
    {
        $this->seed(ItemsTableSeeder::class);

        $buyer = User::first();
        $buyer->forceFill([
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ])->save();

        $profile = $buyer->profile()->create([
            'postcode' => '123-4567',
            'address' => '東京都新宿区西新宿1-1-1',
            'building' => 'テストビル',
            'name' => 'テストユーザー',
        ]);
        $buyer->profile_id = $profile->id;
        $buyer->save();
        $buyer->refresh();

        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ]);

        $item = Item::first();
        $item->user_id = $seller->id;
        $item->is_sold = false;
        $item->buyer_id = null;
        $item->save();

        $controller = new \App\Http\Controllers\PaymentController();
        $request = \Illuminate\Http\Request::create("/payment/success", 'GET', [
            'item_id' => $item->id,
        ]);
        $request->setUserResolver(fn() => $buyer);

        $controller->success($request);

        $item->refresh();

        $this->assertTrue((bool)$item->is_sold, 'Item should be marked as sold.');
        $this->assertEquals($buyer->id, $item->buyer_id, 'Buyer ID should match logged-in user.');

        $response = $this->actingAs($buyer)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /** @test */
    public function purchase_item_appears_in_buy_tab_on_profile()
    {
        $this->seed(ItemsTableSeeder::class);

        $buyer = User::first();
        $buyer->forceFill([
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ])->save();

        $profile = $buyer->profile()->create([
            'name' => '購入者',
            'image' => 'buyer.jpg',
            'postcode' => '123-4567',
            'address' => '東京都新宿区西新宿区1-1-1',
            'building' => 'テストビル',
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

        $this->actingAs($buyer)
        ->post("/purchase/{$item->id}", [
            'item_id'        => $item->id,
            'payment_method' => 'credit',
        ], ['HTTP_X_TEST_MODE' => 'true'])
        ->assertRedirect();

        $this->actingAs($buyer)
        ->get(route('payment.success', ['item_id' => $item->id]))
        ->assertRedirect(route('home'));

        $item->refresh();
        $this->assertTrue((bool)$item->is_sold, 'Item should be marked as sold.');
        $this->assertEquals($buyer->id, $item->buyer_id, 'Buyer ID should match logged-in user.');

        $response = $this->actingAs($buyer)->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSee($item->name);
    }
}