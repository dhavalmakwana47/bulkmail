<?php

// Convert date from UTC to IST
date_default_timezone_set('Asia/Kolkata');

$logfile = 'bounce_res-' . date('Y-m-d') . '.log';

// Database connection - Update with your credentials
$DB = mysqli_connect("localhost", "u742100231_bulkmail", "u2^JJOLf:M", "u742100231_bulkmail");

if ($DB) {
    error_log("[" . gmdate(DATE_RFC822) . "] DB connection success\n", 3, $logfile);
} else {
    error_log("[" . gmdate(DATE_RFC822) . "] DB connection failed\n", 3, $logfile);
    die('Database connection failed');
}

$log = "[" . gmdate(DATE_RFC822) . "] msg:------------ Start Email Status Processing --------------------\n";
error_log($log, 3, $logfile);

$postBody = file_get_contents('php://input');
$log = "[" . gmdate(DATE_RFC822) . "] msg: Post Body = \n" . print_r($postBody, true) . "\n";
error_log($log, 3, $logfile);

$response_data = json_decode($postBody, true);
$date = date("Y-m-d H:i:s");

if (!$response_data) {
    error_log("[" . gmdate(DATE_RFC822) . "] msg: Response Data not found\n", 3, $logfile);
    die('Invalid data');
}

// Handle SNS Subscription Confirmation
if ($response_data['Type'] == 'SubscriptionConfirmation') {
    $subscribeUrl = $response_data['SubscribeURL'];
    file_get_contents($subscribeUrl);
    error_log("[" . gmdate(DATE_RFC822) . "] SNS Subscription confirmed\n", 3, $logfile);
    echo 'Subscription confirmed';
    exit;
}

