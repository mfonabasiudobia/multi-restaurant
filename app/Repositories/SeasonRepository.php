<?php

namespace App\Repositories;

use App\Models\Season;

class SeasonRepository
{
    public function getAllSeasons($perPage = 10)
    {
        return Season::paginate($perPage);
    }

    public function storeSeason($data)
    {
        return Season::create($data);
    }

    public function updateSeason($season, $data)
    {
        return $season->update($data);
    }

    public function toggleStatus($season)
    {
        $season->is_active = !$season->is_active;
        $season->save();
        return $season;
    }
}