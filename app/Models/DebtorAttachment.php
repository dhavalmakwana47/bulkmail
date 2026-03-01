<?php

namespace App\Models;

use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtorAttachment extends Model
{
    use HasFactory, LogActivity;

    protected $fillable = ['user_id', 'name', 'file_path', 'file_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
