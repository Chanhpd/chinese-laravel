<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Vocabulary;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalTopics = Topic::count();
        $totalVocabularies = Vocabulary::count();
        $activeTopics = Topic::where('is_active', true)->count();
        
        // Get vocabulary count by level
        $vocabByLevel = Vocabulary::select('level', DB::raw('count(*) as count'))
            ->groupBy('level')
            ->orderBy('level')
            ->get();
        
        // Get vocabulary count by topic (top 10)
        $topTopics = Topic::withCount('vocabularies')
            ->orderByDesc('vocabularies_count')
            ->limit(10)
            ->get();
        
        // Recent vocabularies
        $recentVocabularies = Vocabulary::with('topic')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalTopics',
            'totalVocabularies',
            'activeTopics',
            'vocabByLevel',
            'topTopics',
            'recentVocabularies'
        ));
    }
}
