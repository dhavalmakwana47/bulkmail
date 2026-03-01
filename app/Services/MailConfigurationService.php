<?php

namespace App\Services;

use App\Jobs\SendBulkMailJob;
use App\Models\MailConfiguration;

class MailConfigurationService
{
    public function syncAttachments(MailConfiguration $mailConfig, array $attachments): void
    {
        $mailConfig->configurationAttachments()->delete();
        
        foreach ($attachments as $attachmentId) {
            $mailConfig->configurationAttachments()->create([
                'debtor_attachment_id' => $attachmentId
            ]);
        }
    }

    public function dispatchIfNow(MailConfiguration $mailConfig): bool
    {
        $sendType = is_string($mailConfig->send_type) 
            ? $mailConfig->send_type 
            : $mailConfig->send_type->value;

        if ($sendType === 'NOW') {
            $mailConfig->update(['status' => 1]);
            SendBulkMailJob::dispatch($mailConfig->id);
            return true;
        }

        return false;
    }
}
