<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebMenu;
use Illuminate\Http\Request;

class WebMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = WebMenu::with('parent')->ordered()->get();
        return view('admin.web-menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableParents = WebMenu::active()->get();
        return view('admin.web-menus.create', compact('availableParents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:web_menus,id',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'target' => 'required|in:_self,_blank',
            'location' => 'required|in:header,footer',
        ]);

        WebMenu::create($request->all());

        return redirect()->route('admin.web-menus.index')
            ->with('success', 'Web menu created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WebMenu $webMenu)
    {
        return view('admin.web-menus.show', compact('webMenu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WebMenu $webMenu)
    {
        $availableParents = WebMenu::where('id', '!=', $webMenu->id)->active()->get();
        return view('admin.web-menus.edit', compact('webMenu', 'availableParents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WebMenu $webMenu)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:web_menus,id',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'target' => 'required|in:_self,_blank',
            'location' => 'required|in:header,footer',
        ]);

        $webMenu->update($request->all());

        return redirect()->route('admin.web-menus.index')
            ->with('success', 'Web menu updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WebMenu $webMenu)
    {
        $webMenu->delete();

        return redirect()->route('admin.web-menus.index')
            ->with('success', 'Web menu deleted successfully.');
    }

    /**
     * Update menu order
     */
    public function updateOrder(Request $request)
    {
        $menus = $request->input('menus', []);

        foreach ($menus as $index => $menuId) {
            WebMenu::where('id', $menuId)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
