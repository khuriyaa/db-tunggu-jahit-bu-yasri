<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    public const PAYMENT_METHODS = ['GoPay', 'Tunai'];
    public const CUSTOMER_PAYMENT_METHODS = ['GoPay', 'Tunai'];

    protected $fillable = [
        'order_code',
        'queue_number',
        'customer_id',
        'user_id',
        'order_date',
        'estimated_completion_date',
        'current_status',
        'payment_method',
        'payment_reported_at',
        'payment_verified_at',
        'total_items',
        'total_price',
    ];

    protected $casts = [
        'order_date' => 'date',
        'estimated_completion_date' => 'date',
        'total_price' => 'decimal:2',
        'payment_reported_at' => 'datetime',
        'payment_verified_at' => 'datetime',
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