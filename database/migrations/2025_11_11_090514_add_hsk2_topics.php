<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $hsk2Topics = [
            // 1️⃣ Giao tiếp & Cuộc sống hằng ngày
            ['name' => 'Daily Routine', 'name_zh' => '日常生活', 'description' => 'Daily routine vocabulary', 'sort_order' => 33],
            ['name' => 'At the Restaurant', 'name_zh' => '在餐厅', 'description' => 'Restaurant and dining vocabulary', 'sort_order' => 34],
            ['name' => 'Shopping', 'name_zh' => '购物', 'description' => 'Shopping vocabulary', 'sort_order' => 35],
            ['name' => 'Asking for Directions', 'name_zh' => '问路', 'description' => 'Direction vocabulary', 'sort_order' => 36],
            ['name' => 'Transportation', 'name_zh' => '交通', 'description' => 'Transportation vocabulary', 'sort_order' => 37],
            
            // 2️⃣ Con người & Cảm xúc
            ['name' => 'Describing Emotions', 'name_zh' => '描述情绪', 'description' => 'Emotions and feelings', 'sort_order' => 38],
            ['name' => 'Describing Appearance', 'name_zh' => '描述外貌', 'description' => 'Appearance vocabulary', 'sort_order' => 39],
            ['name' => 'Hobbies and Interests', 'name_zh' => '爱好和兴趣', 'description' => 'Hobbies and interests', 'sort_order' => 40],
            ['name' => 'Friends and Relationships', 'name_zh' => '朋友和关系', 'description' => 'Relationships vocabulary', 'sort_order' => 41],
            ['name' => 'At School or Work', 'name_zh' => '在学校或工作', 'description' => 'School and work vocabulary', 'sort_order' => 42],
            
            // 3️⃣ Địa điểm & Môi trường
            ['name' => 'Places in the City', 'name_zh' => '城市地点', 'description' => 'City places vocabulary', 'sort_order' => 43],
            ['name' => 'At Home', 'name_zh' => '在家里', 'description' => 'Home vocabulary', 'sort_order' => 44],
            ['name' => 'Weather and Seasons', 'name_zh' => '天气和季节', 'description' => 'Weather and seasons', 'sort_order' => 45],
            ['name' => 'Nature and Environment', 'name_zh' => '自然和环境', 'description' => 'Nature vocabulary', 'sort_order' => 46],
            ['name' => 'Travel and Holidays', 'name_zh' => '旅游和假期', 'description' => 'Travel vocabulary', 'sort_order' => 47],
            
            // 4️⃣ Hành động & Động từ mở rộng
            ['name' => 'Common Verbs Set 1', 'name_zh' => '常用动词1', 'description' => 'Common verbs part 1', 'sort_order' => 48],
            ['name' => 'Common Verbs Set 2', 'name_zh' => '常用动词2', 'description' => 'Common verbs part 2', 'sort_order' => 49],
            ['name' => 'Daily Activities Verbs', 'name_zh' => '日常活动动词', 'description' => 'Daily activities verbs', 'sort_order' => 50],
            ['name' => 'Health and the Body', 'name_zh' => '健康和身体', 'description' => 'Health and body vocabulary', 'sort_order' => 51],
            ['name' => 'Sports and Exercise', 'name_zh' => '运动和锻炼', 'description' => 'Sports vocabulary', 'sort_order' => 52],
            
            // 5️⃣ Từ loại mở rộng & cấu trúc
            ['name' => 'Measure Words', 'name_zh' => '量词', 'description' => 'Chinese measure words', 'sort_order' => 53],
            ['name' => 'Time Expressions', 'name_zh' => '时间表达', 'description' => 'Time expressions', 'sort_order' => 54],
            ['name' => 'Frequency and Duration', 'name_zh' => '频率和时长', 'description' => 'Frequency and duration', 'sort_order' => 55],
            ['name' => 'Question Words', 'name_zh' => '疑问词', 'description' => 'Question words', 'sort_order' => 56],
            ['name' => 'Adjectives and Opposites Set 2', 'name_zh' => '形容词和反义词2', 'description' => 'Adjectives and opposites', 'sort_order' => 57],
            
            // 6️⃣ Văn hóa & Cuộc sống hiện đại
            ['name' => 'Chinese Food and Drinks', 'name_zh' => '中国饮食', 'description' => 'Chinese food and drinks', 'sort_order' => 58],
            ['name' => 'Festivals and Holidays', 'name_zh' => '节日和假期', 'description' => 'Chinese festivals', 'sort_order' => 59],
            ['name' => 'Technology and Media', 'name_zh' => '科技和媒体', 'description' => 'Technology vocabulary', 'sort_order' => 60],
            ['name' => 'Public Services', 'name_zh' => '公共服务', 'description' => 'Public services', 'sort_order' => 61],
            ['name' => 'Cultural Customs', 'name_zh' => '文化习俗', 'description' => 'Cultural customs', 'sort_order' => 62],
        ];

        foreach ($hsk2Topics as $topic) {
            DB::table('topics')->insert([
                'name' => $topic['name'],
                'name_zh' => $topic['name_zh'],
                'description' => $topic['description'],
                'sort_order' => $topic['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete HSK2 topics (sort_order from 33 to 62)
        DB::table('topics')->whereBetween('sort_order', [33, 62])->delete();
    }
};
