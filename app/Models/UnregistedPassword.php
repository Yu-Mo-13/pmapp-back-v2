<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UnregistedPassword extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'password',
        'application_id',
        'account_id',
    ];

    protected static function booted()
    {
        static::creating(function ($unregistedPassword) {
            if (empty($unregistedPassword->uuid)) {
                $unregistedPassword->uuid = (string) Str::uuid();
            }
        });
    }

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
