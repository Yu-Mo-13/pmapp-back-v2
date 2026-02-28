<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Password extends Model
{
    use HasFactory;

    protected $fillable = [
        'password',
        'application_id',
        'account_id',
    ];

    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['password'] = $value;
            return;
        }

        if (Str::startsWith($value, '$2y$')) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = Hash::make($value);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
