<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamPart;
use App\Models\Question;
use App\Models\QuestionContent;
use App\Models\UserExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminExamController extends Controller
{
    /**
     * Get all exams with stats
     */
    public function index(Request $request)
    {
        $query = Exam::query();

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $exams = $query->withCount(['parts', 'attempts'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $exams,
        ]);
    }

    /**
     * Get exam details with all parts, questions
     */
    public function show($id)
    {
        $exam = Exam::with([
            'parts.questions.questionType',
            'parts.questions.contents'
        ])->findOrFail($id);

        // Calculate stats
        $totalQuestions = 0;
        $totalScore = 0;

        foreach ($exam->parts as $part) {
            foreach ($part->questions as $question) {
                $totalQuestions += $question->contents->count();
                $totalScore += $question->total_score;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => $exam,
                'stats' => [
                    'total_parts' => $exam->parts->count(),
                    'total_questions' => $totalQuestions,
                    'total_score' => $totalScore,
                    'total_attempts' => $exam->attempts()->count(),
                    'completed_attempts' => $exam->attempts()->where('status', 'completed')->count(),
                ],
            ],
        ]);
    }

    /**
     * Create new exam
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'time' => 'required|integer|min:1',
            'level' => 'required|in:HSK1,HSK2,HSK3,HSK4,HSK5,HSK6',
            'is_active' => 'boolean',
        ]);

        $exam = Exam::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Exam created successfully',
            'data' => $exam,
        ], 201);
    }

    /**
     * Update exam
     */
    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'time' => 'integer|min:1',
            'level' => 'in:HSK1,HSK2,HSK3,HSK4,HSK5,HSK6',
            'is_active' => 'boolean',
        ]);

        $exam->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Exam updated successfully',
            'data' => $exam,
        ]);
    }

    /**
     * Delete exam
     */
    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        
        // Check if exam has attempts
        if ($exam->attempts()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete exam with existing attempts',
            ], 400);
        }

        $exam->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam deleted successfully',
        ]);
    }

    /**
     * Get exam statistics
     */
    public function statistics($id)
    {
        $exam = Exam::findOrFail($id);

        $stats = [
            'total_attempts' => $exam->attempts()->count(),
            'completed_attempts' => $exam->attempts()->where('status', 'completed')->count(),
            'in_progress_attempts' => $exam->attempts()->where('status', 'in_progress')->count(),
            'average_score' => $exam->attempts()
                ->where('status', 'completed')
                ->avg('percentage'),
            'highest_score' => $exam->attempts()
                ->where('status', 'completed')
                ->max('percentage'),
            'lowest_score' => $exam->attempts()
                ->where('status', 'completed')
                ->min('percentage'),
            'average_time_spent' => $exam->attempts()
                ->where('status', 'completed')
                ->avg('time_spent'),
        ];

        // Score distribution
        $scoreRanges = [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0,
        ];

        $attempts = $exam->attempts()
            ->where('status', 'completed')
            ->select('percentage')
            ->get();

        foreach ($attempts as $attempt) {
            $percentage = $attempt->percentage;
            if ($percentage <= 20) $scoreRanges['0-20']++;
            elseif ($percentage <= 40) $scoreRanges['21-40']++;
            elseif ($percentage <= 60) $scoreRanges['41-60']++;
            elseif ($percentage <= 80) $scoreRanges['61-80']++;
            else $scoreRanges['81-100']++;
        }

        $stats['score_distribution'] = $scoreRanges;

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get exam attempts with user info
     */
    public function attempts($id, Request $request)
    {
        $exam = Exam::findOrFail($id);

        $query = UserExamAttempt::where('exam_id', $id)
            ->with('user:id,name,email');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $attempts = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $attempts,
        ]);
    }

    /**
     * Toggle exam active status
     */
    public function toggleActive($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->is_active = !$exam->is_active;
        $exam->save();

        return response()->json([
            'success' => true,
            'message' => 'Exam status updated',
            'data' => $exam,
        ]);
    }

    /**
     * Duplicate exam
     */
    public function duplicate($id)
    {
        DB::beginTransaction();
        try {
            $oldExam = Exam::with(['parts.questions.contents'])->findOrFail($id);

            // Create new exam
            $newExam = $oldExam->replicate();
            $newExam->title = $oldExam->title . ' (Copy)';
            $newExam->is_active = false;
            $newExam->save();

            // Duplicate parts and questions
            foreach ($oldExam->parts as $oldPart) {
                $newPart = $oldPart->replicate();
                $newPart->exam_id = $newExam->id;
                $newPart->save();

                foreach ($oldPart->questions as $oldQuestion) {
                    $newQuestion = $oldQuestion->replicate();
                    $newQuestion->exam_part_id = $newPart->id;
                    $newQuestion->save();

                    foreach ($oldQuestion->contents as $oldContent) {
                        $newContent = $oldContent->replicate();
                        $newContent->question_id = $newQuestion->id;
                        $newContent->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Exam duplicated successfully',
                'data' => $newExam->load('parts'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate exam: ' . $e->getMessage(),
            ], 500);
        }
    }
}
