<?php

namespace App\Models;

use App\Enums\ContactType;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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

    protected $attributes = [
        'type' => 'SUBSCRIBED',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContactType::class,
        ];
    }

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('type', ContactType::SUBSCRIBED);
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

    public function isSubscribed(): bool
    {
        return $this->type === ContactType::SUBSCRIBED;
    }

    public function unsubscribe(): bool
    {
        $updated = $this->update(['type' => ContactType::UNSUBSCRIBED]);
        
        if ($updated) {
            UnsubscribeLog::create([
                'contact_id' => $this->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'unsubscribed_at' => now(),
            ]);
        }
        
        return $updated;
    }
}
