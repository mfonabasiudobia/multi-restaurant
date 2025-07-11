<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PackagePayment;
use App\Models\ShopPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->paginate(20);
        \Log::info($packages);
        return view('admin.packages.index', compact('packages'));
    }
        
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_limit' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array'
        ]);

        $data = $request->all();
        \Log::info($data);
        if (empty($data['features'])) {
            $data['features'] = [];
        }

        Package::create($data);
        return back()->withSuccess(__('Package created successfully'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_limit' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array'
        ]);

        $data = $request->all();
        if (empty($data['features'])) {
            $data['features'] = [];
        }

        $package->update($data);
        return back()->withSuccess(__('Package updated successfully'));
    }

    public function toggle(Package $package)
    {
        $package->update(['is_active' => !$package->is_active]);
        return back()->withSuccess(__('Package status updated successfully'));
    }

    public function edit(Package $package)
    {
        return response()->json($package);
    }

    public function show(Package $package)
    {
        return response()->json(['package' => $package]);
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return back()->withSuccess(__('Package deleted successfully'));
    }

    /**
     * Show package payments
     */
    public function payments()
    {
        $packagePayments = PackagePayment::with([
            'payment', 
            'payment.paymentProof',
            'shop', 
            'package'
        ])->latest()->paginate(20);
        \Log::info($packagePayments);

        return view('admin.packages.payments', compact('packagePayments'));
    }

    /**
     * Approve package payment
     */
    public function approvePayment($id)
    {
        try {
            DB::beginTransaction();
            
            $packagePayment = PackagePayment::findOrFail($id);
            \Log::info('Package Payment:', ['data' => $packagePayment]);

            // Update package payment status
            $packagePayment->update([
                'status' => 'approved',
                'expires_at' => now()->addDays($packagePayment->package->duration_days)
            ]);

            // Update shop package
            ShopPackage::updateOrCreate(
                ['shop_id' => $packagePayment->shop_id],
                [
                    'package_id' => $packagePayment->package_id,
                    'product_limit' => $packagePayment->package->product_limit,
                    'package_price' => $packagePayment->amount,
                    'is_paid' => true,
                    'expires_at' => $packagePayment->expires_at
                ]
            );

            // Update payment status
            if ($packagePayment->payment) {
                $packagePayment->payment->update(['status' => 'approved']);
            }

            DB::commit();
            return back()->withSuccess('Payment approved successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approval Error:', ['error' => $e->getMessage()]);
            return back()->withError('Payment approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Reject package payment
     */
    public function rejectPayment($id)
    {
        try {
            DB::beginTransaction();
            
            $packagePayment = PackagePayment::findOrFail($id);
            
            // Update package payment status
            $packagePayment->update(['status' => 'rejected']);

            // Update payment status
            if ($packagePayment->payment) {
                $packagePayment->payment->update(['status' => 'rejected']);
            }

            DB::commit();
            return back()->withSuccess('Payment rejected successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Rejection Error:', ['error' => $e->getMessage()]);
            return back()->withError('Payment rejection failed: ' . $e->getMessage());
        }
    }
} 