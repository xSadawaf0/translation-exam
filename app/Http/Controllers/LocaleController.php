<?php
namespace App\Http\Controllers;

use App\Models\Locale;
use Illuminate\Http\Request;
class LocaleController extends Controller
{

    public function index()
    {
        $locales = Locale::all();
        return api_response(
            $locales,
            200,
            'Locales fetched successfully.',
            true,
            
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:locales,code',
            'name' => 'required|string',
        ]);

        try {
            $locale = Locale::create($data);
            return api_response(
                $locale,
                201,
                'Locale created successfully.',
                true,
                
            );
        } catch (\Exception $e) {
            return api_response(
                500,
                'Failed to create locale.',
                false,
                null
            );
        }
    }
}
