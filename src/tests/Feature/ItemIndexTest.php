<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\ItemsTableSeeder;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_all_items_from_the_database()
    {
        $this->seed(ItemsTableSeeder::class);

        $itemCount = Item::count();

        $response = $this->get('/');

        $response->assertStatus(200);

        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }

        $this->assertEquals($itemCount, $items->count());
    }

    /** @test */
    public function it_displays_all_items_and_sold_label()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Default User',
                'email' => 'user@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $soldItem = Item::create([
            'name' => '購入済み商品',
            'price' => 1000,
            'brand' => 'TestBrand',
            'image' => 'item/test.jpg',
            'condition' => 1,
            'description' => '購入済み商品です',
            'user_id' => $user->id,
            'is_sold' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        $response->assertSee($soldItem->name);
        $response->assertSee('Sold');
    }

    /** @test */
    public function it_does_not_display_items_created_by_the_authenticated_user()
    {
        $owner = User::create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);

        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);

        Item::create([
            'name' => 'Owner Item',
            'price' => 1000,
            'brand' => 'Brand A',
            'image' => 'items/item1.jpg',
            'condition' => 1,
            'description' => 'Owner item description',
            'user_id' => $owner->id,
        ]);

        Item::create([
            'name' => 'Other Item',
            'price' => 2000,
            'brand' => 'Brand B',
            'image' => 'items/item2.jpg',
            'condition' => 2,
            'description' => 'Other item description',
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($owner);

        $response = $this->get('/');

        $response->assertDontSee('Owner Item');

        $response->assertSee('Other Item');

        $response->assertStatus(200);
    }
}
