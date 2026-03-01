<?php

namespace App\Services;

use App\Enums\ActionType;
use App\Jobs\SendBulkMailJob;
use App\Models\Contact;
use App\Models\DebtorAttachment;
use App\Models\MailConfiguration;
use App\Models\MailRecipientLog;
use App\Models\SesConnection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class MailService
{
    public function sendBulkMail(MailConfiguration $mailConfig)
    {
        $sesConnection = SesConnection::active()->inRandomOrder()->first();
        
        if (!$sesConnection) {
            Log::error('No active SES connection found');
            return ['total_sent' => 0, 'total_failed' => 0, 'total_contacts' => 0];
        }

        $this->configureSes($sesConnection);
        
        $contacts = Contact::where('user_id', $mailConfig->user_id)->get();
        $attachments = $mailConfig->configurationAttachments()->with('debtorAttachment')->get();
        
        $totalSent = 0;
        $totalFailed = 0;

        foreach ($contacts as $contact) {
            try {
                $body = $this->replaceTagsInBody($mailConfig->body, $contact, $attachments);
                
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

        $mailConfig->update(['status' => 2]);

        activity_log('MailConfiguration', ActionType::SEND, $mailConfig, null, ['total_sent' => $totalSent, 'total_failed' => $totalFailed]);

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
        $html .= '<thead><tr><th>Attachment Name</th><th>Download</th></tr></thead>';
        $html .= '<tbody>';
        
        foreach ($attachments as $attachment) {
            $debtorAttachment = $attachment->debtorAttachment;
            $downloadUrl = route('debtor-attachments.download', Crypt::encryptString($debtorAttachment->id));
            $html .= '<tr>';
            $html .= '<td>' . $debtorAttachment->name . '</td>';
            $html .= '<td><a href="' . $downloadUrl . '">Download</a></td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        
        return $html;
    }

    public function resendMail(MailRecipientLog $log)
    {
        $sesConnection = SesConnection::active()->inRandomOrder()->first();
        
        if (!$sesConnection) {
            throw new \Exception('No active SES connection found');
        }

        $this->configureSes($sesConnection);
        
        $mailConfig = $log->mailConfiguration;
        $contact = $log->contact;
        $attachments = $mailConfig->configurationAttachments()->with('debtorAttachment')->get();
        
        $body = $this->replaceTagsInBody($mailConfig->body, $contact, $attachments);
        
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
