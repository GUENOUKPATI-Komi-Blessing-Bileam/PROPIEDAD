<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('pages.categories.index', [
            'categories' => DB::table('categories')->orderBy('libelle')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:255', Rule::unique('categories', 'libelle')],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'libelle.required' => 'Le nom de la catégorie est obligatoire.',
            'libelle.max' => 'Le nom de la catégorie ne peut pas dépasser 255 caractères.',
            'libelle.unique' => 'Une catégorie portant ce nom existe déjà.',
            'description.max' => 'La description ne peut pas dépasser 2 000 caractères.',
        ]);

        $now = now();

        DB::table('categories')->insert([
            'id' => (string) Str::uuid(),
            'libelle' => $validated['libelle'],
            'description' => $validated['description'] ?? null,
            'actif' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('status', "La catégorie « {$validated['libelle']} » a été créée.");
    }
}
