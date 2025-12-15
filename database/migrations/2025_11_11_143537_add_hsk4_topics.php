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
        // HSK4 Topics (IDs 93-122, sort_order 93-122)
        $topics = [
            // Group 1: Life, Work and Study (Cuộc sống, công việc và học tập)
            ['id' => 93, 'name' => 'Workplace and Careers', 'name_zh' => '工作和职业', 'description' => 'Workplace and careers vocabulary', 'sort_order' => 93, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 94, 'name' => 'Job Hunting and Interviews', 'name_zh' => '求职和面试', 'description' => 'Job hunting and interview vocabulary', 'sort_order' => 94, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 95, 'name' => 'University and Education', 'name_zh' => '大学和教育', 'description' => 'University and education vocabulary', 'sort_order' => 95, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 96, 'name' => 'Office Communication', 'name_zh' => '办公室交流', 'description' => 'Office communication vocabulary', 'sort_order' => 96, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 97, 'name' => 'Study and Self-Improvement', 'name_zh' => '学习和自我提升', 'description' => 'Study and self-improvement vocabulary', 'sort_order' => 97, 'level' => 'HSK4', 'is_active' => true],

            // Group 2: People, Emotions & Relationships (Con người, cảm xúc & mối quan hệ)
            ['id' => 98, 'name' => 'Personality and Character', 'name_zh' => '个性和性格', 'description' => 'Personality and character vocabulary', 'sort_order' => 98, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 99, 'name' => 'Friendship and Trust', 'name_zh' => '友谊和信任', 'description' => 'Friendship and trust vocabulary', 'sort_order' => 99, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 100, 'name' => 'Love and Family Life', 'name_zh' => '爱情和家庭生活', 'description' => 'Love and family life vocabulary', 'sort_order' => 100, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 101, 'name' => 'Emotions and Attitudes', 'name_zh' => '情绪和态度', 'description' => 'Emotions and attitudes vocabulary', 'sort_order' => 101, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 102, 'name' => 'Manners and Social Behavior', 'name_zh' => '礼貌和社交行为', 'description' => 'Manners and social behavior vocabulary', 'sort_order' => 102, 'level' => 'HSK4', 'is_active' => true],

            // Group 3: Society & Culture (Xã hội & văn hóa)
            ['id' => 103, 'name' => 'Chinese Society and Culture', 'name_zh' => '中国社会和文化', 'description' => 'Chinese society and culture vocabulary', 'sort_order' => 103, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 104, 'name' => 'Festivals and Celebrations', 'name_zh' => '节日和庆祝', 'description' => 'Festivals and celebrations vocabulary', 'sort_order' => 104, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 105, 'name' => 'News and Media', 'name_zh' => '新闻和媒体', 'description' => 'News and media vocabulary', 'sort_order' => 105, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 106, 'name' => 'Technology and Innovation', 'name_zh' => '科技和创新', 'description' => 'Technology and innovation vocabulary', 'sort_order' => 106, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 107, 'name' => 'Environment and Society', 'name_zh' => '环境和社会', 'description' => 'Environment and society vocabulary', 'sort_order' => 107, 'level' => 'HSK4', 'is_active' => true],

            // Group 4: Travel & Experiences (Du lịch & trải nghiệm)
            ['id' => 108, 'name' => 'Travel Experiences', 'name_zh' => '旅游经历', 'description' => 'Travel experiences vocabulary', 'sort_order' => 108, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 109, 'name' => 'Transportation and Directions', 'name_zh' => '交通和方向', 'description' => 'Transportation and directions vocabulary', 'sort_order' => 109, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 110, 'name' => 'Hotels and Services', 'name_zh' => '酒店和服务', 'description' => 'Hotels and services vocabulary', 'sort_order' => 110, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 111, 'name' => 'Eating and Cuisine', 'name_zh' => '饮食和烹饪', 'description' => 'Eating and cuisine vocabulary', 'sort_order' => 111, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 112, 'name' => 'Shopping and Consumption', 'name_zh' => '购物和消费', 'description' => 'Shopping and consumption vocabulary', 'sort_order' => 112, 'level' => 'HSK4', 'is_active' => true],

            // Group 5: Thinking & Language Expression (Tư duy & biểu đạt ngôn ngữ)
            ['id' => 113, 'name' => 'Abstract Verbs and Thinking', 'name_zh' => '抽象动词和思维', 'description' => 'Abstract verbs and thinking vocabulary', 'sort_order' => 113, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 114, 'name' => 'Comparisons and Results', 'name_zh' => '比较和结果', 'description' => 'Comparisons and results vocabulary', 'sort_order' => 114, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 115, 'name' => 'Idioms and Expressions', 'name_zh' => '成语和表达', 'description' => 'Idioms and expressions vocabulary', 'sort_order' => 115, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 116, 'name' => 'Grammar and Connectors', 'name_zh' => '语法和连接词', 'description' => 'Grammar and connectors vocabulary', 'sort_order' => 116, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 117, 'name' => 'Time and Sequence', 'name_zh' => '时间和顺序', 'description' => 'Time and sequence vocabulary', 'sort_order' => 117, 'level' => 'HSK4', 'is_active' => true],

            // Group 6: Modern Life & Values (Cuộc sống hiện đại & giá trị)
            ['id' => 118, 'name' => 'Health and Lifestyle', 'name_zh' => '健康和生活方式', 'description' => 'Health and lifestyle vocabulary', 'sort_order' => 118, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 119, 'name' => 'Money and Economy', 'name_zh' => '金钱和经济', 'description' => 'Money and economy vocabulary', 'sort_order' => 119, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 120, 'name' => 'Entertainment and Leisure', 'name_zh' => '娱乐和休闲', 'description' => 'Entertainment and leisure vocabulary', 'sort_order' => 120, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 121, 'name' => 'Dreams and Motivation', 'name_zh' => '梦想和动力', 'description' => 'Dreams and motivation vocabulary', 'sort_order' => 121, 'level' => 'HSK4', 'is_active' => true],
            ['id' => 122, 'name' => 'Life Philosophy and Reflection', 'name_zh' => '人生哲学和反思', 'description' => 'Life philosophy and reflection vocabulary', 'sort_order' => 122, 'level' => 'HSK4', 'is_active' => true],
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
        DB::table('topics')->whereIn('id', range(93, 122))->delete();
    }
};
