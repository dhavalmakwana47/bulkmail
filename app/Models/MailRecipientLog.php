<?php

namespace App\Models;

use App\Enums\MailStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailRecipientLog extends Model
{
    protected $fillable = [
        'mail_configuration_id',
        'contact_id',
        'status',
        'sent_at',
        'delivered_at',
        'error_message',
        'bounce_reason',
        'message_id',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'status' => MailStatus::class,
        ];
    }

    public function mailConfiguration(): BelongsTo
    {
        return $this->belongsTo(MailConfiguration::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
