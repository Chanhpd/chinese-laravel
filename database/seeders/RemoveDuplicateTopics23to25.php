<?php

namespace Database\Seeders;

use App\Models\Vocabulary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateTopics23to25 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Starting to remove duplicates for Topics 23-25...\n";
        
        // Get count before
        $beforeTotal = Vocabulary::count();
        $beforeTopic23 = Vocabulary::where('topic_id', 23)->count();
        $beforeTopic24 = Vocabulary::where('topic_id', 24)->count();
        $beforeTopic25 = Vocabulary::where('topic_id', 25)->count();
        
        echo "Before cleanup:\n";
        echo "  Total vocabularies: $beforeTotal\n";
        echo "  Topic 23: $beforeTopic23\n";
        echo "  Topic 24: $beforeTopic24\n";
        echo "  Topic 25: $beforeTopic25\n\n";
        
        // Remove duplicates for Topics 23-25
        foreach ([23, 24, 25] as $topicId) {
            echo "Processing Topic $topicId...\n";
            
            // Get all vocabularies for this topic
            $vocabularies = Vocabulary::where('topic_id', $topicId)
                ->orderBy('id', 'asc')
                ->get();
            
            // Group by word (simplified Chinese)
            $grouped = $vocabularies->groupBy('word');
            
            $deletedCount = 0;
            foreach ($grouped as $word => $items) {
                if ($items->count() > 1) {
                    // Keep the first one, delete the rest
                    $keepId = $items->first()->id;
                    $deleteIds = $items->pluck('id')->filter(function($id) use ($keepId) {
                        return $id != $keepId;
                    })->toArray();
                    
                    if (!empty($deleteIds)) {
                        Vocabulary::whereIn('id', $deleteIds)->delete();
                        $deletedCount += count($deleteIds);
                        echo "  Deleted " . count($deleteIds) . " duplicate(s) for word: $word (kept ID: $keepId)\n";
                    }
                }
            }
            
            echo "  Topic $topicId: Deleted $deletedCount duplicate record(s)\n\n";
        }
        
        // Get count after
        $afterTotal = Vocabulary::count();
        $afterTopic23 = Vocabulary::where('topic_id', 23)->count();
        $afterTopic24 = Vocabulary::where('topic_id', 24)->count();
        $afterTopic25 = Vocabulary::where('topic_id', 25)->count();
        
        echo "After cleanup:\n";
        echo "  Total vocabularies: $afterTotal\n";
        echo "  Topic 23: $afterTopic23\n";
        echo "  Topic 24: $afterTopic24\n";
        echo "  Topic 25: $afterTopic25\n\n";
        
        echo "Total deleted: " . ($beforeTotal - $afterTotal) . " records\n";
        echo "Cleanup completed successfully!\n";
    }
}
