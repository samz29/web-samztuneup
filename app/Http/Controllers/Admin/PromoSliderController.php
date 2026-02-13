<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoSliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promoSliders = PromoSlider::ordered()->paginate(10);
        return view('admin.promo-sliders.index', compact('promoSliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.promo-sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('promo-sliders', 'public');
        }

        PromoSlider::create($data);

        return redirect()->route('admin.promo-sliders.index')
            ->with('success', 'Promo slider created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PromoSlider $promoSlider)
    {
        return view('admin.promo-sliders.show', compact('promoSlider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PromoSlider $promoSlider)
    {
        return view('admin.promo-sliders.edit', compact('promoSlider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PromoSlider $promoSlider)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($promoSlider->image) {
                Storage::disk('public')->delete($promoSlider->image);
            }
            $data['image'] = $request->file('image')->store('promo-sliders', 'public');
        }

        $promoSlider->update($data);

        return redirect()->route('admin.promo-sliders.index')
            ->with('success', 'Promo slider updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromoSlider $promoSlider)
    {
        // Delete image file
        if ($promoSlider->image) {
            Storage::disk('public')->delete($promoSlider->image);
        }

        $promoSlider->delete();

        return redirect()->route('admin.promo-sliders.index')
            ->with('success', 'Promo slider deleted successfully.');
    }
}
