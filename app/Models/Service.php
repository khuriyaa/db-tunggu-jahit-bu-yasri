<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'service_type',
        'base_price',
        'estimated_days',
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}