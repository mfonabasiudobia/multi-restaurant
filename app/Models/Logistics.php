<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logistics extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'article_name',
        'bag_number',
        'location',
        'row'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'bag_number', 'bag_number');
    }
}
