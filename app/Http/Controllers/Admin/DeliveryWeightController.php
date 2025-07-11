<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryWeight;
use App\Models\Unit;
use App\Models\GeneraleSetting;
use Illuminate\Http\Request;

class DeliveryWeightController extends Controller
{
    public function index()
    {
        $weights = DeliveryWeight::latest()->get();
        $weightUnit = Unit::where('is_weight', true)
            ->whereNull('shop_id')
            ->first() ?? Unit::create([
                'name' => 'KG',
                'is_active' => true,
                'is_weight' => true,
                'shop_id' => null
            ]);
        $generaleSetting = GeneraleSetting::first();
        return view('admin.delivery-weight.index', compact('weights', 'weightUnit', 'generaleSetting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'min_weight' => 'required|numeric|min:0',
            'max_weight' => 'required|numeric|gt:min_weight',
            'price' => 'required|numeric|min:0'
        ]);

        DeliveryWeight::create($request->all());
        return redirect()->back()->with('success', 'Weight range added successfully');
    }

    public function update(Request $request, DeliveryWeight $deliveryWeight)
    {
        $request->validate([
            'min_weight' => 'required|numeric|min:0',
            'max_weight' => 'required|numeric|gt:min_weight',
            'price' => 'required|numeric|min:0'
        ]);

        $deliveryWeight->update($request->all());
        return redirect()->back()->with('success', 'Weight range updated successfully');
    }

    public function destroy(DeliveryWeight $deliveryWeight)
    {
        $deliveryWeight->delete();
        return redirect()->back()->with('success', 'Weight range deleted successfully');
    }
} 