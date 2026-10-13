<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'qr_image_path',
    ];
}
