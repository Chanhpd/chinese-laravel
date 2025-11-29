<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use App\Http\Resources\StoryResource;

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

        return StoryResource::collection($stories);
    }

    /**
     * Display the specified story.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $story = Story::where('slug', $slug)->firstOrFail();

        return new StoryResource($story);
    }

    /**
     * Get all HSK levels available.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function hskLevels()
    {
        $levels = Story::select('hsk_level')
            ->distinct()
            ->whereNotNull('hsk_level')
            ->orderBy('hsk_level')
            ->pluck('hsk_level');

        return response()->json([
            'data' => $levels
        ]);
    }
}
