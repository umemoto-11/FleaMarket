<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\MOdels\User;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_all_necessary_item_details()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $item = Item::latest()->first();

        \DB::table('likes')->insert([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        \DB::table('comments')->insert([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメントです',
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        $response->assertSee($item->name);
        $response->assertSee($item->brand);
        $response->assertSee('¥' . number_format($item->price) . '(税込)');
        $response->assertSee($item->description);

        $likedCount = \DB::table('likes')->where('item_id', $item->id)->count();
        $commentCount = \DB::table('comments')->where('item_id', $item->id)->count();

        $response->assertSee((string) $likedCount);
        $response->assertSee((string) $commentCount);

        $response->assertSee($item->condition->value);
        if ($item->category) {
            $response->assertSee($item->category->name);
        }

        $response->assertSee($item->image);
    }

    /** @test */
    public function it_displays_selected_categories_for_item()
    {
        $this->seed(ItemsTableSeeder::class);
        $this->seed(CategoriesTableSeeder::class);

        $item = Item::first();
        $categories = \DB::table('categories')->take(2)->get();

        foreach ($categories as $category) {
            \DB::table('category_item')->insert([
                'item_id' => $item->id,
                'category_id' => $category->id,
            ]);
        }

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}