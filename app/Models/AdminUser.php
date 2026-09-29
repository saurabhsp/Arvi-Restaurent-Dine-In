<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $fillable = ['name', 'username', 'password', 'mobile'];
    protected $hidden = ['password'];
}
