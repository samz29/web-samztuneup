<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::ordered()->get();
        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('admin.galleries.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
                'caption' => 'nullable|string|max:100',
            ]);
            $data['is_active'] = $request->boolean('is_active');
            $data['sort_order'] = (int) $request->input('sort_order', 0);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                \Log::info('File info: ' . $file->getClientOriginalName() . ' - ' . $file->getSize());
                $data['image'] = $request->file('image')->store('galleries', 'public');
                \Log::info('Stored path: ' . $data['image']);
            }
            Gallery::create($data);
            return redirect()->route('admin.galleries.index')->with('success', 'Foto galeri berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Gallery upload error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal upload: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        try {
            $data = $request->validate([
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'caption' => 'nullable|string|max:100',
            ]);
            $data['is_active'] = $request->boolean('is_active');
            $data['sort_order'] = (int) $request->input('sort_order', 0);
            if ($request->hasFile('image')) {
                if ($gallery->image) {
                    Storage::disk('public')->delete($gallery->image);
                }
                $data['image'] = $request->file('image')->store('galleries', 'public');
            }
            $gallery->update($data);
            return redirect()->route('admin.galleries.index')->with('success', 'Foto galeri berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }
        $gallery->delete();
        return redirect()->route('admin.galleries.index')->with('success', 'Foto galeri berhasil dihapus.');
    }
}
