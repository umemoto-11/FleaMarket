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
        $user = User::first() ?? User::create(['name' => 'Default User', 'email' => 'user@example.com', 'password' => 'password']);

        $param = [
            'name' => '腕時計',
            'price' => '15000',
            'brand' => 'Rolax',
            'image' => 'items/Armani+Mens+Clock.jpg',
            'condition' => '1',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'HDD',
            'price' => '5000',
            'brand' => '西芝',
            'image' => 'items/HDD+Hard+Disk.jpg',
            'condition' => '2',
            'description' => '高速で信頼性の高いハードディスク',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => '玉ねぎ3束',
            'price' => '300',
            'brand' => 'なし',
            'image' => 'items/iLoveIMG+d.jpg',
            'condition' => '3',
            'description' => '新鮮な玉ねぎ3束のセット',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => '革靴',
            'price' => '4000',
            'brand' => '',
            'image' => 'items/Leather+Shoes+Product+Photo.jpg',
            'condition' => '4',
            'description' => 'クラシックなデザインの革靴',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'ノートPC',
            'price' => '45000',
            'brand' => '',
            'image' => 'items/Living+Room+Laptop.jpg',
            'condition' => '1',
            'description' => '高性能なノートパソコン',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'マイク',
            'price' => '8000',
            'brand' => 'なし',
            'image' => 'items/Music+Mic+4632231.jpg',
            'condition' => '2',
            'description' => '高音質のレコーディング用マイク',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);$param = [
            'name' => 'ショルダーバッグ',
            'price' => '3500',
            'brand' => '',
            'image' => 'items/Purse+fashion+pocket.jpg',
            'condition' => '3',
            'description' => 'おしゃれなショルダーバッグ',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);$param = [
            'name' => 'タンブラー',
            'price' => '500',
            'brand' => 'なし',
            'image' => 'items/Tumbler+souvenir.jpg',
            'condition' => '4',
            'description' => '使いやすいタンブラー',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'コーヒーミル',
            'price' => '4000',
            'brand' => 'Starbacks',
            'image' => 'items/Waitress+with+Coffee+Grinder.jpg',
            'condition' => '1',
            'description' => '手動のコーヒーミル',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);
        $param = [
            'name' => 'メイクセット',
            'price' => '2500',
            'brand' => '',
            'image' => 'items/外出メイクアップセット.jpg',
            'condition' => '2',
            'description' => '便利なメイクアップセット',
            'user_id' => '1',
        ];
        DB::table('items')->insert($param);
    }
}
