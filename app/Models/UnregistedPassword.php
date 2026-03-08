<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
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

        try {
            Crypt::decryptString($value);
            $this->attributes['password'] = $value;
            return;
        } catch (DecryptException $e) {
        }

        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getPasswordAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            return $value;
        }
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
