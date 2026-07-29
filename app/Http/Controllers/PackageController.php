<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::query();

        if ($request->has('slug')) {
            $query->where('slug', $request->slug);
        }

        $packages = $query->get();

        return response()->json($packages);
    }

    public function show($id)
    {
        return response()->json(Package::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:packages',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'max_roofline_ft' => 'required|integer',
            'features' => 'required|array',
            'color' => 'required|string',
            'is_popular' => 'boolean'
        ]);

        $package = Package::create($validated);
        return response()->json($package, 201);
    }

    // Add Feature
    public function addFeature(Request $request, $id)
    {
        $package = Package::findOrFail($id);
        $feature = $request->input('feature');

        if ($feature) {
            $features = $package->features ?? [];
            $features[] = $feature;
            $package->features = $features;
            $package->save();
        }

        return response()->json($package);
    }

    // Remove Feature
    public function removeFeature($id, $index)
    {
        $package = Package::findOrFail($id);
        $features = $package->features ?? [];

        if (isset($features[$index])) {
            array_splice($features, $index, 1);
            $package->features = $features;
            $package->save();
        }

        return response()->json($package);
    }

    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);
        $package->update($request->all());
        return response()->json($package);
    }

    public function destroy($id)
    {
        Package::findOrFail($id)->delete();
        return response()->json(['message' => 'Package deleted']);
    }
}