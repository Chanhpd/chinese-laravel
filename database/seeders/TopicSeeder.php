<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $topics = [
            [
                'name' => 'Basic Greetings',
                'name_zh' => '问候语',
                'description' => 'Basic greetings and self-introductions in Chinese',
                'image_url' => 'https://example.com/images/greetings.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Family',
                'name_zh' => '家庭',
                'description' => 'Vocabulary about family members and relationships',
                'image_url' => 'https://example.com/images/family.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Numbers',
                'name_zh' => '数字',
                'description' => 'Numbers from 0 to 100 and their usage',
                'image_url' => 'https://example.com/images/numbers.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Colors',
                'name_zh' => '颜色',
                'description' => 'Vocabulary about colors and descriptions',
                'image_url' => 'https://example.com/images/colors.jpg',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Food & Drinks',
                'name_zh' => '食物和饮料',
                'description' => 'Vocabulary about food, drinks and restaurants',
                'image_url' => 'https://example.com/images/food.jpg',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Shopping',
                'name_zh' => '购物',
                'description' => 'Vocabulary related to shopping and prices',
                'image_url' => 'https://example.com/images/shopping.jpg',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Travel',
                'name_zh' => '旅游',
                'description' => 'Useful vocabulary for travel and transportation',
                'image_url' => 'https://example.com/images/travel.jpg',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Time',
                'name_zh' => '时间',
                'description' => 'Vocabulary about time, dates, and hours',
                'image_url' => 'https://example.com/images/time.jpg',
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($topics as $topic) {
            Topic::create($topic);
        }
    }
}
