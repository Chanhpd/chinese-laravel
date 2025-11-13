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
        // Add description column to topics table if it doesn't exist
        if (!Schema::hasColumn('topics', 'description')) {
            Schema::table('topics', function (Blueprint $table) {
                $table->string('description')->nullable()->after('name_zh');
            });
        }

        // Update descriptions for HSK6 topics (155-184)
        $descriptions = [
            // 1️⃣ Xã hội, chính trị và toàn cầu (5 topics: 155-159)
            155 => 'Social issues and public life vocabulary',
            156 => 'Government and politics vocabulary',
            157 => 'Law and justice vocabulary',
            158 => 'Economy and business vocabulary',
            159 => 'Globalization and international relations vocabulary',

            // 2️⃣ Văn hóa, giáo dục và truyền thông (5 topics: 160-164)
            160 => 'Culture and heritage vocabulary',
            161 => 'Art and literature vocabulary',
            162 => 'Education and knowledge vocabulary',
            163 => 'Language and communication vocabulary',
            164 => 'Media and information vocabulary',

            // 3️⃣ Tư duy, cảm xúc và nhân sinh quan (5 topics: 165-169)
            165 => 'Psychology and emotions vocabulary',
            166 => 'Personality and character vocabulary',
            167 => 'Philosophy and values vocabulary',
            168 => 'Morality and ethics vocabulary',
            169 => 'Happiness and success vocabulary',

            // 4️⃣ Môi trường, khoa học và công nghệ (5 topics: 170-174)
            170 => 'Science and innovation vocabulary',
            171 => 'Technology and AI vocabulary',
            172 => 'Environment and sustainability vocabulary',
            173 => 'Space and the universe vocabulary',
            174 => 'Medicine and health vocabulary',

            // 5️⃣ Văn hóa đại chúng & toàn cầu hóa (5 topics: 175-179)
            175 => 'Pop culture and media vocabulary',
            176 => 'Global trends and lifestyle vocabulary',
            177 => 'Work and economy in modern society vocabulary',
            178 => 'Digital life and privacy vocabulary',
            179 => 'Social media and influence vocabulary',

            // 6️⃣ Tư duy học thuật & kỹ năng ngôn ngữ (5 topics: 180-184)
            180 => 'Critical thinking vocabulary',
            181 => 'Argumentation and discussion vocabulary',
            182 => 'Writing and expression vocabulary',
            183 => 'Reading and comprehension vocabulary',
            184 => 'Advanced grammar and idioms vocabulary',
        ];

        foreach ($descriptions as $id => $description) {
            DB::table('topics')->where('id', $id)->update(['description' => $description]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove descriptions from HSK6 topics
        DB::table('topics')
            ->whereBetween('id', [155, 184])
            ->update(['description' => null]);

        // Optionally, drop the description column if no other topics use it
        // Schema::table('topics', function (Blueprint $table) {
        //     $table->dropColumn('description');
        // });
    }
};
