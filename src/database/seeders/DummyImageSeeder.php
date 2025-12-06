<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DummyImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $images = [
            'Armani+Mens+Clock.jpg',
            'HDD+Hard+Disk.jpg',
            'iLoveIMG+d.jpg',
            'Leather+Shoes+Product+Photo.jpg',
            'Living+Room+Laptop.jpg',
            'Music+Mic+4632231.jpg',
            'Purse+fashion+pocket.jpg',
            'Tumbler+souvenir.jpg',
            'Waitress+with+Coffee+Grinder.jpg',
            'makeup_set.jpg',
        ];

        foreach ($images as $image) {
            Storage::disk('public')->put(
                'items/' . $image,
                file_get_contents(database_path('seeders/images/' . $image))
            );
        }
    }
}
