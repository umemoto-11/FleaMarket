<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ItemsTableSeeder;
use Illuminate\Support\Facades\DB;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_display_only_liked_items_in_mylist()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $items = Item::all();

        DB::table('likes')->insert([
            ['user_id' => $user->id, 'item_id' => $items[0]->id],
            ['user_id' => $user->id, 'item_id' => $items[2]->id],
        ]);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        $response->assertSee($items[0]->name);
        $response->assertSee($items[2]->name);

        $response->assertDontSee($items[1]->name);
    }

    /** @test */
    public function it_displays_sold_label_for_liked_sold_items_in_mylist()
    {
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $items = Item::all();

        DB::table('likes')->insert([
            ['user_id' => $user->id, 'item_id' => $items[0]->id],
            ['user_id' => $user->id, 'item_id' => $items[1]->id],
        ]);

        $items[0]->update(['is_sold' => false]);
        $items[1]->update(['is_sold' => true]);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertStringNotContainsString('Sold', $this->extractItemBlock($html, $items[0]->name));

        $this->assertStringContainsString('Sold', $this->extractItemBlock($html, $items[1]->name));
    }

    private function extractItemBlock(string $html, string $itemName): string
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query("//a[contains(@class, 'item-link')]");

        foreach ($nodes as $node) {
            if (strpos($node->textContent, $itemName) !== false) {
                return $node->textContent;
            }
        }

        return '';
    }

    /** @test */
    public function mylist_is_empty_for_guest()
    {
        $this->seed(ItemsTableSeeder::class);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        $items = Item::all();
        foreach ($items as $item) {
            $response->assertDontSee($item->name);
        }

        $response->assertDontSee('Sold');
    }
}
