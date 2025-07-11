<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InnerAd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InnerAdController extends Controller
{
    public function index()
    {
        $ads = InnerAd::latest()->get();
        // No need to manually set image URL since it's handled by accessor in InnerAd model
        \Log::info($ads);
        return view('admin.inner-ads.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.inner-ads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
        ]);

        $path = null;
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('inner-ads', 's3');
        }

        InnerAd::create([
            'title' => $request->title,
            'image' => $path,
            'link' => $request->link,
            'status' => true
        ]);

        return redirect()->route('admin.inner-ads.index')
            ->with('success', 'Inner ad created successfully');
    }

    public function edit(InnerAd $innerAd)
    {
        return view('admin.inner-ads.edit', compact('innerAd'));
    }

    public function update(Request $request, InnerAd $innerAd)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
        ]);

        $data = [
            'title' => $request->title,
            'link' => $request->link,
        ];

        if ($request->hasFile('banner')) {
            if ($innerAd->image) {
                Storage::disk('s3')->delete($innerAd->image);
            }
            
            $image = $request->file('banner');
            $data['image'] = $image->store('inner-ads', 's3');
        }

        $innerAd->update($data);

        return redirect()->route('admin.inner-ads.index')
            ->with('success', 'Inner ad updated successfully');
    }

    public function destroy(InnerAd $innerAd)
    {
        if ($innerAd->image) {
            Storage::disk('s3')->delete($innerAd->image);
        }
        
        $innerAd->delete();

        return redirect()->route('admin.inner-ads.index')
            ->with('success', 'Inner ad deleted successfully');
    }

    public function toggle(InnerAd $innerAd)
    {
        $innerAd->update([
            'status' => !$innerAd->status
        ]);

        return back()->with('success', 'Status updated successfully');
    }
} 