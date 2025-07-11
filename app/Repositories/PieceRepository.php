<?php
namespace App\Repositories;

use App\Models\Piece;

class PieceRepository extends Repository
{
    public static function model()
    {
        return Piece::class;    
    }
}