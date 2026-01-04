<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // Read JSON file
        $jsonPath = base_path('docs/data/mock_exam_data.json');
        if (!File::exists($jsonPath)) {
            $this->command->error('File mock_exam_data.json not found!');
            return;
        }

        $data = json_decode(File::get($jsonPath), true);
        if (!$data || !isset($data['Questions'])) {
            $this->command->error('Invalid JSON structure!');
            return;
        }

        $examData = $data['Questions'];

        DB::beginTransaction();
        try {
            // 1. Create Exam
            $exam = DB::table('exams')->insertGetId([
                'title' => $examData['title'],
                'time' => $examData['time'],
                'level' => 'HSK1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("✓ Created exam: {$examData['title']}");

            // 2. Create Question Types (if not exists)
            $questionTypes = [
                ['code' => '110001', 'name' => 'Listening - True/False', 'part_type' => 'listening'],
                ['code' => '110002', 'name' => 'Listening - Image Selection', 'part_type' => 'listening'],
                ['code' => '110003', 'name' => 'Listening - Image Matching', 'part_type' => 'listening'],
                ['code' => '110004', 'name' => 'Listening - Comprehension', 'part_type' => 'listening'],
                ['code' => '120001', 'name' => 'Reading - Image Matching', 'part_type' => 'reading'],
                ['code' => '120002', 'name' => 'Reading - Sentence Matching', 'part_type' => 'reading'],
                ['code' => '120003', 'name' => 'Reading - Fill in Blank', 'part_type' => 'reading'],
                ['code' => '120004', 'name' => 'Reading - Comprehension', 'part_type' => 'reading'],
            ];

            foreach ($questionTypes as $type) {
                DB::table('question_types')->updateOrInsert(
                    ['code' => $type['code']],
                    array_merge($type, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }

            $this->command->info("✓ Created question types");

            // 3. Process Exam Parts (limit to first 2 question types per part)
            $partOrder = 1;
            foreach ($examData['parts'] as $partData) {
                // Create Exam Part
                $examPartId = DB::table('exam_parts')->insertGetId([
                    'exam_id' => $exam,
                    'name' => $partData['name'],
                    'time' => $partData['time'],
                    'order' => $partOrder++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("  ✓ Part: {$partData['name']}");

                // Process Question Groups (ALL groups)
                $contentCount = 0;
                foreach ($partData['content'] as $questionGroup) {
                    $questionTypeCode = $questionGroup['kind'];
                    
                    // Get question type ID
                    $questionType = DB::table('question_types')
                        ->where('code', $questionTypeCode)
                        ->first();

                    if (!$questionType) {
                        continue;
                    }

                    // Process Questions (ALL questions)
                    foreach ($questionGroup['Questions'] as $questionData) {
                        // Create Question
                        $questionId = DB::table('questions')->insertGetId([
                            'exam_part_id' => $examPartId,
                            'question_type_id' => $questionType->id,
                            'order' => $contentCount++,
                            'g_text' => json_encode($questionData['general']['G_text'] ?? []),
                            'g_text_translate' => json_encode($questionData['general']['G_text_translate'] ?? []),
                            'g_text_audio' => $questionData['general']['G_text_audio'] ?? null,
                            'g_text_audio_translate' => json_encode($questionData['general']['G_text_audio_translate'] ?? []),
                            'g_audio' => json_encode($questionData['general']['G_audio'] ?? []),
                            'g_image' => json_encode($questionData['general']['G_image'] ?? []),
                            'total_score' => array_sum($questionData['scores'] ?? [5]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Create Question Contents
                        $subOrder = 1;
                        foreach ($questionData['content'] as $content) {
                            DB::table('question_contents')->insert([
                                'question_id' => $questionId,
                                'sub_order' => $subOrder++,
                                'q_text' => $content['Q_text'] ?? null,
                                'q_audio' => $content['Q_audio'] ?? null,
                                'q_image' => $content['Q_image'] ?? null,
                                'a_text' => json_encode($content['A_text'] ?? []),
                                'a_audio' => json_encode($content['A_audio'] ?? []),
                                'a_image' => json_encode($content['A_image'] ?? []),
                                'a_correct' => json_encode($content['A_correct'] ?? []),
                                'a_more_correct' => json_encode($content['A_more_correct'] ?? []),
                                'explain' => json_encode($content['explain'] ?? []),
                                'advance_explain' => json_encode($content['advance_explain'] ?? []),
                                'lang_explain_advance' => json_encode($content['langExplainAdvance'] ?? []),
                                'score' => 5,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            $this->command->info("\n✅ Exam data imported successfully (FULL DATA)!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
            throw $e;
        }
    }
}
