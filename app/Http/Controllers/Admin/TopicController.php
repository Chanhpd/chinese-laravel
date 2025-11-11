<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\TopicTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicController extends Controller
{
    /**
     * Display a listing of topics grouped by level
     */
    public function index(Request $request)
    {
        $level = $request->get('level', 'all');
        
        $query = Topic::with('vocabularies');
        
        if ($level !== 'all') {
            // Filter by topic level instead of vocabulary level
            $query->where('level', $level);
        }
        
        $topics = $query->orderBy('sort_order')->paginate(20);
        
        // Get vocabulary count by level for each topic
        foreach ($topics as $topic) {
            $topic->vocab_by_level = DB::table('vocabularies')
                ->where('topic_id', $topic->id)
                ->select('level', DB::raw('count(*) as count'))
                ->groupBy('level')
                ->get()
                ->pluck('count', 'level');
        }
        
        return view('admin.topics.index', compact('topics', 'level'));
    }

    /**
     * Show the form for creating a new topic
     */
    public function create()
    {
        return view('admin.topics.create');
    }

    /**
     * Store a newly created topic
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_zh' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $topic = Topic::create($validated);

        return redirect()->route('admin.topics.show', $topic->id)
            ->with('success', 'Topic created successfully!');
    }

    /**
     * Display the specified topic with its vocabularies
     */
    public function show(Topic $topic, Request $request)
    {
        $level = $request->get('level', 'all');
        
        $vocabulariesQuery = $topic->vocabularies()->with('translations');
        
        if ($level !== 'all') {
            $vocabulariesQuery->where('level', $level);
        }
        
        $vocabularies = $vocabulariesQuery->orderBy('word')->paginate(20);
        
        // Load translations
        $topic->load('translations');
        
        // Get vocabulary count by level
        $vocabCountByLevel = DB::table('vocabularies')
            ->where('topic_id', $topic->id)
            ->select('level', DB::raw('count(*) as count'))
            ->groupBy('level')
            ->get()
            ->pluck('count', 'level');
        
        return view('admin.topics.show', compact('topic', 'vocabularies', 'level', 'vocabCountByLevel'));
    }

    /**
     * Show the form for editing the specified topic
     */
    public function edit(Topic $topic)
    {
        $topic->load('translations');
        return view('admin.topics.edit', compact('topic'));
    }

    /**
     * Update the specified topic
     */
    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_zh' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $topic->update($validated);

        return redirect()->route('admin.topics.show', $topic->id)
            ->with('success', 'Topic updated successfully!');
    }

    /**
     * Remove the specified topic
     */
    public function destroy(Topic $topic)
    {
        $vocabCount = $topic->vocabularies()->count();
        
        if ($vocabCount > 0) {
            return redirect()->route('admin.topics.index')
                ->with('error', "Cannot delete topic with {$vocabCount} vocabularies. Please delete or reassign vocabularies first.");
        }
        
        $topic->delete();
        
        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic deleted successfully!');
    }

    /**
     * Update topic translations
     */
    public function updateTranslations(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'translations' => 'required|array',
            'translations.*.language_code' => 'required|string|size:2',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        foreach ($validated['translations'] as $translationData) {
            TopicTranslation::updateOrCreate(
                [
                    'topic_id' => $topic->id,
                    'language_code' => $translationData['language_code'],
                ],
                [
                    'name' => $translationData['name'],
                    'description' => $translationData['description'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.topics.edit', $topic->id)
            ->with('success', 'Translations updated successfully!');
    }
}
