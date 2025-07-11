<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Logistics;
use Illuminate\Http\Request;
use App\Repositories\LogisticsRepository;

class LogisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Logistics::query();

        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('bag_number', 'like', "%{$request->search}%")
                  ->orWhere('location', 'like', "%{$request->search}%")
                  ->orWhere('row', 'like', "%{$request->search}%");
            });
        }

        // Filter by location
        if ($request->location) {
            $query->where('location', $request->location);
        }

        // Filter by row
        if ($request->row) {
            $query->where('row', $request->row);
        }

        $logistics = $query->latest()->paginate(20);

        return view('admin.logistics.index', compact('logistics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.logistics.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'article_name' => 'required|string|max:255',
            'bag_number' => 'required|string|unique:logistics,bag_number',
            'location' => 'required|string|max:255',
            'row' => 'required|integer'
        ]);

        LogisticsRepository::create($request->all());

        return redirect()->route('admin.logistics.index')
            ->withSuccess(__('Logistics entry created successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Logistics $logistics)
    {
        return view('admin.logistics.show', compact('logistics'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Logistics $logistics)
    {
        return view('admin.logistics.edit', compact('logistics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Logistics $logistics)
    {
        $request->validate([
            'article_name' => 'required|string|max:255',
            'bag_number' => 'required|string|unique:logistics,bag_number,' . $logistics->id,
            'location' => 'required|string|max:255',
            'row' => 'required|integer'
        ]);

        LogisticsRepository::update($logistics, $request->all());

        return redirect()->route('admin.logistics.index')
            ->withSuccess(__('Logistics entry updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logistics $logistics)
    {
        $logistics->delete();

        return redirect()->route('admin.logistics.index')
            ->withSuccess(__('Logistics entry deleted successfully'));
    }
}
