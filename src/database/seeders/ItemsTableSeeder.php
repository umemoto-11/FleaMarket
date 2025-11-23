<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\User;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::find(1) ?? User::create([
            'id' => 1,
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::find(2) ?? User::create([
            'id' => 2,
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        $user3 = User::find(3) ?? User::create([
            'id' => 3,
            'name' => 'User Three',
            'email' => 'user3@example.com',
            'password' => bcrypt('password'),
        ]);

        $items = [
            [
                'name' => '腕時計',
                'price' => '15000',
                'brand' => 'Rolax',
                'image' => 'items/Armani+Mens+Clock.jpg',
                'condition' => '1',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
            ],
            [
                'name' => 'HDD',
                'price' => '5000',
                'brand' => '西芝',
                'image' => 'items/HDD+Hard+Disk.jpg',
                'condition' => '2',
                'description' => '高速で信頼性の高いハードディスク',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => '300',
                'brand' => 'なし',
                'image' => 'items/iLoveIMG+d.jpg',
                'condition' => '3',
                'description' => '新鮮な玉ねぎ3束のセット',
            ],
            [
                'name' => '革靴',
                'price' => '4000',
                'brand' => '',
                'image' => 'items/Leather+Shoes+Product+Photo.jpg',
                'condition' => '4',
                'description' => 'クラシックなデザインの革靴',
            ],
            [
                'name' => 'ノートPC',
                'price' => '45000',
                'brand' => '',
                'image' => 'items/Living+Room+Laptop.jpg',
                'condition' => '1',
                'description' => '高性能なノートパソコン',
            ],
            [
                'name' => 'マイク',
                'price' => '8000',
                'brand' => 'なし',
                'image' => 'items/Music+Mic+4632231.jpg',
                'condition' => '2',
                'description' => '高音質のレコーディング用マイク',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => '3500',
                'brand' => '',
                'image' => 'items/Purse+fashion+pocket.jpg',
                'condition' => '3',
                'description' => 'おしゃれなショルダーバッグ',
            ],
            [
                'name' => 'タンブラー',
                'price' => '500',
                'brand' => 'なし',
                'image' => 'items/Tumbler+souvenir.jpg',
                'condition' => '4',
                'description' => '使いやすいタンブラー',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => '4000',
                'brand' => 'Starbacks',
                'image' => 'items/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '1',
                'description' => '手動のコーヒーミル',
            ],
            [
                'name' => 'メイクセット',
                'price' => '2500',
                'brand' => '',
                'image' => 'items/外出メイクアップセット.jpg',
                'condition' => '2',
                'description' => '便利なメイクアップセット',
            ],
        ];

        foreach ($items as $index => $item) {
            DB::table('items')->insert([
                'name'        => $item['name'],
                'price'       => $item['price'],
                'brand'       => $item['brand'],
                'image'       => $item['image'],
                'condition'   => $item['condition'],
                'description' => $item['description'],
                'user_id'     => $index < 5 ? $user1->id : $user2->id,
            ]);
        }
    }
}
