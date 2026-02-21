<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'admin_visible',
        'web_user_visible',
        'mobile_user_visible',
        'sort_order',
    ];
}
