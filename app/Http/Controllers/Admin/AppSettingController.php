<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = AppSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.app-settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.app-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:app_settings,key',
            'value' => 'nullable',
            'type' => 'required|in:string,file,boolean,json',
            'group' => 'required|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $data = $request->all();

        // Handle file uploads
        if ($request->type === 'file' && $request->hasFile('file_value')) {
            $data['value'] = $request->file('file_value')->store('settings', 'public');
        }

        AppSetting::create($data);

        return redirect()->route('admin.app-settings.index')
            ->with('success', 'App setting created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AppSetting $appSetting)
    {
        return view('admin.app-settings.show', compact('appSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppSetting $appSetting)
    {
        return view('admin.app-settings.edit', compact('appSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppSetting $appSetting)
    {
        $request->validate([
            'key' => 'required|string|unique:app_settings,key,' . $appSetting->id,
            'value' => 'nullable',
            'type' => 'required|in:string,file,boolean,json',
            'group' => 'required|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $data = $request->all();

        // Handle file uploads
        if ($request->type === 'file' && $request->hasFile('file_value')) {
            // Delete old file
            if ($appSetting->value && Storage::disk('public')->exists($appSetting->value)) {
                Storage::disk('public')->delete($appSetting->value);
            }
            $data['value'] = $request->file('file_value')->store('settings', 'public');
        }

        $appSetting->update($data);

        return redirect()->route('admin.app-settings.index')
            ->with('success', 'App setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppSetting $appSetting)
    {
        // Delete file if it's a file type setting
        if ($appSetting->type === 'file' && $appSetting->value && Storage::disk('public')->exists($appSetting->value)) {
            Storage::disk('public')->delete($appSetting->value);
        }

        $appSetting->delete();

        return redirect()->route('admin.app-settings.index')
            ->with('success', 'App setting deleted successfully.');
    }

    /**
     * Update multiple settings at once
     */
    public function bulkUpdate(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            // Handle file uploads for file type settings
            if ($request->hasFile("file_{$key}")) {
                $setting = AppSetting::where('key', $key)->first();
                if ($setting && $setting->type === 'file') {
                    // Delete old file
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    // Store new file
                    $value = $request->file("file_{$key}")->store('settings', 'public');
                }
            }
            AppSetting::setValue($key, $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
