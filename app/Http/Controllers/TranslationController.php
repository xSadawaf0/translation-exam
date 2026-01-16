<?php
namespace App\Http\Controllers;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string',
            'content' => 'required|string',
            'locale_id' => 'required|integer|exists:locales,id',
            'tag_id' => 'nullable|integer|exists:tags,id',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ]);

        $translation = Translation::create([
            'key' => $data['key'],
            'content' => $data['content'],
            'locale_id' => $data['locale_id'],
        ]);

        $tagIds = [];
        if (!empty($data['tags'])) {
            $tagIds = $data['tags'];
        } elseif (!empty($data['tag_id'])) {
            $tagIds = [$data['tag_id']];
        }
        if (!empty($tagIds)) {
            $translation->tags()->sync($tagIds);
        }

        $translation->load(['tags', 'locale']);
        return api_response(
            $translation,
            201,
            'Translation created successfully.',
            true
        );
    }

    public function update(Request $request, $id)
    {
        $translation = Translation::findOrFail($id);
        $data = $request->validate([
            'content' => 'string',
            'tags' => 'array',
            'tags.*' => 'integer|exists:tags,id',
        ]);
        $translation->update($data);
        if (isset($data['tags'])) {
            $translation->tags()->sync($data['tags']);
        }
        $translation->load(['tags', 'locale']);
        return api_response(
            $translation,
            200,
            'Translation updated successfully.',
            true
        );
    }

    public function show($id)
    {
        $translation = Translation::with(['tags', 'locale'])->findOrFail($id);
        return api_response(
            $translation,
            200,
            'Translation fetched successfully.',
            true
        );
    }

    public function index(Request $request)
    {
        $query = Translation::with(['tags', 'locale']);
        if ($request->filled('key')) {
            $query->where('key', 'like', '%' . $request->key . '%');
        }
        if ($request->filled('content')) {
            $query->where('content', 'like', '%' . $request->content . '%');
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }
        if ($request->filled('locale')) {
            $query->where('locale_id', $request->locale);
        }
        $perPage = $request->input('per_page', 10);
        $results = $query->select(['id', 'key', 'content', 'locale_id', 'created_at', 'updated_at'])->paginate($perPage);
        return api_response(
            $results,
            200,
            'Translations fetched successfully.',
            true
        );
    }

    public function export(Request $request)
    {
        $query = Translation::query();
        if ($request->filled('locale')) {
            $query->where('locale_id', $request->locale);
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }
        return response()->stream(function () use ($query) {
            echo '[';
            $first = true;
            $query->with(['tags', 'locale'])->chunk(500, function ($translations) use (&$first) {
                foreach ($translations as $translation) {
                    if (!$first) echo ',';
                    echo json_encode([
                        'key' => $translation->key,
                        'content' => $translation->content,
                        'locale_id' => $translation->locale_id,
                        'tags' => $translation->tags->pluck('id'),
                        'locale' => $translation->locale ? [
                            'id' => $translation->locale->id,
                            'code' => $translation->locale->code,
                            'name' => $translation->locale->name,
                        ] : null,
                    ]);
                    $first = false;
                }
            });
            echo ']';
        }, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
