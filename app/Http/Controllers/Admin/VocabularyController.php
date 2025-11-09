<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vocabulary;
use App\Models\VocabularyTranslation;
use App\Models\Topic;
use Illuminate\Http\Request;

class VocabularyController extends Controller
{
    /**
     * Display a listing of vocabularies
     */
    public function index(Request $request)
    {
        $query = Vocabulary::with('topic');
        
        // Filter by level
        if ($request->has('level') && $request->level !== 'all') {
            $query->where('level', $request->level);
        }
        
        // Filter by topic
        if ($request->has('topic_id') && $request->topic_id !== 'all') {
            $query->where('topic_id', $request->topic_id);
        }
        
        // Search by word
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('word', 'like', "%{$search}%")
                  ->orWhere('pinyin', 'like', "%{$search}%")
                  ->orWhere('meaning', 'like', "%{$search}%");
            });
        }
        
        $vocabularies = $query->orderBy('level')->orderBy('word')->paginate(20);
        
        // Get all topics for filter
        $topics = Topic::orderBy('name')->get();
        
        return view('admin.vocabularies.index', compact('vocabularies', 'topics'));
    }

    /**
     * Show the form for creating a new vocabulary
     */
    public function create(Request $request)
    {
        $topics = Topic::orderBy('name')->get();
        $preselectedTopicId = $request->get('topic_id');
        
        return view('admin.vocabularies.create', compact('topics', 'preselectedTopicId'));
    }

    /**
     * Store a newly created vocabulary
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'word' => 'required|string|max:50',
            'phonetic' => 'nullable|string|max:100',
            'pinyin' => 'nullable|string|max:100',
            'simplified' => 'nullable|string|max:50',
            'traditional' => 'nullable|string|max:50',
            'part_of_speech' => 'nullable|string|max:50',
            'meaning' => 'required|string',
            'meaning_vi' => 'nullable|string',
            'meaning_zh' => 'nullable|string',
            'example_sentence' => 'nullable|string',
            'example_translation' => 'nullable|string',
            'example_highlight' => 'nullable|string|max:50',
            'definition' => 'nullable|string',
            'radical_info' => 'nullable|string',
            'stroke_count' => 'nullable|integer',
            'tone_pattern' => 'nullable|string|max:20',
            'related_words' => 'nullable|array',
            'similar_chars' => 'nullable|array',
            'pronunciation_audio' => 'nullable|url',
            'image_url' => 'nullable|url',
            'level' => 'required|in:HSK1,HSK2,HSK3,HSK4,HSK5,HSK6',
        ]);

        $vocabulary = Vocabulary::create($validated);

        return redirect()->route('admin.vocabularies.show', $vocabulary->id)
            ->with('success', 'Vocabulary created successfully!');
    }

    /**
     * Display the specified vocabulary
     */
    public function show(Vocabulary $vocabulary)
    {
        $vocabulary->load(['topic', 'translations']);
        
        return view('admin.vocabularies.show', compact('vocabulary'));
    }

    /**
     * Show the form for editing the specified vocabulary
     */
    public function edit(Vocabulary $vocabulary)
    {
        $topics = Topic::orderBy('name')->get();
        $vocabulary->load('translations');
        
        return view('admin.vocabularies.edit', compact('vocabulary', 'topics'));
    }

    /**
     * Update the specified vocabulary
     */
    public function update(Request $request, Vocabulary $vocabulary)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'word' => 'required|string|max:50',
            'phonetic' => 'nullable|string|max:100',
            'pinyin' => 'nullable|string|max:100',
            'simplified' => 'nullable|string|max:50',
            'traditional' => 'nullable|string|max:50',
            'part_of_speech' => 'nullable|string|max:50',
            'meaning' => 'required|string',
            'meaning_vi' => 'nullable|string',
            'meaning_zh' => 'nullable|string',
            'example_sentence' => 'nullable|string',
            'example_translation' => 'nullable|string',
            'example_highlight' => 'nullable|string|max:50',
            'definition' => 'nullable|string',
            'radical_info' => 'nullable|string',
            'stroke_count' => 'nullable|integer',
            'tone_pattern' => 'nullable|string|max:20',
            'related_words' => 'nullable|array',
            'similar_chars' => 'nullable|array',
            'pronunciation_audio' => 'nullable|url',
            'image_url' => 'nullable|url',
            'level' => 'required|in:HSK1,HSK2,HSK3,HSK4,HSK5,HSK6',
        ]);

        $vocabulary->update($validated);

        return redirect()->route('admin.vocabularies.show', $vocabulary->id)
            ->with('success', 'Vocabulary updated successfully!');
    }

    /**
     * Remove the specified vocabulary
     */
    public function destroy(Vocabulary $vocabulary)
    {
        $vocabulary->delete();
        
        return redirect()->route('admin.vocabularies.index')
            ->with('success', 'Vocabulary deleted successfully!');
    }

    /**
     * Update vocabulary translations
     */
    public function updateTranslations(Request $request, Vocabulary $vocabulary)
    {
        $validated = $request->validate([
            'translations' => 'required|array',
            'translations.*.language_code' => 'required|string|size:2',
            'translations.*.meaning' => 'required|string',
            'translations.*.example_translation' => 'nullable|string',
        ]);

        foreach ($validated['translations'] as $translationData) {
            VocabularyTranslation::updateOrCreate(
                [
                    'vocabulary_id' => $vocabulary->id,
                    'language_code' => $translationData['language_code'],
                ],
                [
                    'meaning' => $translationData['meaning'],
                    'example_translation' => $translationData['example_translation'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.vocabularies.edit', $vocabulary->id)
            ->with('success', 'Translations updated successfully!');
    }
}
