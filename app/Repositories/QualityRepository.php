<?php
namespace App\Repositories;

use App\Models\Quality;

class QualityRepository
{
    public function getAllQualities()
    {
        return Quality::paginate(10);
    }

    public function storeQuality($data)
    {
        return Quality::create($data);
    }

    public function updateQuality(Quality $quality, $data)
    {
        return $quality->update($data);
    }

    public function toggleStatus(Quality $quality)
    {
        $quality->is_active = !$quality->is_active;
        return $quality->save();
    }
}