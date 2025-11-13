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
        // Delete old Vietnamese HSK5 topics
        DB::table('topics')->whereBetween('id', [123, 154])->delete();

        // Insert new English HSK5 topics
        $topics = [
            // 1. Life & Family (5 topics: ID 123-127)
            ['id' => 123, 'name' => 'Family and Relationships', 'name_zh' => '家庭与关系', 'level' => 'HSK5', 'description' => 'Parents, children, relatives, couples, friends'],
            ['id' => 124, 'name' => 'Marriage and Love', 'name_zh' => '婚姻与爱情', 'level' => 'HSK5', 'description' => 'Marriage, romance, relationships'],
            ['id' => 125, 'name' => 'Urban and Rural Life', 'name_zh' => '城市与农村生活', 'level' => 'HSK5', 'description' => 'City life, village life, lifestyle differences'],
            ['id' => 126, 'name' => 'Customs and Traditions', 'name_zh' => '习俗与传统', 'level' => 'HSK5', 'description' => 'Customs, etiquette, traditional festivals'],
            ['id' => 127, 'name' => 'Housing and Living Environment', 'name_zh' => '住房与居住环境', 'level' => 'HSK5', 'description' => 'Housing, decoration, neighbors'],

            // 2. Work & Career (4 topics: ID 128-131)
            ['id' => 128, 'name' => 'Career and Employment', 'name_zh' => '职业与就业', 'level' => 'HSK5', 'description' => 'Occupation, employment, interviews, resumes'],
            ['id' => 129, 'name' => 'Business and Economy', 'name_zh' => '商业与经济', 'level' => 'HSK5', 'description' => 'Economy, trade, market, advertising'],
            ['id' => 130, 'name' => 'Work Environment and Communication', 'name_zh' => '工作环境与沟通', 'level' => 'HSK5', 'description' => 'Teamwork, meetings, communication skills'],
            ['id' => 131, 'name' => 'Finance and Investment', 'name_zh' => '财务与投资', 'level' => 'HSK5', 'description' => 'Income, consumption, investment'],

            // 3. Education & Learning (4 topics: ID 132-135)
            ['id' => 132, 'name' => 'Lifelong Learning', 'name_zh' => '终身学习', 'level' => 'HSK5', 'description' => 'Continuous learning, self-study'],
            ['id' => 133, 'name' => 'Modern and Traditional Education', 'name_zh' => '现代与传统教育', 'level' => 'HSK5', 'description' => 'Modern education, exam-oriented education'],
            ['id' => 134, 'name' => 'Foreign Language Learning', 'name_zh' => '外语学习', 'level' => 'HSK5', 'description' => 'Language learning, cultural differences'],
            ['id' => 135, 'name' => 'Exams and Achievement', 'name_zh' => '考试与成绩', 'level' => 'HSK5', 'description' => 'Exams, grades, goals'],

            // 4. Culture & Arts (4 topics: ID 136-139)
            ['id' => 136, 'name' => 'Chinese Literature', 'name_zh' => '中国文学', 'level' => 'HSK5', 'description' => 'Poetry, novels, prose'],
            ['id' => 137, 'name' => 'Film, Music, and Painting', 'name_zh' => '影视音乐美术', 'level' => 'HSK5', 'description' => 'Movies, music, fine arts'],
            ['id' => 138, 'name' => 'Pop Culture', 'name_zh' => '流行文化', 'level' => 'HSK5', 'description' => 'Popular culture, celebrities, internet culture'],
            ['id' => 139, 'name' => 'Thought and Philosophy', 'name_zh' => '思想与哲学', 'level' => 'HSK5', 'description' => 'Confucianism, Taoism, modern concepts'],

            // 5. Society & Current Affairs (4 topics: ID 140-143)
            ['id' => 140, 'name' => 'Social Issues', 'name_zh' => '社会问题', 'level' => 'HSK5', 'description' => 'Environmental pollution, education equity, aging, wealth gap'],
            ['id' => 141, 'name' => 'Science and Technology', 'name_zh' => '科学与技术', 'level' => 'HSK5', 'description' => 'Artificial intelligence, internet, robots'],
            ['id' => 142, 'name' => 'Media and Social Networks', 'name_zh' => '传媒与社交媒体', 'level' => 'HSK5', 'description' => 'News, media, social platforms'],
            ['id' => 143, 'name' => 'Policy and Law', 'name_zh' => '政策与法律', 'level' => 'HSK5', 'description' => 'Legal awareness, social responsibility'],

            // 6. Health & Lifestyle (4 topics: ID 144-147)
            ['id' => 144, 'name' => 'Nutrition, Exercise, and Rest', 'name_zh' => '营养运动休息', 'level' => 'HSK5', 'description' => 'Diet, exercise, rest'],
            ['id' => 145, 'name' => 'Mental Health', 'name_zh' => '心理健康', 'level' => 'HSK5', 'description' => 'Mental health, stress, happiness'],
            ['id' => 146, 'name' => 'Good and Bad Habits', 'name_zh' => '好习惯与坏习惯', 'level' => 'HSK5', 'description' => 'Good habits, bad habits, daily routine'],
            ['id' => 147, 'name' => 'Traditional and Modern Medicine', 'name_zh' => '传统与现代医学', 'level' => 'HSK5', 'description' => 'Chinese medicine, Western medicine, treatment'],

            // 7. Travel & Cultural Exchange (4 topics: ID 148-151)
            ['id' => 148, 'name' => 'Domestic and International Travel', 'name_zh' => '国内外旅游', 'level' => 'HSK5', 'description' => 'Domestic travel, overseas travel, attractions'],
            ['id' => 149, 'name' => 'Cultural Experiences', 'name_zh' => '文化体验', 'level' => 'HSK5', 'description' => 'Cuisine, customs, festive activities'],
            ['id' => 150, 'name' => 'International Exchange and Study Abroad', 'name_zh' => '国际交流与留学', 'level' => 'HSK5', 'description' => 'Study abroad, cross-cultural communication'],
            ['id' => 151, 'name' => 'Travel Situations and Responses', 'name_zh' => '旅游应对', 'level' => 'HSK5', 'description' => 'Asking directions, accommodation, shopping'],

            // 8. Life Philosophy & Self-awareness (3 topics: ID 152-154)
            ['id' => 152, 'name' => 'Goals, Dreams, and Success', 'name_zh' => '目标与成功', 'level' => 'HSK5', 'description' => 'Ideals, success, effort'],
            ['id' => 153, 'name' => 'Failure and Learning', 'name_zh' => '失败与成长', 'level' => 'HSK5', 'description' => 'Failure, experience, growth'],
            ['id' => 154, 'name' => 'Life Outlook and Values', 'name_zh' => '人生观与价值观', 'level' => 'HSK5', 'description' => 'Life philosophy, values, happiness'],
        ];

        foreach ($topics as $topic) {
            DB::table('topics')->insert([
                'id' => $topic['id'],
                'name' => $topic['name'],
                'name_zh' => $topic['name_zh'],
                'level' => $topic['level'],
                'description' => $topic['description'],
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
        DB::table('topics')->whereBetween('id', [123, 154])->delete();

        // Restore Vietnamese topics if needed
        $topics = [
            ['id' => 123, 'name' => 'Gia đình và mối quan hệ', 'name_zh' => '家庭与关系', 'level' => 'HSK5', 'description' => '父母、孩子、亲戚、夫妻、朋友'],
            ['id' => 124, 'name' => 'Hôn nhân & tình yêu', 'name_zh' => '婚姻与爱情', 'level' => 'HSK5', 'description' => '婚姻、恋爱、感情'],
            ['id' => 125, 'name' => 'Cuộc sống đô thị và nông thôn', 'name_zh' => '城市与农村生活', 'level' => 'HSK5', 'description' => '城市生活、乡村生活'],
            ['id' => 126, 'name' => 'Phong tục, lễ nghi và truyền thống', 'name_zh' => '习俗与传统', 'level' => 'HSK5', 'description' => '习俗、礼节、传统节日'],
            ['id' => 127, 'name' => 'Nhà ở & môi trường sống', 'name_zh' => '住房与居住环境', 'level' => 'HSK5', 'description' => '住房、装修、邻居'],
            ['id' => 128, 'name' => 'Sự nghiệp & việc làm', 'name_zh' => '职业与就业', 'level' => 'HSK5', 'description' => '职业、就业、面试、简历'],
            ['id' => 129, 'name' => 'Kinh doanh & kinh tế', 'name_zh' => '商业与经济', 'level' => 'HSK5', 'description' => '经济、贸易、市场、广告'],
            ['id' => 130, 'name' => 'Môi trường làm việc & kỹ năng giao tiếp', 'name_zh' => '工作环境与沟通', 'level' => 'HSK5', 'description' => '团队合作、会议、沟通技巧'],
            ['id' => 131, 'name' => 'Tài chính & đầu tư cơ bản', 'name_zh' => '财务与投资', 'level' => 'HSK5', 'description' => '收入、消费、投资'],
            ['id' => 132, 'name' => 'Học tập suốt đời', 'name_zh' => '终身学习', 'level' => 'HSK5', 'description' => '终身学习、自学'],
            ['id' => 133, 'name' => 'Giáo dục hiện đại và truyền thống', 'name_zh' => '现代与传统教育', 'level' => 'HSK5', 'description' => '现代教育、应试教育'],
            ['id' => 134, 'name' => 'Học ngoại ngữ', 'name_zh' => '外语学习', 'level' => 'HSK5', 'description' => '语言学习、文化差异'],
            ['id' => 135, 'name' => 'Kỳ thi, đánh giá, thành tích', 'name_zh' => '考试与成绩', 'level' => 'HSK5', 'description' => '考试、成绩、目标'],
            ['id' => 136, 'name' => 'Văn học Trung Hoa', 'name_zh' => '中国文学', 'level' => 'HSK5', 'description' => '诗歌、小说、散文'],
            ['id' => 137, 'name' => 'Điện ảnh, âm nhạc, hội họa', 'name_zh' => '影视音乐美术', 'level' => 'HSK5', 'description' => '电影、音乐、美术'],
            ['id' => 138, 'name' => 'Văn hóa đại chúng', 'name_zh' => '流行文化', 'level' => 'HSK5', 'description' => '流行文化、明星、网络文化'],
            ['id' => 139, 'name' => 'Tư tưởng & triết học', 'name_zh' => '思想与哲学', 'level' => 'HSK5', 'description' => '儒家思想、道家、现代观念'],
            ['id' => 140, 'name' => 'Các vấn đề xã hội', 'name_zh' => '社会问题', 'level' => 'HSK5', 'description' => '环境污染、教育公平、老龄化、贫富差距'],
            ['id' => 141, 'name' => 'Khoa học và công nghệ', 'name_zh' => '科学与技术', 'level' => 'HSK5', 'description' => '人工智能、互联网、机器人'],
            ['id' => 142, 'name' => 'Truyền thông & mạng xã hội', 'name_zh' => '传媒与社交媒体', 'level' => 'HSK5', 'description' => '新闻、媒体、社交平台'],
            ['id' => 143, 'name' => 'Chính sách & pháp luật cơ bản', 'name_zh' => '政策与法律', 'level' => 'HSK5', 'description' => '法律意识、社会责任'],
            ['id' => 144, 'name' => 'Dinh dưỡng, thể thao, nghỉ ngơi', 'name_zh' => '营养运动休息', 'level' => 'HSK5', 'description' => '饮食、锻炼、休息'],
            ['id' => 145, 'name' => 'Sức khỏe tinh thần', 'name_zh' => '心理健康', 'level' => 'HSK5', 'description' => '心理健康、压力、幸福感'],
            ['id' => 146, 'name' => 'Thói quen tốt và xấu', 'name_zh' => '好习惯与坏习惯', 'level' => 'HSK5', 'description' => '好习惯、坏习惯、作息'],
            ['id' => 147, 'name' => 'Y học cổ truyền & hiện đại', 'name_zh' => '传统与现代医学', 'level' => 'HSK5', 'description' => '中医、西医、治疗'],
            ['id' => 148, 'name' => 'Du lịch trong nước và quốc tế', 'name_zh' => '国内外旅游', 'level' => 'HSK5', 'description' => '国内游、出国游、景点'],
            ['id' => 149, 'name' => 'Trải nghiệm văn hóa', 'name_zh' => '文化体验', 'level' => 'HSK5', 'description' => '美食、习俗、节庆活动'],
            ['id' => 150, 'name' => 'Giao lưu quốc tế & du học', 'name_zh' => '国际交流与留学', 'level' => 'HSK5', 'description' => '留学、跨文化交流'],
            ['id' => 151, 'name' => 'Ứng xử trong các tình huống du lịch', 'name_zh' => '旅游应对', 'level' => 'HSK5', 'description' => '问路、住宿、购物'],
            ['id' => 152, 'name' => 'Mục tiêu, ước mơ, thành công', 'name_zh' => '目标与成功', 'level' => 'HSK5', 'description' => '理想、成功、努力'],
            ['id' => 153, 'name' => 'Thất bại và học hỏi', 'name_zh' => '失败与成长', 'level' => 'HSK5', 'description' => '失败、经验、成长'],
            ['id' => 154, 'name' => 'Quan điểm sống, giá trị, hạnh phúc', 'name_zh' => '人生观与价值观', 'level' => 'HSK5', 'description' => '人生观、价值观、幸福'],
        ];

        foreach ($topics as $topic) {
            DB::table('topics')->insert([
                'id' => $topic['id'],
                'name' => $topic['name'],
                'name_zh' => $topic['name_zh'],
                'level' => $topic['level'],
                'description' => $topic['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
