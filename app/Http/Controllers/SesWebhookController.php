<?php

namespace App\Http\Controllers;

use App\Models\MailRecipientLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SesWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $postBody = $request->getContent();
        Log::info('SES Webhook received', ['body' => $postBody]);

        $data = json_decode($postBody, true);

        if (!$data) {
            Log::error('Invalid SES webhook data');
            return response()->json(['error' => 'Invalid data'], 400);
        }

        // Handle SNS Subscription Confirmation
        if ($data['Type'] == 'SubscriptionConfirmation') {
            $subscribeUrl = $data['SubscribeURL'];
            file_get_contents($subscribeUrl);
            Log::info('SNS subscription confirmed', ['url' => $subscribeUrl]);
            return response()->json(['message' => 'confirmed']);
        }

        // Handle Notification
        if ($data['Type'] == 'Notification') {
            $message = json_decode($data['Message'], true);
            $messageId = $message['mail']['messageId'] ?? null;

            // Handle Bounce
            if ($message['notificationType'] == 'Bounce') {
                $this->handleBounce($message, $messageId);
            }

            // Handle Delivery
            if ($message['notificationType'] == 'Delivery') {
                $this->handleDelivery($message, $messageId);
            }

            // Handle Complaint
            if ($message['notificationType'] == 'Complaint') {
                $this->handleComplaint($message, $messageId);
            }
        }

        return response()->json(['success' => true]);
    }

    private function handleBounce($message, $messageId)
    {
        $bounce = $message['bounce'];
        $bounceType = $bounce['bounceType'];
        $bounceSubType = $bounce['bounceSubType'];

        foreach ($bounce['bouncedRecipients'] as $recipient) {
            $email = $recipient['emailAddress'];
            $diagnosticCode = $recipient['diagnosticCode'] ?? '';

            if (!empty($diagnosticCode)) {
                $reason = $diagnosticCode;
            } else {
                $reason = $bounceType != $bounceSubType 
                    ? "Bounce - {$bounceType} ({$bounceSubType})" 
                    : "Bounce - {$bounceType}";
            }

            $this->updateRecipientLog($messageId, $email, 'BOUNCED', $reason);

            Log::info('Bounce processed', [
                'message_id' => $messageId,
                'email' => $email,
                'type' => $bounceType,
                'reason' => $reason
            ]);
        }
    }

    private function handleDelivery($message, $messageId)
    {
        $recipients = $message['delivery']['recipients'] ?? [];
        $smtpResponse = $message['delivery']['smtpResponse'] ?? 'Delivered';

        foreach ($recipients as $email) {
            $this->updateRecipientLog($messageId, $email, 'DELIVERED', $smtpResponse);

            Log::info('Delivery confirmed', [
                'message_id' => $messageId,
                'email' => $email
            ]);
        }
    }

    private function handleComplaint($message, $messageId)
    {
        $complainedRecipients = $message['complaint']['complainedRecipients'] ?? [];

        foreach ($complainedRecipients as $recipient) {
            $email = $recipient['emailAddress'];
            $reason = 'User marked email as spam';

            $this->updateRecipientLog($messageId, $email, 'COMPLAINT', $reason);

            Log::warning('Complaint received', [
                'message_id' => $messageId,
                'email' => $email
            ]);
        }
    }

    private function updateRecipientLog($messageId, $email, $status, $reason)
    {
        if ($messageId) {
            $updated = MailRecipientLog::where('message_id', $messageId)->update([
                'status' => $status,
                'bounce_reason' => $status === 'BOUNCED' ? $reason : null,
                'delivered_at' => $status === 'DELIVERED' ? now() : null
            ]);

            if ($updated) {
                Log::info('Updated by message_id', [
                    'message_id' => $messageId,
                    'status' => $status
                ]);
                return;
            }
        }
    }
}
