<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            User::updateOrCreate(['email'=>env('ADMIN_EMAIL')],['name'=>'Restaurant Admin','password'=>Hash::make(env('ADMIN_PASSWORD')),'is_admin'=>true]);
        }
        $starters=Category::firstOrCreate(['name'=>'Starters'],['sort_order'=>1]);
        $mains=Category::firstOrCreate(['name'=>'Main Course'],['sort_order'=>2]);
        $drinks=Category::firstOrCreate(['name'=>'Drinks'],['sort_order'=>3]);
        Product::firstOrCreate(['name'=>'Crispy Corn'],['category_id'=>$starters->id,'description'=>'Golden corn with a gentle spice.','price'=>180,'sort_order'=>1]);
        Product::firstOrCreate(['name'=>'Paneer Tikka'],['category_id'=>$starters->id,'description'=>'Smoky paneer with peppers.','price'=>240,'sort_order'=>2]);
        Product::firstOrCreate(['name'=>'Butter Paneer'],['category_id'=>$mains->id,'description'=>'Paneer in a creamy tomato sauce.','price'=>320,'sort_order'=>1]);
        Product::firstOrCreate(['name'=>'Fresh Lime Soda'],['category_id'=>$drinks->id,'description'=>'Cool and refreshing.','price'=>90,'sort_order'=>1]);
    }
}
