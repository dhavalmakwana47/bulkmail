<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailConfigurationAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['mail_configuration_id', 'debtor_attachment_id'];

    public function mailConfiguration()
    {
        return $this->belongsTo(MailConfiguration::class);
    }

    public function debtorAttachment()
    {
        return $this->belongsTo(DebtorAttachment::class);
    }
}
