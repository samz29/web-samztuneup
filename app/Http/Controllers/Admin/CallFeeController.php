<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallFee;
use Illuminate\Http\Request;

class CallFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $callFees = CallFee::orderBy('min_distance')->paginate(15);
        return view('admin.call-fees.index', compact('callFees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.call-fees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'min_distance' => 'required|integer|min:0',
            'max_distance' => 'nullable|integer|min:0|gte:min_distance',
            'fee' => 'required|numeric|min:0',
            'active' => 'boolean',
        ]);

        CallFee::create($request->all());

        return redirect()->route('admin.call-fees.index')
            ->with('success', 'Call fee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CallFee $callFee)
    {
        return view('admin.call-fees.show', compact('callFee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CallFee $callFee)
    {
        return view('admin.call-fees.edit', compact('callFee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CallFee $callFee)
    {
        $request->validate([
            'min_distance' => 'required|integer|min:0',
            'max_distance' => 'nullable|integer|min:0|gte:min_distance',
            'fee' => 'required|numeric|min:0',
            'active' => 'boolean',
        ]);

        $callFee->update($request->all());

        return redirect()->route('admin.call-fees.index')
            ->with('success', 'Call fee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CallFee $callFee)
    {
        $callFee->delete();

        return redirect()->route('admin.call-fees.index')
            ->with('success', 'Call fee deleted successfully.');
    }
}
