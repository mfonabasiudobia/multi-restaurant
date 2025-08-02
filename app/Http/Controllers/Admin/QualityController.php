<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quality;
use App\Repositories\QualityRepository;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    protected $repository;

    public function __construct(QualityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $qualities = $this->repository->getAllQualities();

        \Log::warning('View Quality');

        return view('admin.quality.index', compact('qualities'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $this->repository->storeQuality($request->all());

        \Log::warning('Save Quality');
        return redirect()->route('admin.quality.index')->with('success', 'Quality created successfully.');
    }

    public function edit(Quality $quality)
    {
        return response()->json($quality);
    }

    public function update(Request $request, Quality $quality)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $this->repository->updateQuality($quality, $request->all());

        \Log::warning('Update Quality');
        return redirect()->route('admin.quality.index')->with('success', 'Quality updated successfully.');
    }

    public function destroy(Quality $quality)
    {
        $quality->delete();
        \Log::warning('Delete Quality');
        return redirect()->route('admin.quality.index')->with('success', 'Quality deleted successfully.');
    }

    public function toggle(Quality $quality)
    {
        $this->repository->toggleStatus($quality);

        \Log::warning('Toggle Quality');
        return redirect()->route('admin.quality.index')->with('success', 'Quality status updated successfully.');
    }
}