if ($response_data['Type'] == "Notification") {
    $message = json_decode($response_data['Message'], true);
    $log = "[" . gmdate(DATE_RFC822) . "] msg: Message Data = \n" . print_r($message, true) . "\n";
    error_log($log, 3, $logfile);

    $messageId = isset($message['mail']['messageId']) ? $message['mail']['messageId'] : null;

    // Handle Delivery Notification
    if ($message['notificationType'] == "Delivery") {
        $log = "[" . gmdate(DATE_RFC822) . "] msg: Processing Delivery notification\n";
        error_log($log, 3, $logfile);

        $recipients = $message['delivery']['recipients'];
        $smtpResponse = isset($message['delivery']['smtpResponse']) ? $message['delivery']['smtpResponse'] : 'Delivered';

        foreach ($recipients as $emailAddress) {
            $emailAddress = mysqli_real_escape_string($DB, trim($emailAddress));
            $reason = mysqli_real_escape_string($DB, $smtpResponse);

            if ($messageId) {
                $messageIdEscaped = mysqli_real_escape_string($DB, $messageId);
                $update = "UPDATE mail_recipient_logs 
                          SET status = 'DELIVERED', 
                              bounce_reason = '{$reason}', 
                              delivered_at = '{$date}'
                          WHERE message_id = '{$messageIdEscaped}'";
            } else {
                $update = "UPDATE mail_recipient_logs mrl
                          INNER JOIN contacts c ON mrl.contact_id = c.id
                          SET mrl.status = 'DELIVERED', 
                              mrl.bounce_reason = '{$reason}', 
                              mrl.delivered_at = '{$date}'
                          WHERE c.email = '{$emailAddress}' 
                          AND mrl.status = 'SENT'
                          ORDER BY mrl.id DESC LIMIT 1";
            }

            $log = "[" . gmdate(DATE_RFC822) . "] SQL Update: {$update}\n";
            error_log($log, 3, $logfile);

            $result = $DB->query($update);

            if ($result) {
                $log = "[" . gmdate(DATE_RFC822) . "] Delivery status updated for: {$emailAddress}\n";
                error_log($log, 3, $logfile);
            } else {
                $log = "[" . gmdate(DATE_RFC822) . "] Failed to update delivery status: " . $DB->error . "\n";
                error_log($log, 3, $logfile);
            }
        }
    }

    // Handle Bounce Notification
    if ($message['notificationType'] == "Bounce") {
        $log = "[" . gmdate(DATE_RFC822) . "] msg: Processing Bounce notification\n";
        error_log($log, 3, $logfile);

        $bounce = $message['bounce'];
        $bounceType = $bounce['bounceType'];
        $bounceSubType = $bounce['bounceSubType'];

        foreach ($bounce['bouncedRecipients'] as $bouncedRecipient) {
            $emailAddress = mysqli_real_escape_string($DB, trim($bouncedRecipient['emailAddress']));
            $diagnosticCode = isset($bouncedRecipient['diagnosticCode']) ? $bouncedRecipient['diagnosticCode'] : '';
            
            if (!empty($diagnosticCode)) {
                $reason = mysqli_real_escape_string($DB, trim($diagnosticCode));
            } else {
                if ($bounceType != $bounceSubType) {
                    $reason = "Bounce - {$bounceType} ({$bounceSubType})";
                } else {
                    $reason = "Bounce - {$bounceType}";
                }
                $reason = mysqli_real_escape_string($DB, $reason);
            }

            $log = "[" . gmdate(DATE_RFC822) . "] Bounce detected: {$emailAddress} - {$reason}\n";
            error_log($log, 3, $logfile);

            if ($messageId) {
                $messageIdEscaped = mysqli_real_escape_string($DB, $messageId);
                $update = "UPDATE mail_recipient_logs 
                          SET status = 'BOUNCED', 
                              bounce_reason = '{$reason}',
                              error_message = '{$reason}'
                          WHERE message_id = '{$messageIdEscaped}'";
            } else {
                $update = "UPDATE mail_recipient_logs mrl
                          INNER JOIN contacts c ON mrl.contact_id = c.id
                          SET mrl.status = 'BOUNCED', 
                              mrl.bounce_reason = '{$reason}',
                              mrl.error_message = '{$reason}'
                          WHERE c.email = '{$emailAddress}' 
                          AND mrl.status = 'SENT'
                          ORDER BY mrl.id DESC LIMIT 1";
            }

            $log = "[" . gmdate(DATE_RFC822) . "] SQL Update: {$update}\n";
            error_log($log, 3, $logfile);

            $result = $DB->query($update);

            if ($result) {
                $log = "[" . gmdate(DATE_RFC822) . "] Bounce status updated for: {$emailAddress}\n";
                error_log($log, 3, $logfile);
            } else {
                $log = "[" . gmdate(DATE_RFC822) . "] Failed to update bounce status: " . $DB->error . "\n";
                error_log($log, 3, $logfile);
            }
        }
    }

    // Handle Complaint Notification
    if ($message['notificationType'] == "Complaint") {
        $log = "[" . gmdate(DATE_RFC822) . "] msg: Processing Complaint notification\n";
        error_log($log, 3, $logfile);

        $complaint = $message['complaint'];
        $complainedRecipients = $complaint['complainedRecipients'];

        foreach ($complainedRecipients as $recipient) {
            $emailAddress = mysqli_real_escape_string($DB, trim($recipient['emailAddress']));
            $reason = 'User marked email as spam';

            if ($messageId) {
                $messageIdEscaped = mysqli_real_escape_string($DB, $messageId);
                $update = "UPDATE mail_recipient_logs 
                          SET status = 'COMPLAINT', 
                              bounce_reason = '{$reason}',
                              error_message = '{$reason}'
                          WHERE message_id = '{$messageIdEscaped}'";
            } else {
                $update = "UPDATE mail_recipient_logs mrl
                          INNER JOIN contacts c ON mrl.contact_id = c.id
                          SET mrl.status = 'COMPLAINT', 
                              mrl.bounce_reason = '{$reason}',
                              mrl.error_message = '{$reason}'
                          WHERE c.email = '{$emailAddress}' 
                          AND mrl.status = 'SENT'
                          ORDER BY mrl.id DESC LIMIT 1";
            }

            $log = "[" . gmdate(DATE_RFC822) . "] SQL Update: {$update}\n";
            error_log($log, 3, $logfile);

            $result = $DB->query($update);

            if ($result) {
                $log = "[" . gmdate(DATE_RFC822) . "] Complaint status updated for: {$emailAddress}\n";
                error_log($log, 3, $logfile);
            } else {
                $log = "[" . gmdate(DATE_RFC822) . "] Failed to update complaint status: " . $DB->error . "\n";
                error_log($log, 3, $logfile);
            }
        }
    }
}

mysqli_close($DB);

$log = "[" . gmdate(DATE_RFC822) . "] msg:------------ End Email Status Processing --------------------\n";
error_log($log, 3, $logfile);

echo 'OK';
?>
