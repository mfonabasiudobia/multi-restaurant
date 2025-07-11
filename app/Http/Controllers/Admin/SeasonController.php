<?php
namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Season;
use App\Repositories\SeasonRepository;

class SeasonController extends Controller
{
    public function index(SeasonRepository $repository)
    {
        $seasons = $repository->getAllSeasons();
        return view('admin.season.index', compact('seasons'));
    }
    
    public function store(Request $request, SeasonRepository $repository)
    {
        \Log::info($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $repository->storeSeason($request->except('_token'));
        return redirect()->back()->with('success', 'Season created successfully.');
    }
    
    public function update(Request $request, Season $season, SeasonRepository $repository)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $repository->updateSeason($season, $request->except('_token'));
        return redirect()->back()->with('success', 'Season updated successfully.');
    }
    
    public function toggle(Season $season, SeasonRepository $repository)
    {
        $repository->toggleStatus($season);
        return redirect()->back()->with('success', 'Season status updated successfully.');
    }
}