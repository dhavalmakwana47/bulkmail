<?php

namespace App\Models;

use App\Enums\SendType;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailConfiguration extends Model
{
    use LogActivity;
    protected $fillable = [
        'user_id',
        'from_name',
        'reply_email',
        'subject',
        'body',
        'attachments',
        'send_type',
        'scheduled_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'scheduled_at' => 'datetime',
            'send_type' => SendType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipientLogs(): HasMany
    {
        return $this->hasMany(MailRecipientLog::class);
    }

    public function configurationAttachments(): HasMany
    {
        return $this->hasMany(MailConfigurationAttachment::class);
    }
}
