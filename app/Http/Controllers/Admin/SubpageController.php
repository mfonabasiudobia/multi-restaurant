<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subpage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SubpageController extends Controller
{
    public function index()
    {
        $subpages = Subpage::all();
        \Log::info($subpages);
        return view('admin.subpages.index', compact('subpages'));
    }
    public function index1()
    {
        return response()->json(Subpage::all());
    }

    public function create()
    {
        return view('admin.subpages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:subpages,title',
            'content' => 'required',
            'section' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $slug = Str::slug($validated['title']);
            if (Subpage::where('slug', $slug)->exists()) {
                $slug .= '-' . time(); // Ensure unique slug
            }
            \Log::info($request->all());

            Subpage::create([
                'title' => $validated['title'],
                'slug' => $slug,
                'content' => $validated['content'],
                'section' => $validated['section'],
            ]);

            DB::commit();
            return redirect()->route('admin.subpages.index')->with('success', 'Subpage created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create subpage: ' . $e->getMessage());
        }
    }

    public function show($slug)
    {
        $subpage = Subpage::where('slug', $slug)->firstOrFail();
        return view('admin.subpages.show', compact('subpage'));
    }

    public function edit($id)
    {
        $subpage = Subpage::findOrFail($id);
        return view('admin.subpages.edit', compact('subpage'));
    }

    public function update(Request $request, $id)
    {
        $subpage = Subpage::findOrFail($id);
        \Log::info($request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:subpages,title,' . $id,
            'content' => 'required',
            'section' => 'required|string|in:about,information',
        ]);
        \Log::info($validated);

        try {
            DB::beginTransaction();

            $slug = Str::slug($validated['title']);
            if (Subpage::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug .= '-' . time(); // Ensure unique slug
            }

            $subpage->update([
                'title' => $validated['title'],
                'slug' => $slug,
                'content' => $validated['content'],
                'section' => $validated['section'],
            ]);

            DB::commit();
            return redirect()->route('admin.subpages.index')->with('success', 'Subpage updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update subpage: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Subpage::findOrFail($id)->delete();
            return redirect()->route('admin.subpages.index')->with('success', 'Subpage deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete subpage: ' . $e->getMessage());
        }
    }
}