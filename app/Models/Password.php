<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

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
