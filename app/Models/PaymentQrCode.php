<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentQrCode extends Model
{
    protected $fillable = ['image_path', 'upi_id', 'payee_name'];
}
