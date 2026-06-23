<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SystemLanguageController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        $currentLang = $setting ? $setting->system_language : 'en';
        return view('admin.settings.language', compact('currentLang'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'language' => 'required|in:en,sw,ar',
        ]);

        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create(['system_language' => $request->language]);
        } else {
            $setting->update(['system_language' => $request->language]);
        }

        session(['locale' => $request->language]);

        return redirect()->back()->with('success', __('Language updated successfully'));
    }
}
