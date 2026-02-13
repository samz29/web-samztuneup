<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartController extends Controller
{
    public function index()
    {
        $parts = Part::ordered()->get();
        return view('admin.parts.index', compact('parts'));
    }

    public function create()
    {
        return view('admin.parts.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
            ]);
            $data['is_active'] = $request->boolean('is_active');
            $data['sort_order'] = (int) $request->input('sort_order', 0);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                \Log::info('File info: ' . $file->getClientOriginalName() . ' - ' . $file->getSize());
                $data['image'] = $request->file('image')->store('parts', 'public');
                \Log::info('Stored path: ' . $data['image']);
            }
            Part::create($data);
            return redirect()->route('admin.parts.index')->with('success', 'Part berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Part upload error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal upload: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Part $part)
    {
        return view('admin.parts.edit', compact('part'));
    }

    public function update(Request $request, Part $part)
    {
        try {
            $data = $request->validate([
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
            ]);
            $data['is_active'] = $request->boolean('is_active');
            $data['sort_order'] = (int) $request->input('sort_order', 0);
            if ($request->hasFile('image')) {
                if ($part->image) {
                    Storage::disk('public')->delete($part->image);
                }
                $file = $request->file('image');
                \Log::info('File info: ' . $file->getClientOriginalName() . ' - ' . $file->getSize());
                $data['image'] = $request->file('image')->store('parts', 'public');
                \Log::info('Stored path: ' . $data['image']);
            }
            $part->update($data);
            return redirect()->route('admin.parts.index')->with('success', 'Part berhasil diupdate.');
        } catch (\Exception $e) {
            \Log::error('Part update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal update: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Part $part)
    {
        if ($part->image) {
            Storage::disk('public')->delete($part->image);
        }
        $part->delete();
        return redirect()->route('admin.parts.index')->with('success', 'Part berhasil dihapus.');
    }
}
