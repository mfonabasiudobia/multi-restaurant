<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quality extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    /**
     * Scope a query to only include active qualities.
     */
    public function scopeIsActive($query)

    {

        return $query->where('is_active', true);

    }

}