<?php

namespace App\Models;

use App\Enums\ContactType;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use LogActivity;
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContactType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ContactAttribute::class);
    }

    public function mailRecipientLogs(): HasMany
    {
        return $this->hasMany(MailRecipientLog::class);
    }
}
