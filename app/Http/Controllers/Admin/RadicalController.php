<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Radical;
use App\Models\Level;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RadicalController extends Controller
{
    /**
     * Get all radicals with pagination and filters.
     */
    public function index(Request $request)
    {
        $query = Radical::with('level');

        // Filter by level
        if ($request->has('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        // Filter by HSK level number
        if ($request->has('level_number')) {
            $query->whereHas('level', function ($q) use ($request) {
                $q->where('level_number', $request->level_number);
            });
        }

        // Search by hanzi, pinyin, or meaning
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('hanzi', 'like', "%{$search}%")
                  ->orWhere('traditional', 'like', "%{$search}%")
                  ->orWhere('pinyin', 'like', "%{$search}%")
                  ->orWhere('meaning', 'like', "%{$search}%")
                  ->orWhere('meaning_vi', 'like', "%{$search}%")
                  ->orWhere('meaning_en', 'like', "%{$search}%");
            });
        }

        // Filter by stroke count
        if ($request->has('stroke_count')) {
            $query->where('stroke_count', $request->stroke_count);
        }

        // Filter by favorites
        if ($request->has('is_favorite')) {
            $query->where('is_favorite', $request->is_favorite);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'frequency_rank');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if ($sortBy === 'level') {
            $query->join('levels', 'radical.level_id', '=', 'levels.id')
                  ->orderBy('levels.level_number', $sortOrder)
                  ->select('radical.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $radicals = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $radicals->map(function ($radical) {
                return [
                    'id' => $radical->id,
                    'hanzi' => $radical->hanzi,
                    'traditional' => $radical->traditional,
                    'pinyin' => $radical->pinyin,
                    'radical' => $radical->radical,
                    'stroke_count' => $radical->stroke_count,
                    'frequency_rank' => $radical->frequency_rank,
                    'general_standard' => $radical->general_standard,
                    'level_id' => $radical->level_id,
                    'level' => $radical->level ? [
                        'id' => $radical->level->id,
                        'level_name' => $radical->level->level_name,
                        'level_number' => $radical->level->level_number,
                    ] : null,
                    'meaning' => $radical->meaning,
                    'meaning_vi' => $radical->meaning_vi,
                    'meaning_cn' => $radical->meaning_cn,
                    'meaning_en' => $radical->meaning_en,
                    'meaning_jp' => $radical->meaning_jp,
                    'meaning_kr' => $radical->meaning_kr,
                    'meaning_th' => $radical->meaning_th,
                    'meaning_de' => $radical->meaning_de,
                    'meaning_fr' => $radical->meaning_fr,
                    'meaning_es' => $radical->meaning_es,
                    'meaning_it' => $radical->meaning_it,
                    'meaning_br' => $radical->meaning_br,
                    'meaning_tr' => $radical->meaning_tr,
                    'is_favorite' => $radical->is_favorite,
                ];
            }),
            'meta' => [
                'current_page' => $radicals->currentPage(),
                'last_page' => $radicals->lastPage(),
                'per_page' => $radicals->perPage(),
                'total' => $radicals->total(),
            ],
        ]);
    }

    /**
     * Get a specific radical with details.
     */
    public function show($id)
    {
        $radical = Radical::with('level')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $radical->id,
                'hanzi' => $radical->hanzi,
                'traditional' => $radical->traditional,
                'pinyin' => $radical->pinyin,
                'radical' => $radical->radical,
                'stroke_count' => $radical->stroke_count,
                'frequency_rank' => $radical->frequency_rank,
                'general_standard' => $radical->general_standard,
                'level_id' => $radical->level_id,
                'level' => $radical->level ? [
                    'id' => $radical->level->id,
                    'level_name' => $radical->level->level_name,
                    'level_number' => $radical->level->level_number,
                    'test_type' => $radical->level->test_type,
                ] : null,
                'meaning' => $radical->meaning,
                'meaning_vi' => $radical->meaning_vi,
                'meaning_cn' => $radical->meaning_cn,
                'meaning_en' => $radical->meaning_en,
                'meaning_jp' => $radical->meaning_jp,
                'meaning_kr' => $radical->meaning_kr,
                'meaning_th' => $radical->meaning_th,
                'meaning_de' => $radical->meaning_de,
                'meaning_fr' => $radical->meaning_fr,
                'meaning_es' => $radical->meaning_es,
                'meaning_it' => $radical->meaning_it,
                'meaning_br' => $radical->meaning_br,
                'meaning_tr' => $radical->meaning_tr,
                'is_favorite' => $radical->is_favorite,
            ],
        ]);
    }

    /**
     * Create a new radical.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hanzi' => 'required|string|max:10',
            'traditional' => 'nullable|string|max:10',
            'pinyin' => 'required|string|max:50',
            'radical' => 'nullable|string|max:10',
            'stroke_count' => 'nullable|integer|min:1',
            'frequency_rank' => 'nullable|integer|min:1',
            'general_standard' => 'nullable|string|max:50',
            'level_id' => 'nullable|exists:levels,id',
            'meaning' => 'nullable|string',
            'meaning_vi' => 'nullable|string',
            'meaning_cn' => 'nullable|string',
            'meaning_en' => 'nullable|string',
            'meaning_jp' => 'nullable|string',
            'meaning_kr' => 'nullable|string',
            'meaning_th' => 'nullable|string',
            'meaning_de' => 'nullable|string',
            'meaning_fr' => 'nullable|string',
            'meaning_es' => 'nullable|string',
            'meaning_it' => 'nullable|string',
            'meaning_br' => 'nullable|string',
            'meaning_tr' => 'nullable|string',
            'is_favorite' => 'nullable|boolean',
        ]);

        $radical = Radical::create($request->all());

        // Log admin action
        AdminLog::log(
            'create_radical',
            "Created radical: {$radical->hanzi} ({$radical->pinyin})",
            'Radical',
            $radical->id,
            null,
            $radical->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Radical created successfully',
            'data' => $radical->load('level'),
        ], 201);
    }

    /**
     * Update radical information.
     */
    public function update(Request $request, $id)
    {
        $radical = Radical::findOrFail($id);
        $oldValues = $radical->toArray();

        $request->validate([
            'hanzi' => 'sometimes|required|string|max:10',
            'traditional' => 'nullable|string|max:10',
            'pinyin' => 'sometimes|required|string|max:50',
            'radical' => 'nullable|string|max:10',
            'stroke_count' => 'nullable|integer|min:1',
            'frequency_rank' => 'nullable|integer|min:1',
            'general_standard' => 'nullable|string|max:50',
            'level_id' => 'nullable|exists:levels,id',
            'meaning' => 'nullable|string',
            'meaning_vi' => 'nullable|string',
            'meaning_cn' => 'nullable|string',
            'meaning_en' => 'nullable|string',
            'meaning_jp' => 'nullable|string',
            'meaning_kr' => 'nullable|string',
            'meaning_th' => 'nullable|string',
            'meaning_de' => 'nullable|string',
            'meaning_fr' => 'nullable|string',
            'meaning_es' => 'nullable|string',
            'meaning_it' => 'nullable|string',
            'meaning_br' => 'nullable|string',
            'meaning_tr' => 'nullable|string',
            'is_favorite' => 'nullable|boolean',
        ]);

        $radical->update($request->all());

        // Log admin action
        AdminLog::log(
            'update_radical',
            "Updated radical: {$radical->hanzi} ({$radical->pinyin})",
            'Radical',
            $radical->id,
            $oldValues,
            $radical->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Radical updated successfully',
            'data' => $radical->load('level'),
        ]);
    }

    /**
     * Delete a radical.
     */
    public function destroy($id)
    {
        $radical = Radical::findOrFail($id);
        $radicalInfo = "{$radical->hanzi} ({$radical->pinyin})";
        
        $radical->delete();

        // Log admin action
        AdminLog::log(
            'delete_radical',
            "Deleted radical: {$radicalInfo}",
            'Radical',
            $id
        );

        return response()->json([
            'success' => true,
            'message' => 'Radical deleted successfully',
        ]);
    }

    /**
     * Get statistics about radicals.
     */
    public function statistics()
    {
        $stats = [
            'total_radicals' => Radical::count(),
            'by_level' => Level::where('test_type', 'HSK')
                ->withCount('radicals')
                ->orderBy('level_number')
                ->get()
                ->map(function ($level) {
                    return [
                        'level_name' => $level->level_name,
                        'level_number' => $level->level_number,
                        'radical_count' => $level->radicals_count,
                    ];
                }),
            'by_stroke_count' => Radical::selectRaw('stroke_count, COUNT(*) as count')
                ->whereNotNull('stroke_count')
                ->groupBy('stroke_count')
                ->orderBy('stroke_count')
                ->get()
                ->map(function ($item) {
                    return [
                        'stroke_count' => $item->stroke_count,
                        'count' => $item->count,
                    ];
                }),
            'favorites_count' => Radical::where('is_favorite', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Bulk import radicals from JSON.
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'radicals' => 'required|array',
            'radicals.*.hanzi' => 'required|string',
            'radicals.*.pinyin' => 'required|string',
            'level_id' => 'nullable|exists:levels,id',
        ]);

        $imported = 0;
        $errors = [];

        foreach ($request->radicals as $index => $radicalData) {
            try {
                // Merge level_id if provided at root level
                if ($request->has('level_id') && !isset($radicalData['level_id'])) {
                    $radicalData['level_id'] = $request->level_id;
                }

                Radical::create($radicalData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'data' => $radicalData,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Log admin action
        AdminLog::log(
            'bulk_import_radicals',
            "Bulk imported {$imported} radicals",
            'Radical',
            null
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully imported {$imported} radicals",
            'imported' => $imported,
            'errors' => $errors,
        ]);
    }

    /**
     * Update multiple radicals at once.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:radical,id',
            'data' => 'required|array',
        ]);

        $updated = Radical::whereIn('id', $request->ids)
            ->update($request->data);

        // Log admin action
        AdminLog::log(
            'bulk_update_radicals',
            "Bulk updated {$updated} radicals",
            'Radical',
            null
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updated} radicals",
            'updated' => $updated,
        ]);
    }

    /**
     * Delete multiple radicals at once.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:radical,id',
        ]);

        $deleted = Radical::whereIn('id', $request->ids)->delete();

        // Log admin action
        AdminLog::log(
            'bulk_delete_radicals',
            "Bulk deleted {$deleted} radicals",
            'Radical',
            null
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deleted} radicals",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Get available HSK levels for radicals.
     */
    public function getLevels()
    {
        $levels = Level::where('test_type', 'HSK')
            ->orderBy('level_number')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $levels,
        ]);
    }
}
