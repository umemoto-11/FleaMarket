<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_item_with_required_information()
    {
        $this->seed(ItemsTableSeeder::class);
        $this->seed(CategoriesTableSeeder::class);

        $user = User::first();
        $user->forceFill([
            'email_verified_at' => now(),
            'is_profile_setup' => true,
        ])->save();

        $category = Category::first();

        $item = \App\Models\Item::create([
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 5000,
            'condition' => 1,
            'description' => 'テスト商品の説明',
            'image' => 'item.jpg',
            'user_id' => $user->id,
        ]);

        $item->categories()->attach($category->id);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 5000,
        ]);

        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);
    }
}
