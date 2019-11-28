<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('category_place')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        Category::unguard();

        $data = ['Супермаркеты', 'Развлечения', 'Любимые места'];

        foreach ($data as $name) {
            Category::create(['name' => $name]);
        }
        Category::reguard();
    }
}
