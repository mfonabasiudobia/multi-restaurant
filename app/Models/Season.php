<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;
    protected $fillable = [

        'name',

        'description',

        'is_active',

    ];
    public function scopeIsActive($query)

    {

        return $query->where('is_active', true);

    }


}

