<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $pages = [
            [
                'title_ar' => 'من نحن',
                'title_en' => 'About Us',
                'body_ar' => 'هذه صفحة من نحن باللغة العربية.',
                'body_en' => 'This is the About Us page in English.',
                'slug' => 'about-us',
                'status' => 1,
                'page_type' => 1,
            ],
            [
                'title_ar' => 'سياسة الخصوصية',
                'title_en' => 'Privacy Policy',
                'body_ar' => 'هذه سياسة الخصوصية باللغة العربية.',
                'body_en' => 'This is the privacy policy page in English.',
                'slug' => 'privacy-policy',
                'status' => 1,
                'page_type' => 2,
            ],
            [
                'title_ar' => 'الشروط والأحكام',
                'title_en' => 'Terms and Conditions',
                'body_ar' => 'هذه الشروط والأحكام باللغة العربية.',
                'body_en' => 'These are the terms and conditions in English.',
                'slug' => 'terms-and-conditions',
                'status' => 1,
                'page_type' => 3,
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }

    }
}
