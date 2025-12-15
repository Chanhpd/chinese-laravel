<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use App\Http\Requests\StoryRequest;
use Illuminate\Support\Str;

class StoryController extends Controller
{
    /**
     * Display a listing of the stories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Story::query();

        // Filter by HSK level
        if ($request->has('hsk_level')) {
            $query->byHskLevel($request->hsk_level);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Order by created_at descending
        $query->orderBy('created_at', 'desc');

        // Pagination
        $perPage = $request->input('per_page', 15);
        $stories = $query->paginate($perPage);

        return response()->json($stories);
    }

    /**
     * Store a newly created story in storage.
     *
     * @param  \App\Http\Requests\StoryRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoryRequest $request)
    {
        $data = $request->validated();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title_english']);
        }

        $story = Story::create($data);

        return response()->json([
            'message' => 'Story created successfully',
            'data' => $story
        ], 201);
    }

    /**
     * Display the specified story.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $story = Story::findOrFail($id);

        return response()->json([
            'data' => $story
        ]);
    }

    /**
     * Update the specified story in storage.
     *
     * @param  \App\Http\Requests\StoryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoryRequest $request, $id)
    {
        $story = Story::findOrFail($id);
        
        $data = $request->validated();
        
        // Update slug if title_english changed and slug not provided
        if (!empty($data['title_english']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title_english']);
        }

        $story->update($data);

        return response()->json([
            'message' => 'Story updated successfully',
            'data' => $story
        ]);
    }

    /**
     * Remove the specified story from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $story = Story::findOrFail($id);
        $story->delete();

        return response()->json([
            'message' => 'Story deleted successfully'
        ]);
    }

    /**
     * Get statistics about stories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        $stats = [
            'total' => Story::count(),
            'by_hsk_level' => Story::select('hsk_level')
                ->selectRaw('count(*) as count')
                ->groupBy('hsk_level')
                ->orderBy('hsk_level')
                ->get()
                ->pluck('count', 'hsk_level'),
        ];

        return response()->json($stats);
    }
}
