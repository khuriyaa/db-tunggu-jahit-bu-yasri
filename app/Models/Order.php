<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'queue_number',
        'customer_id',
        'user_id',
        'order_date',
        'estimated_completion_date',
        'current_status',
        'total_items',
        'total_price',
    ];

    protected $casts = [
        'order_date' => 'date',
        'estimated_completion_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class);
    }
}