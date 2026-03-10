<?php

namespace App\Services;

use App\Enums\ActionType;
use App\Jobs\SendBulkMailJob;
use App\Jobs\SendBulkMailChunkJob;
use App\Models\Contact;
use App\Models\DebtorAttachment;
use App\Models\MailConfiguration;
use App\Models\MailRecipientLog;
use App\Models\SesConnection;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Bus;

class MailService
{
    public function sendBulkMail(MailConfiguration $mailConfig)
    {
        $contacts = Contact::where('user_id', $mailConfig->user_id)
            ->subscribed()
            ->pluck('id');

        $chunkSize = 50;
        $chunks = $contacts->chunk($chunkSize);
        $jobs = [];

        foreach ($chunks as $index => $chunk) {
            $isLastChunk = ($index === $chunks->count() - 1);
            $jobs[] = new SendBulkMailChunkJob($mailConfig->id, $chunk->toArray(), $isLastChunk);
        }

        Bus::batch($jobs)
            ->name('Bulk Mail: ' . $mailConfig->subject)
            ->dispatch();

        Log::info('Bulk mail jobs dispatched', [
            'mail_config_id' => $mailConfig->id,
            'total_contacts' => $contacts->count(),
            'total_chunks' => $chunks->count(),
        ]);

        return [
            'total_sent' => 0,
            'total_failed' => 0,
            'total_contacts' => $contacts->count(),
        ];
    }

    public function sendBulkMailChunk(MailConfiguration $mailConfig, array $contactIds)
    {
        $sesConnection = SesConnection::active()->inRandomOrder()->first();

        if (!$sesConnection) {
            Log::error('No active SES connection found');
            return ['total_sent' => 0, 'total_failed' => 0, 'total_contacts' => 0];
        }

        $this->configureSes($sesConnection);

        $contacts = Contact::whereIn('id', $contactIds)->get();
        $attachments = $mailConfig->configurationAttachments()->with('debtorAttachment')->get();

        $totalSent = 0;
        $totalFailed = 0;

        foreach ($contacts as $contact) {
            try {
                $alreadySent = MailRecipientLog::where('mail_configuration_id', $mailConfig->id)
                    ->where('contact_id', $contact->id)
                    ->exists();
                if ($alreadySent) {
                    continue;
                }
                $body = $this->replaceTagsInBody($mailConfig->body, $contact, $attachments);
                $body = $this->appendUnsubscribeFooter($body, $contact);

                $sentMessage = Mail::html($body, function ($message) use ($mailConfig, $contact, $sesConnection) {
                    $message->from($sesConnection->from_email, $mailConfig->from_name)
                        ->replyTo($mailConfig->reply_email)
                        ->to($contact->email, $contact->name)
                        ->subject($mailConfig->subject);
                });

                $messageId = $sentMessage ? $sentMessage->getMessageId() : null;

                MailRecipientLog::create([
                    'mail_configuration_id' => $mailConfig->id,
                    'contact_id' => $contact->id,
                    'status' => 'SENT',
                    'sent_at' => now(),
                    'delivered_at' => now(),
                    'message_id' => $messageId,
                ]);

                $totalSent++;
            } catch (\Exception $e) {
                MailRecipientLog::create([
                    'mail_configuration_id' => $mailConfig->id,
                    'contact_id' => $contact->id,
                    'status' => 'FAILED',
                    'error_message' => $e->getMessage(),
                    'bounce_reason' => $e->getMessage(),
                ]);

                $totalFailed++;
                Log::error('Mail send failed: ' . $e->getMessage());
            }
        }

        return [
            'total_sent' => $totalSent,
            'total_failed' => $totalFailed,
            'total_contacts' => $contacts->count(),
        ];
    }

    private function configureSes(SesConnection $sesConnection)
    {
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $sesConnection->hostname,
            'port' => $sesConnection->port,
            'encryption' => $sesConnection->port == 587 ? 'tls' : 'ssl',
            'username' => $sesConnection->username,
            'password' => $sesConnection->password,
        ]);

        Config::set('mail.default', 'smtp');
    }

    private function replaceTagsInBody($body, Contact $contact, $attachments)
    {
        $body = str_replace('{{name}}', $contact->name, $body);
        $body = str_replace('{{email}}', $contact->email, $body);
        $body = str_replace('{{phone}}', $contact->phone ?? '', $body);

        $attributes = $contact->attributes->pluck('value', 'key')->toArray();
        $body = str_replace('{{attribute_1}}', $attributes['attribute_1'] ?? '', $body);
        $body = str_replace('{{attribute_2}}', $attributes['attribute_2'] ?? '', $body);
        $body = str_replace('{{attribute_3}}', $attributes['attribute_3'] ?? '', $body);
        $body = str_replace('{{attribute_4}}', $attributes['attribute_4'] ?? '', $body);

        if (strpos($body, '{{attachment_list}}') !== false) {
            $attachmentTable = $this->generateAttachmentTable($attachments);
            $body = str_replace('{{attachment_list}}', $attachmentTable, $body);
        }

        return $body;
    }

    private function generateAttachmentTable($attachments)
    {
        if ($attachments->isEmpty()) {
            return '';
        }

        $html = '<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead><tr><th>Attachment List</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($attachments as $attachment) {
            $debtorAttachment = $attachment->debtorAttachment;
            $downloadUrl = route('debtor-attachments.download', Crypt::encryptString($debtorAttachment->id));
            $html .= '<tr>';
            $html .= '<td><a href="' . $downloadUrl . '">' . $debtorAttachment->name . '</a></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function appendUnsubscribeFooter(string $body, Contact $contact): string
    {
        $subscriptionService = app(SubscriptionService::class);
        $unsubscribeUrl = $subscriptionService->generateUnsubscribeUrl($contact);

        $footer = view('emails.partials.unsubscribe-footer', [
            'unsubscribeUrl' => $unsubscribeUrl
        ])->render();

        return $body . $footer;
    }

    public function resendMail(MailRecipientLog $log)
    {
        $sesConnection = SesConnection::active()->inRandomOrder()->first();

        if (!$sesConnection) {
            throw new \Exception('No active SES connection found');
        }

        $this->configureSes($sesConnection);

        $mailConfig = $log->mailConfiguration;
        $contact = $log->contact()->first();

        if (!$contact) {
            throw new \Exception('Contact not found');
        }

        $contactType = is_string($contact->type) ? $contact->type : $contact->type->value;

        if ($contactType !== 'SUBSCRIBED') {
            throw new \Exception('Cannot send email to unsubscribed contact');
        }

        $attachments = $mailConfig->configurationAttachments()->with('debtorAttachment')->get();

        $body = $this->replaceTagsInBody($mailConfig->body, $contact, $attachments);
        $body = $this->appendUnsubscribeFooter($body, $contact);

        $sentMessage = Mail::html($body, function ($message) use ($mailConfig, $contact, $sesConnection) {
            $message->from($sesConnection->from_email, $mailConfig->from_name)
                ->replyTo($mailConfig->reply_email)
                ->to($contact->email, $contact->name)
                ->subject($mailConfig->subject);
        });

        $messageId = $sentMessage ? $sentMessage->getMessageId() : null;

        $log->update([
            'status' => 'SENT',
            'sent_at' => now(),
            'delivered_at' => now(),
            'message_id' => $messageId,
            'error_message' => null,
            'bounce_reason' => null,
        ]);

        activity_log('MailRecipientLog', ActionType::RESEND, $log);
    }
}
