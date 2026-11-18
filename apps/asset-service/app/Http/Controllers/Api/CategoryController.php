<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends AuthenticatedController
{
    public function index(Request $request): JsonResponse
    {
        $userId = $this->authenticatedUserId($request);
        if ($userId instanceof JsonResponse) {
            return $userId;
        }

        return response()->json([
            'data' => DB::table('categories')
                ->orderBy('libelle')
                ->get(['id', 'libelle', 'description', 'actif'])
                ->map(fn (object $category): array => [
                    'id' => $category->id,
                    'label' => $category->libelle,
                    'description' => $category->description,
                    'status' => $category->actif ? 'Active' : 'Inactive',
                ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $userId = $this->authenticatedUserId($request);
        if ($userId instanceof JsonResponse) {
            return $userId;
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'min:1', 'max:255', Rule::unique('categories', 'libelle')],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'label.required' => 'Le nom de la catégorie est obligatoire.',
            'label.max' => 'Le nom de la catégorie ne peut pas dépasser 255 caractères.',
            'label.unique' => 'Une catégorie portant ce nom existe déjà.',
            'description.max' => 'La description ne peut pas dépasser 2 000 caractères.',
        ]);

        $id = (string) Str::uuid();
        $now = now();

        DB::table('categories')->insert([
            'id' => $id,
            'libelle' => $validated['label'],
            'description' => $validated['description'] ?? null,
            'actif' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json([
            'id' => $id,
            'label' => $validated['label'],
            'description' => $validated['description'] ?? null,
            'status' => 'Active',
        ], 201);
    }
}
