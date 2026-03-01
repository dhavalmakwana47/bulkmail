<?php

namespace App\Console\Commands;

use App\Jobs\SendBulkMailJob;
use App\Models\MailConfiguration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\MailService;

class SendScheduledMails extends Command
{
    protected $signature = 'mail:send-scheduled';
    protected $description = 'Send scheduled bulk mails';

    public function handle(MailService $mailService)
    {
        $scheduledMails = MailConfiguration::where('send_type', 'SCHEDULED')
            ->where('scheduled_at', '<=', now())
            ->where('status', 0)
            ->get();

        foreach ($scheduledMails as $mailConfig) {
            $mailConfig->update(['status' => 1]);
        
        if (!$mailConfig) {
            Log::error('Mail configuration not found: ' . $mailConfig->id);
            return;
        }

        $result = $mailService->sendBulkMail($mailConfig);
        
        Log::info('Bulk mail sent', [
            'mail_config_id' => $mailConfig->id,
            'total_sent' => $result['total_sent'],
            'total_failed' => $result['total_failed'],
            'total_contacts' => $result['total_contacts'],
        ]);
        }

        $this->info('Total scheduled mails dispatched: ' . $scheduledMails->count());
    }
}
