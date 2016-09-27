<?php

use App\Keyword;
use Illuminate\Database\Seeder;

class KeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keywords = [
            [
                'name' => 'aerial view',
                'weight' => 95,
                'image' => '57e3d1558f279.jpg'
            ],
            [
                'name' => 'gopro',
                'weight' => 90,
                'image' => '57e3d22fef007.jpg'
            ],
            [
                'name' => 'things to do',
                'weight' => 70,
                'image' => '57e3d247a3a18.jpg'
            ],
            [
                'name' => 'food',
                'weight' => 65,
                'image' => '57e3d258c065b.jpg'
            ],
            [
                'name' => 'tours',
                'weight' => 62,
                'image' => '57e3d2684314e.jpg'
            ],
            [
                'name' => 'market',
                'weight' => 60,
                'image' => '57e3d2763b4e3.jpg'
            ],
            [
                'name' => 'night life',
                'weight' => 55,
                'image' => '57e3d2864923c.jpg'
            ],
            [
                'name' => 'festivals',
                'weight' => 50,
                'image' => '57e3d295490aa.jpg'
            ],
            [
                'name' => 'nature',
                'weight' => 50,
                'image' => '57e3d821de458.jpg'
            ],

        ];
        foreach ($keywords as $keyword) {
            Keyword::create([
                'name' => $keyword['name'],
                'weight' => $keyword['weight'],
                'image' => $keyword['image']
            ]);
        }
    }
}
