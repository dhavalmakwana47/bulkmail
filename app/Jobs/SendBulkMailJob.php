<?php

namespace App\Jobs;

use App\Models\MailConfiguration;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mailConfigId;

    public function __construct($mailConfigId)
    {
        $this->mailConfigId = $mailConfigId;
    }

    public function handle(MailService $mailService)
    {
        $mailConfig = MailConfiguration::find($this->mailConfigId);
        
        if (!$mailConfig) {
            Log::error('Mail configuration not found: ' . $this->mailConfigId);
            return;
        }

        $result = $mailService->sendBulkMail($mailConfig);
        
        Log::info('Bulk mail sent', [
            'mail_config_id' => $this->mailConfigId,
            'total_sent' => $result['total_sent'],
            'total_failed' => $result['total_failed'],
            'total_contacts' => $result['total_contacts'],
        ]);
    }
}
