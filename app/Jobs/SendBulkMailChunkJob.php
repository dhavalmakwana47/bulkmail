<?php

namespace App\Jobs;

use App\Enums\ActionType;
use App\Models\MailConfiguration;
use App\Services\MailService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkMailChunkJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    public $tries = 3;

    public $mailConfigId;
    public $contactIds;
    public $isLastChunk;

    public function __construct($mailConfigId, $contactIds, $isLastChunk = false)
    {
        $this->mailConfigId = $mailConfigId;
        $this->contactIds = $contactIds;
        $this->isLastChunk = $isLastChunk;
    }

    public function handle(MailService $mailService)
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $mailConfig = MailConfiguration::find($this->mailConfigId);
        
        if (!$mailConfig) {
            Log::error('Mail configuration not found: ' . $this->mailConfigId);
            return;
        }

        $result = $mailService->sendBulkMailChunk($mailConfig, $this->contactIds);
        
        Log::info('Bulk mail chunk sent', [
            'mail_config_id' => $this->mailConfigId,
            'chunk_size' => count($this->contactIds),
            'total_sent' => $result['total_sent'],
            'total_failed' => $result['total_failed'],
        ]);

        if ($this->isLastChunk ) {
            $this->finalizeBulkMail($mailConfig);
        }
    }

    private function finalizeBulkMail(MailConfiguration $mailConfig)
    {
        $totalSent = $mailConfig->recipientLogs()->where('status', 'SENT')->count();
        $totalFailed = $mailConfig->recipientLogs()->where('status', 'FAILED')->count();

        $mailConfig->update(['status' => 2]);

        activity_log('MailConfiguration', ActionType::SEND, $mailConfig, null, [
            'total_sent' => $totalSent,
            'total_failed' => $totalFailed
        ]);

        Log::info('Bulk mail completed', [
            'mail_config_id' => $mailConfig->id,
            'total_sent' => $totalSent,
            'total_failed' => $totalFailed,
        ]);
    }
}
