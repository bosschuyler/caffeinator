<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $beverages = [
            [
                'name' => 'Monster Ultra Sunrise',
                'description' => 'A refreshing orange beverage that has 75mg of caffeine per serving. Every can has two servings.',
                'caffeine' => 75,
                'servings' => 2,
                'measure' => 'can',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Black Coffee',
                'description' => 'The classic, the average 8oz. serving of black coffee has 95mg of caffeine',
                'caffeine' => 95,
                'servings' => 1,
                'measure' => 'cup',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Americano',
                'description' => 'Sometimes you need to water it down a bit... and in comes the americano with an average of 77mg of caffeine per serving.',
                'caffeine' => 77,
                'servings' => 1,
                'measure' => 'cup',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Sugar Free NOS',
                'description' => 'Another orange delight without the sugar. It has 130mg of caffeine per serving and each can has two servings.',
                'caffeine' => 130,
                'servings' => 2,
                'measure' => 'can',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => '5 Hour Energy',
                'description' => 'An amazing shot of get up and go! Each 2fl. oz container has 200mg of caffeine to get you going.',
                'caffeine' => 200,
                'servings' => 1,
                'measure' => 'shot',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        DB::table('beverage')->insert($beverages);

    }
}
