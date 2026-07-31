<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /** Public – storefront header */
    public function publicIndex()
    {
        $keys = [
            'contact_email',
            'contact_phone',
            'contact_phone_display',
            'contact_address',
            'social_facebook',
            'social_instagram',
            'social_x',
            'social_youtube',
            'social_pinterest',
        ];

        $all = Setting::allCached();
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = $all[$key] ?? '';
        }

        return response()->json($data);
    }

    /** Admin only */
    public function index(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['message' => 'Not allowed. Please contact admin support.'], 403);
        }

        return $this->publicIndex();
    }

    public function update(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['message' => 'Not allowed. Please contact admin support.'], 403);
        }

        $validated = $request->validate([
            'contact_email'          => 'nullable|email|max:255',
            'contact_phone'          => 'nullable|string|max:30',
            'contact_phone_display'  => 'nullable|string|max:30',
            'contact_address'        => 'nullable|string|max:500',
            'social_facebook'        => 'nullable|url|max:500',
            'social_instagram'       => 'nullable|url|max:500',
            'social_x'               => 'nullable|url|max:500',
            'social_youtube'         => 'nullable|url|max:500',
            'social_pinterest'       => 'nullable|url|max:500',
        ]);

        Setting::setMany($validated);

        return response()->json([
            'message'  => 'Settings saved',
            'settings' => Setting::allCached(),
        ]);
    }

    private function isAdmin(Request $request): bool
    {
        $user = $request->user();
        if (!$user) return false;
        return in_array($user->role, ['admin', 'super_admin', 'administrator'], true);
    }
}