<?php
namespace App\Http\Controllers;


use App\Models\Tag;
use Illuminate\Http\Request;
class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return api_response($tags, 200, 'Tag list', true);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
        ]);
        $existing = Tag::where('name', $data['name'])->first();
        if ($existing) {
            return api_response($existing, 409, 'Tag already exists', false);
        }
        $tag = Tag::create($data);
        return api_response($tag, 201, 'Tag created', true);
    }
}
