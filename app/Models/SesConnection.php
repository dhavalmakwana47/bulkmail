<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'ses_name',
        'username',
        'password',
        'region',
        'hostname',
        'port',
        'active',
        'from_email',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 'Y');
    }
}
