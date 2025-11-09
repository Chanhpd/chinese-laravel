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
                'name' => 'Chào hỏi cơ bản',
                'name_zh' => '问候语',
                'description' => 'Các cách chào hỏi và giới thiệu bản thân trong tiếng Trung',
                'image_url' => 'https://example.com/images/greetings.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Gia đình',
                'name_zh' => '家庭',
                'description' => 'Từ vựng về thành viên gia đình và quan hệ họ hàng',
                'image_url' => 'https://example.com/images/family.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Số đếm',
                'name_zh' => '数字',
                'description' => 'Các số đếm từ 0 đến 100 và cách sử dụng',
                'image_url' => 'https://example.com/images/numbers.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Màu sắc',
                'name_zh' => '颜色',
                'description' => 'Từ vựng về màu sắc và cách mô tả',
                'image_url' => 'https://example.com/images/colors.jpg',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Đồ ăn & Thức uống',
                'name_zh' => '食物和饮料',
                'description' => 'Từ vựng về đồ ăn, thức uống và nhà hàng',
                'image_url' => 'https://example.com/images/food.jpg',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Mua sắm',
                'name_zh' => '购物',
                'description' => 'Từ vựng liên quan đến mua sắm và giá cả',
                'image_url' => 'https://example.com/images/shopping.jpg',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Du lịch',
                'name_zh' => '旅游',
                'description' => 'Từ vựng hữu ích cho du lịch và di chuyển',
                'image_url' => 'https://example.com/images/travel.jpg',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Thời gian',
                'name_zh' => '时间',
                'description' => 'Từ vựng về thời gian, ngày tháng, giờ phút',
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
