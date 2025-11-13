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
        $hsk3Topics = [
            // 1️⃣ Cuộc sống hằng ngày & Thói quen
            ['name' => 'Daily Life and Habits', 'name_zh' => '日常生活和习惯', 'description' => 'Daily habits and routines', 'sort_order' => 63],
            ['name' => 'Housework and Chores', 'name_zh' => '家务活', 'description' => 'Household chores', 'sort_order' => 64],
            ['name' => 'At the Supermarket', 'name_zh' => '在超市', 'description' => 'Shopping at supermarket', 'sort_order' => 65],
            ['name' => 'Eating Out', 'name_zh' => '外出就餐', 'description' => 'Dining out', 'sort_order' => 66],
            ['name' => 'Health and Illness', 'name_zh' => '健康和疾病', 'description' => 'Health and sickness', 'sort_order' => 67],

            // 2️⃣ Giao tiếp & Cảm xúc
            ['name' => 'Feelings and Emotions', 'name_zh' => '感受和情绪', 'description' => 'Advanced emotions', 'sort_order' => 68],
            ['name' => 'Expressing Opinions', 'name_zh' => '表达意见', 'description' => 'Expressing views', 'sort_order' => 69],
            ['name' => 'Apologies and Thanks', 'name_zh' => '道歉和感谢', 'description' => 'Apologies and gratitude', 'sort_order' => 70],
            ['name' => 'Invitations and Suggestions', 'name_zh' => '邀请和建议', 'description' => 'Invitations and suggestions', 'sort_order' => 71],
            ['name' => 'Feelings and Relationships', 'name_zh' => '感情和关系', 'description' => 'Emotions and relationships', 'sort_order' => 72],

            // 3️⃣ Nơi chốn & Cuộc sống đô thị
            ['name' => 'City and Transport', 'name_zh' => '城市和交通', 'description' => 'City and transportation', 'sort_order' => 73],
            ['name' => 'Travel and Tourism', 'name_zh' => '旅行和旅游', 'description' => 'Travel and tourism', 'sort_order' => 74],
            ['name' => 'Hotels and Accommodation', 'name_zh' => '酒店和住宿', 'description' => 'Hotels and lodging', 'sort_order' => 75],
            ['name' => 'Post Office and Bank', 'name_zh' => '邮局和银行', 'description' => 'Post office and banking', 'sort_order' => 76],
            ['name' => 'Public Places', 'name_zh' => '公共场所', 'description' => 'Public places', 'sort_order' => 77],

            // 4️⃣ Học tập & Công việc
            ['name' => 'School Life', 'name_zh' => '学校生活', 'description' => 'School life', 'sort_order' => 78],
            ['name' => 'Office and Jobs', 'name_zh' => '办公室和工作', 'description' => 'Office and work', 'sort_order' => 79],
            ['name' => 'Technology and Internet', 'name_zh' => '科技和互联网', 'description' => 'Technology and internet', 'sort_order' => 80],
            ['name' => 'Studying and Learning', 'name_zh' => '学习', 'description' => 'Studying and learning', 'sort_order' => 81],
            ['name' => 'Dreams and Future Plans', 'name_zh' => '梦想和未来计划', 'description' => 'Dreams and plans', 'sort_order' => 82],

            // 5️⃣ Từ loại mở rộng & ngữ pháp
            ['name' => 'Time and Frequency (Advanced)', 'name_zh' => '时间和频率（高级）', 'description' => 'Advanced time expressions', 'sort_order' => 83],
            ['name' => 'Comparisons and Degrees', 'name_zh' => '比较和程度', 'description' => 'Comparisons and degrees', 'sort_order' => 84],
            ['name' => 'Conjunctions and Connectors', 'name_zh' => '连词和连接词', 'description' => 'Conjunctions', 'sort_order' => 85],
            ['name' => 'Measure Words (Advanced)', 'name_zh' => '量词（高级）', 'description' => 'Advanced measure words', 'sort_order' => 86],
            ['name' => 'Adverbs and Modal Words', 'name_zh' => '副词和语气词', 'description' => 'Adverbs and particles', 'sort_order' => 87],

            // 6️⃣ Văn hóa & Cuộc sống xã hội
            ['name' => 'Festivals and Traditions', 'name_zh' => '节日和传统', 'description' => 'Traditional festivals', 'sort_order' => 88],
            ['name' => 'Chinese Food and Drinks', 'name_zh' => '中国饮食', 'description' => 'Chinese cuisine', 'sort_order' => 89],
            ['name' => 'Cultural Differences', 'name_zh' => '文化差异', 'description' => 'Cultural differences', 'sort_order' => 90],
            ['name' => 'Environment and Weather Changes', 'name_zh' => '环境和气候变化', 'description' => 'Environment and weather', 'sort_order' => 91],
            ['name' => 'Entertainment and Media', 'name_zh' => '娱乐和媒体', 'description' => 'Entertainment and media', 'sort_order' => 92],
        ];

        foreach ($hsk3Topics as $topic) {
            DB::table('topics')->insert([
                'name' => $topic['name'],
                'name_zh' => $topic['name_zh'],
                'description' => $topic['description'],
                'sort_order' => $topic['sort_order'],
                'level' => 'HSK3',
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
        DB::table('topics')->where('level', 'HSK3')->delete();
    }
};
