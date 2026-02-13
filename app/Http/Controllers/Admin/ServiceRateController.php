<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRate;
use Illuminate\Http\Request;

class ServiceRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serviceRates = ServiceRate::ordered()->paginate(10);
        return view('admin.service-rates.index', compact('serviceRates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.service-rates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'additional_fee' => 'numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        ServiceRate::create($request->all());

        return redirect()->route('admin.service-rates.index')
            ->with('success', 'Service rate created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceRate $serviceRate)
    {
        return view('admin.service-rates.show', compact('serviceRate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceRate $serviceRate)
    {
        return view('admin.service-rates.edit', compact('serviceRate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceRate $serviceRate)
    {
        // Prevent changing service_name for core booking services
        $coreServices = ['remap_ecu', 'custom_tune', 'dyno_tune', 'full_package'];
        if (in_array($serviceRate->service_name, $coreServices) && $request->service_name !== $serviceRate->service_name) {
            return redirect()->back()
                ->withErrors(['service_name' => 'Cannot change service name for core booking services.'])
                ->withInput();
        }

        $request->validate([
            'service_name' => 'required|string|max:255|unique:service_rates,service_name,' . $serviceRate->id,
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'additional_fee' => 'numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $serviceRate->update($request->all());

        return redirect()->route('admin.service-rates.index')
            ->with('success', 'Service rate updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceRate $serviceRate)
    {
        // Prevent deletion of core booking services
        $coreServices = ['remap_ecu', 'custom_tune', 'dyno_tune', 'full_package'];
        if (in_array($serviceRate->service_name, $coreServices)) {
            return redirect()->back()
                ->withErrors(['service_name' => 'Cannot delete core booking services.']);
        }

        $serviceRate->delete();

        return redirect()->route('admin.service-rates.index')
            ->with('success', 'Service rate deleted successfully.');
    }
}
