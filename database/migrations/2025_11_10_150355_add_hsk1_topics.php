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
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Delete old topics and translations
        DB::table('topic_translations')->truncate();
        DB::table('topics')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Insert 32 HSK1 topics
        $topics = [
            ['name' => 'Hello and Goodbye', 'name_zh' => '你好和再见', 'description' => 'Basic greetings and farewells', 'image_url' => 'https://cdn.langeek.co/photo/57036/thumb/lesson-1?type=png', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'People', 'name_zh' => '人们', 'description' => 'Words related to people and pronouns', 'image_url' => 'https://cdn.langeek.co/photo/57037/thumb/lesson-2?type=png', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Numbers 0 to 100', 'name_zh' => '数字0到100', 'description' => 'Counting numbers from 0 to 100', 'image_url' => 'https://cdn.langeek.co/photo/57038/thumb/lesson-3?type=png', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Family', 'name_zh' => '家庭', 'description' => 'Family members and relationships', 'image_url' => 'https://cdn.langeek.co/photo/57039/thumb/lesson-4?type=png', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Colors', 'name_zh' => '颜色', 'description' => 'Basic colors vocabulary', 'image_url' => 'https://cdn.langeek.co/photo/57040/thumb/lesson-5?type=png', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Months and Seasons', 'name_zh' => '月份和季节', 'description' => 'Months of the year and seasons', 'image_url' => 'https://cdn.langeek.co/photo/57041/thumb/lesson-6?type=png', 'is_active' => true, 'sort_order' => 6],
            ['name' => 'Time and Date', 'name_zh' => '时间和日期', 'description' => 'Telling time and dates', 'image_url' => 'https://cdn.langeek.co/photo/57042/thumb/lesson-7?type=png', 'is_active' => true, 'sort_order' => 7],
            ['name' => 'Personal Information', 'name_zh' => '个人信息', 'description' => 'Name, age, nationality, etc.', 'image_url' => 'https://cdn.langeek.co/photo/57043/thumb/lesson-8?type=png', 'is_active' => true, 'sort_order' => 8],
            ['name' => 'The Body', 'name_zh' => '身体', 'description' => 'Body parts vocabulary', 'image_url' => 'https://cdn.langeek.co/photo/57044/thumb/lesson-9?type=png', 'is_active' => true, 'sort_order' => 9],
            ['name' => 'The Head and Face', 'name_zh' => '头部和面部', 'description' => 'Parts of the head and face', 'image_url' => 'https://cdn.langeek.co/photo/57045/thumb/lesson-10?type=png', 'is_active' => true, 'sort_order' => 10],
            ['name' => 'Opposite Adjectives', 'name_zh' => '反义形容词', 'description' => 'Common opposite adjectives', 'image_url' => 'https://cdn.langeek.co/photo/57046/thumb/lesson-11?type=png', 'is_active' => true, 'sort_order' => 11],
            ['name' => 'House and Apartment', 'name_zh' => '房子和公寓', 'description' => 'Housing vocabulary', 'image_url' => 'https://cdn.langeek.co/photo/57047/thumb/lesson-12?type=png', 'is_active' => true, 'sort_order' => 12],
            ['name' => 'Furniture and Home Appliances', 'name_zh' => '家具和家电', 'description' => 'Furniture and appliances', 'image_url' => 'https://cdn.langeek.co/photo/57048/thumb/lesson-13?type=png', 'is_active' => true, 'sort_order' => 13],
            ['name' => 'Jobs', 'name_zh' => '职业', 'description' => 'Common professions', 'image_url' => 'https://cdn.langeek.co/photo/57049/thumb/lesson-14?type=png', 'is_active' => true, 'sort_order' => 14],
            ['name' => 'Clothes and Shoes', 'name_zh' => '衣服和鞋子', 'description' => 'Clothing and footwear', 'image_url' => 'https://cdn.langeek.co/photo/57050/thumb/lesson-15?type=png', 'is_active' => true, 'sort_order' => 15],
            ['name' => 'Animals', 'name_zh' => '动物', 'description' => 'Common animals', 'image_url' => 'https://cdn.langeek.co/photo/57051/thumb/lesson-16?type=png', 'is_active' => true, 'sort_order' => 16],
            ['name' => 'Basic Verbs', 'name_zh' => '基本动词', 'description' => 'Essential verbs', 'image_url' => 'https://cdn.langeek.co/photo/57052/thumb/lesson-17?type=png', 'is_active' => true, 'sort_order' => 17],
            ['name' => 'Household Items', 'name_zh' => '家居用品', 'description' => 'Common household items', 'image_url' => 'https://cdn.langeek.co/photo/57053/thumb/lesson-18?type=png', 'is_active' => true, 'sort_order' => 18],
            ['name' => 'Food and Ingredients', 'name_zh' => '食物和配料', 'description' => 'Food items and cooking ingredients', 'image_url' => 'https://cdn.langeek.co/photo/57054/thumb/lesson-19?type=png', 'is_active' => true, 'sort_order' => 19],
            ['name' => 'Food and Drinks', 'name_zh' => '食物和饮料', 'description' => 'Food and beverages', 'image_url' => 'https://cdn.langeek.co/photo/57055/thumb/lesson-20?type=png', 'is_active' => true, 'sort_order' => 20],
            ['name' => 'The Weather and Nature', 'name_zh' => '天气和自然', 'description' => 'Weather and nature vocabulary', 'image_url' => 'https://cdn.langeek.co/photo/57056/thumb/lesson-21?type=png', 'is_active' => true, 'sort_order' => 21],
            ['name' => 'Useful Verbs', 'name_zh' => '常用动词', 'description' => 'Commonly used verbs', 'image_url' => 'https://cdn.langeek.co/photo/57057/thumb/lesson-22?type=png', 'is_active' => true, 'sort_order' => 22],
            ['name' => 'School', 'name_zh' => '学校', 'description' => 'School-related vocabulary', 'image_url' => 'https://cdn.langeek.co/photo/57058/thumb/lesson-23?type=png', 'is_active' => true, 'sort_order' => 23],
            ['name' => 'City', 'name_zh' => '城市', 'description' => 'City and urban vocabulary', 'image_url' => 'https://cdn.langeek.co/photo/57059/thumb/lesson-24?type=png', 'is_active' => true, 'sort_order' => 24],
            ['name' => 'Free Time Activities', 'name_zh' => '业余活动', 'description' => 'Leisure and hobbies', 'image_url' => 'https://cdn.langeek.co/photo/57060/thumb/lesson-25?type=png', 'is_active' => true, 'sort_order' => 25],
            ['name' => 'Countries and Nationalities', 'name_zh' => '国家和国籍', 'description' => 'Countries and nationalities', 'image_url' => 'https://cdn.langeek.co/photo/57061/thumb/lesson-26?type=png', 'is_active' => true, 'sort_order' => 26],
            ['name' => 'Simple Verbs', 'name_zh' => '简单动词', 'description' => 'Simple action verbs', 'image_url' => 'https://cdn.langeek.co/photo/57062/thumb/lesson-27?type=png', 'is_active' => true, 'sort_order' => 27],
            ['name' => 'Transportation', 'name_zh' => '交通', 'description' => 'Transportation vocabulary', 'image_url' => 'https://cdn.langeek.co/photo/57063/thumb/lesson-28?type=png', 'is_active' => true, 'sort_order' => 28],
            ['name' => 'Directions and Continents', 'name_zh' => '方向和大陆', 'description' => 'Directions and continents', 'image_url' => 'https://cdn.langeek.co/photo/57064/thumb/lesson-29?type=png', 'is_active' => true, 'sort_order' => 29],
            ['name' => 'Adverbs and Pronouns', 'name_zh' => '副词和代词', 'description' => 'Common adverbs and pronouns', 'image_url' => 'https://cdn.langeek.co/photo/57065/thumb/lesson-30?type=png', 'is_active' => true, 'sort_order' => 30],
            ['name' => 'Prepositions and Determiners', 'name_zh' => '介词和限定词', 'description' => 'Prepositions and determiners', 'image_url' => 'https://cdn.langeek.co/photo/57066/thumb/lesson-31?type=png', 'is_active' => true, 'sort_order' => 31],
            ['name' => 'Describing People', 'name_zh' => '描述人物', 'description' => 'Adjectives for describing people', 'image_url' => 'https://cdn.langeek.co/photo/57067/thumb/lesson-32?type=png', 'is_active' => true, 'sort_order' => 32],
        ];

        foreach ($topics as $topic) {
            DB::table('topics')->insert(array_merge($topic, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('topics')->truncate();
    }
};
