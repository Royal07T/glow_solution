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
            ['name' => 'Accounting & Finance', 'type' => 'professional'],
            ['name' => 'Administration', 'type' => 'professional'],
            ['name' => 'Agriculture & Agro-Allied', 'type' => 'professional'],
            ['name' => 'Architecture & Building Design', 'type' => 'professional'],
            ['name' => 'Automobile & Auto Mechanics', 'type' => 'professional'],
            ['name' => 'Banking & Financial Services', 'type' => 'professional'],
            ['name' => 'Building & Construction', 'type' => 'professional'],
            ['name' => 'Business Development', 'type' => 'professional'],
            ['name' => 'Carpentry & Furniture', 'type' => 'domestic'],
            ['name' => 'Childcare & Nanny', 'type' => 'domestic'],
            ['name' => 'Cleaning Services', 'type' => 'domestic'],
            ['name' => 'Consulting & Strategy', 'type' => 'professional'],
            ['name' => 'Content Writing & Copywriting', 'type' => 'professional'],
            ['name' => 'Cooking & Catering', 'type' => 'domestic'],
            ['name' => 'Customer Service & Call Centre', 'type' => 'professional'],
            ['name' => 'Cybersecurity', 'type' => 'professional'],
            ['name' => 'Data & Analytics', 'type' => 'professional'],
            ['name' => 'Data Entry & Back Office', 'type' => 'professional'],
            ['name' => 'Digital Marketing & Social Media', 'type' => 'professional'],
            ['name' => 'Drivers', 'type' => 'both'],
            ['name' => 'E-commerce & Retail', 'type' => 'professional'],
            ['name' => 'Education', 'type' => 'professional'],
            ['name' => 'Elderly Care', 'type' => 'domestic'],
            ['name' => 'Electrical & Solar Installation', 'type' => 'domestic'],
            ['name' => 'Engineering', 'type' => 'professional'],
            ['name' => 'Errand & Personal Assistant', 'type' => 'domestic'],
            ['name' => 'Event Staff & Ushering', 'type' => 'both'],
            ['name' => 'Facilities Management', 'type' => 'professional'],
            ['name' => 'Food & Beverage', 'type' => 'professional'],
            ['name' => 'Freelance & Remote Work', 'type' => 'professional'],
            ['name' => 'Gardening & Landscaping', 'type' => 'domestic'],
            ['name' => 'Generator & AC Technician', 'type' => 'both'],
            ['name' => 'Government & Public Sector', 'type' => 'professional'],
            ['name' => 'Graphic Design & Creative Arts', 'type' => 'professional'],
            ['name' => 'Hairdressing & Beauty', 'type' => 'domestic'],
            ['name' => 'Health, Safety & Environment', 'type' => 'professional'],
            ['name' => 'Healthcare', 'type' => 'professional'],
            ['name' => 'Home Repairs & Maintenance', 'type' => 'domestic'],
            ['name' => 'Hospitality & Tourism', 'type' => 'professional'],
            ['name' => 'Housekeeping', 'type' => 'domestic'],
            ['name' => 'Human Resources & Recruitment', 'type' => 'professional'],
            ['name' => 'Insurance', 'type' => 'professional'],
            ['name' => 'Internship & Graduate Trainee', 'type' => 'professional'],
            ['name' => 'IT & Technology', 'type' => 'professional'],
            ['name' => 'Journalism & Communications', 'type' => 'professional'],
            ['name' => 'Laundry & Dry Cleaning', 'type' => 'domestic'],
            ['name' => 'Legal & Compliance', 'type' => 'professional'],
            ['name' => 'Logistics & Supply Chain', 'type' => 'professional'],
            ['name' => 'Manufacturing & Production', 'type' => 'professional'],
            ['name' => 'Marine & Offshore', 'type' => 'professional'],
            ['name' => 'Marketing & Advertising', 'type' => 'professional'],
            ['name' => 'Media, Art & Entertainment', 'type' => 'professional'],
            ['name' => 'Mining & Natural Resources', 'type' => 'professional'],
            ['name' => 'NGO & Social Services', 'type' => 'professional'],
            ['name' => 'Oil, Gas & Energy', 'type' => 'professional'],
            ['name' => 'Painting & Decorating', 'type' => 'domestic'],
            ['name' => 'Pharmaceutical', 'type' => 'professional'],
            ['name' => 'Photography & Videography', 'type' => 'professional'],
            ['name' => 'Plumbing', 'type' => 'domestic'],
            ['name' => 'Printing & Publishing', 'type' => 'professional'],
            ['name' => 'Procurement & Purchasing', 'type' => 'professional'],
            ['name' => 'Project Management', 'type' => 'professional'],
            ['name' => 'Public Relations', 'type' => 'professional'],
            ['name' => 'Quality Assurance & Control', 'type' => 'professional'],
            ['name' => 'Real Estate & Property', 'type' => 'professional'],
            ['name' => 'Religious & Community Services', 'type' => 'professional'],
            ['name' => 'Research & Development', 'type' => 'professional'],
            ['name' => 'Sales & Marketing', 'type' => 'professional'],
            ['name' => 'Sales Representatives', 'type' => 'professional'],
            ['name' => 'Security Services', 'type' => 'both'],
            ['name' => 'Software & Web Development', 'type' => 'professional'],
            ['name' => 'Sports & Fitness', 'type' => 'professional'],
            ['name' => 'Supermarket & Store Operations', 'type' => 'professional'],
            ['name' => 'Tailoring & Fashion Design', 'type' => 'domestic'],
            ['name' => 'Telecommunications', 'type' => 'professional'],
            ['name' => 'Transportation & Fleet Management', 'type' => 'professional'],
            ['name' => 'Travel & Aviation', 'type' => 'professional'],
            ['name' => 'UI/UX Design', 'type' => 'professional'],
            ['name' => 'Veterinary Services', 'type' => 'professional'],
            ['name' => 'Virtual Assistance', 'type' => 'professional'],
            ['name' => 'Warehouse & Inventory', 'type' => 'professional'],
            ['name' => 'Welding & Fabrication', 'type' => 'domestic'],
        ];

        foreach ($categories as $category) {
            JobCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'is_active' => true,
                ]
            );
        }
    }
}
