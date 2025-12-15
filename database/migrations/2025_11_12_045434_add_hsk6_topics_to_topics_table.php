<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert HSK6 topics (IDs 155-184, 30 topics total)
        DB::table('topics')->insert([
            // 1️⃣ Xã hội, chính trị và toàn cầu (5 topics: 155-159)
            [
                'id' => 155,
                'name' => 'Social Issues and Public Life',
                'name_zh' => '社会问题与公共生活',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 156,
                'name' => 'Government and Politics',
                'name_zh' => '政府与政治',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 157,
                'name' => 'Law and Justice',
                'name_zh' => '法律与公正',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 158,
                'name' => 'Economy and Business',
                'name_zh' => '经济与商业',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 159,
                'name' => 'Globalization and International Relations',
                'name_zh' => '全球化与国际关系',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2️⃣ Văn hóa, giáo dục và truyền thông (5 topics: 160-164)
            [
                'id' => 160,
                'name' => 'Culture and Heritage',
                'name_zh' => '文化与遗产',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 161,
                'name' => 'Art and Literature',
                'name_zh' => '艺术与文学',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 162,
                'name' => 'Education and Knowledge',
                'name_zh' => '教育与知识',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 163,
                'name' => 'Language and Communication',
                'name_zh' => '语言与交流',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 164,
                'name' => 'Media and Information',
                'name_zh' => '媒体与信息',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 3️⃣ Tư duy, cảm xúc và nhân sinh quan (5 topics: 165-169)
            [
                'id' => 165,
                'name' => 'Psychology and Emotions',
                'name_zh' => '心理学与情绪',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 166,
                'name' => 'Personality and Character',
                'name_zh' => '性格与品格',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 167,
                'name' => 'Philosophy and Values',
                'name_zh' => '哲学与价值观',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 168,
                'name' => 'Morality and Ethics',
                'name_zh' => '道德与伦理',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 169,
                'name' => 'Happiness and Success',
                'name_zh' => '幸福与成功',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 4️⃣ Môi trường, khoa học và công nghệ (5 topics: 170-174)
            [
                'id' => 170,
                'name' => 'Science and Innovation',
                'name_zh' => '科学与创新',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 171,
                'name' => 'Technology and AI',
                'name_zh' => '技术与人工智能',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 172,
                'name' => 'Environment and Sustainability',
                'name_zh' => '环境与可持续发展',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 173,
                'name' => 'Space and the Universe',
                'name_zh' => '太空与宇宙',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 174,
                'name' => 'Medicine and Health',
                'name_zh' => '医学与健康',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 5️⃣ Văn hóa đại chúng & toàn cầu hóa (5 topics: 175-179)
            [
                'id' => 175,
                'name' => 'Pop Culture and Media',
                'name_zh' => '流行文化与媒体',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 176,
                'name' => 'Global Trends and Lifestyle',
                'name_zh' => '全球趋势与生活方式',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 177,
                'name' => 'Work and Economy in Modern Society',
                'name_zh' => '现代社会的工作与经济',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 178,
                'name' => 'Digital Life and Privacy',
                'name_zh' => '数字生活与隐私',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 179,
                'name' => 'Social Media and Influence',
                'name_zh' => '社交媒体与影响力',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 6️⃣ Tư duy học thuật & kỹ năng ngôn ngữ (5 topics: 180-184)
            [
                'id' => 180,
                'name' => 'Critical Thinking',
                'name_zh' => '批判性思维',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 181,
                'name' => 'Argumentation and Discussion',
                'name_zh' => '论证与讨论',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 182,
                'name' => 'Writing and Expression',
                'name_zh' => '写作与表达',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 183,
                'name' => 'Reading and Comprehension',
                'name_zh' => '阅读与理解',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 184,
                'name' => 'Advanced Grammar and Idioms',
                'name_zh' => '高级语法与成语',
                'level' => 'HSK6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete HSK6 topics (IDs 155-184)
        DB::table('topics')->whereBetween('id', [155, 184])->delete();
    }
};
