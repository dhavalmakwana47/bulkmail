<?php

namespace App\Models;

use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesVerifiedEmail extends Model
{
    use HasFactory, LogActivity;

    protected $fillable = [
        'email',
        'active_status',
    ];

    public function scopeActive($query)
    {
        return $query->where('active_status', 'Y');
    }
}
