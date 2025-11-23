<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Database\Seeders\ItemsTableSeeder;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_search_items_by_partial_name()
    {
        $user = User::first() ?? User::create([
            'name' => '山田太郎',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $item1 = Item::create([
            'name' => '赤い腕時計',
            'price' => 12000,
            'brand' => 'TestBrand',
            'image' => 'items/red_watch.jpg',
            'condition' => 1,
            'description' => '赤いおしゃれな腕時計',
            'user_id' => $user->id,
        ]);

        $item2 = Item::create([
            'name' => '青いバッグ',
            'price' => 8000,
            'brand' => 'TestBrand',
            'image' => 'items/blue_bag.jpg',
            'condition' => 1,
            'description' => '青いシンプルなバッグ',
            'user_id' => $user->id,
        ]);

        $response = $this->get('/search?keyword=腕時計');

        $response->assertStatus(200);

        $response->assertSee($item1->name);

        $response->assertDontSee($item2->name);
    }

    /** @test */
    public function mylist_keeps_search_keyword_and_displays_correct_items()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $items = Item::all();

        DB::table('likes')->insert([
            ['user_id' => $user->id, 'item_id' => $items[0]->id],
            ['user_id' => $user->id, 'item_id' => $items[2]->id],
        ]);

        $this->actingAs($user);

        $keyword = '腕時計';
        $response = $this->withSession(['keyword' => $keyword])->get('/?tab=mylist');

        $response->assertStatus(200);

        $itemsInMyList = Item::whereIn('id', [$items[0]->id, $items[2]->id])
                    ->where('name', 'like', "%{$keyword}%")
                    ->get();

        foreach ($itemsInMyList as $item) {
            $this->assertStringContainsString($item->name, $response->getContent());
        }

        $notShownItems = Item::whereNotIn('id', $itemsInMyList->pluck('id'))->get();

        foreach ($notShownItems as $item) {
            $this->assertStringNotContainsString($item->name, $response->getContent());
        }
    }
}
