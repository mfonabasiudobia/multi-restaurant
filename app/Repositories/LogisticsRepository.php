<?php

namespace App\Repositories;

use Arafat\LaravelRepository\Repository;
use Illuminate\Http\Request;
use App\Models\Logistics;

class LogisticsRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Logistics::class;
    }

    public static function storeByRequest(Request $request)
    {
       self::create([
            //
       ]);
    }

    public static function create(array $data)
    {
        return self::model()::create($data);
    }

    public static function update($logistics, array $data)
    {
        return $logistics->update($data);
    }

    public static function delete($logistics)
    {
        return $logistics->delete();
    }

    public static function findByBagNumber($bagNumber)
    {
        return self::model()::where('bag_number', $bagNumber)->first();
    }
}
