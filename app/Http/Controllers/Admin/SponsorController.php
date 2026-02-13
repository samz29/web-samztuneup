<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::ordered()->get();
        return view('admin.sponsors.index', compact('sponsors'));
    }

    public function create()
    {
        return view('admin.sponsors.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'required|string|max:255',
            ]);
            $data['is_active'] = $request->boolean('is_active');
            $data['sort_order'] = (int) $request->input('sort_order', 0);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                \Log::info('File info: ' . $file->getClientOriginalName() . ' - ' . $file->getSize());
                $data['image'] = $request->file('image')->store('sponsors', 'public');
                \Log::info('Stored path: ' . $data['image']);
            }
            Sponsor::create($data);
            return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Sponsor upload error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal upload: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Sponsor $sponsor)
    {
        return view('admin.sponsors.edit', compact('sponsor'));
    }

    public function update(Request $request, Sponsor $sponsor)
    {
        try {
            $data = $request->validate([
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'required|string|max:255',
            ]);
            $data['is_active'] = $request->boolean('is_active');
            $data['sort_order'] = (int) $request->input('sort_order', 0);
            if ($request->hasFile('image')) {
                if ($sponsor->image) {
                    Storage::disk('public')->delete($sponsor->image);
                }
                $file = $request->file('image');
                \Log::info('File info: ' . $file->getClientOriginalName() . ' - ' . $file->getSize());
                $data['image'] = $request->file('image')->store('sponsors', 'public');
                \Log::info('Stored path: ' . $data['image']);
            }
            $sponsor->update($data);
            return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor berhasil diupdate.');
        } catch (\Exception $e) {
            \Log::error('Sponsor update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal update: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Sponsor $sponsor)
    {
        if ($sponsor->image) {
            Storage::disk('public')->delete($sponsor->image);
        }
        $sponsor->delete();
        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor berhasil dihapus.');
    }
}
