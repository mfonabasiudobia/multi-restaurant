<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'payment_id',
        'file_path',
    ];

    protected $nullable = [
        'order_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function getRelatedDocument()
    {
        return $this->order_id ? $this->order : $this->payment;
    }

    public function getProofType()
    {
        return $this->order_id ? 'order' : 'package';
    }
}