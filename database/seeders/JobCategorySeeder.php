<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Housekeeping', 'type' => 'domestic'],
            ['name' => 'Childcare & Nanny', 'type' => 'domestic'],
            ['name' => 'Elderly Care', 'type' => 'domestic'],
            ['name' => 'Cooking & Catering', 'type' => 'domestic'],
            ['name' => 'Administration', 'type' => 'professional'],
            ['name' => 'Sales & Marketing', 'type' => 'professional'],
            ['name' => 'IT & Technology', 'type' => 'professional'],
            ['name' => 'Healthcare', 'type' => 'professional'],
            ['name' => 'Education', 'type' => 'professional'],
            ['name' => 'Engineering', 'type' => 'professional'],
        ];

        foreach ($categories as $category) {
            JobCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'type' => $category['type'],
                'is_active' => true,
            ]);
        }
    }
}
