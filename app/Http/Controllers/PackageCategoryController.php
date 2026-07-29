<?php

namespace App\Http\Controllers;

use App\Models\PackageCategory;
use Illuminate\Http\Request;

class PackageCategoryController extends Controller
{
    public function index()
    {
        return response()->json(PackageCategory::all());
    }

    public function show($id)
    {
        $category = PackageCategory::findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_title' => 'required|string',
            'package_description' => 'required|string',
            'slug' => 'required|string|unique:package_categories',
            'event_date_from' => 'nullable|date',
            'event_date_to' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $category = PackageCategory::create($validated);
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = PackageCategory::findOrFail($id);
        $category->update($request->all());
        return response()->json($category);
    }

    public function destroy($id)
    {
        PackageCategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}