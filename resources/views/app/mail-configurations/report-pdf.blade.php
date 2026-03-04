<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mail Configuration Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; font-size: 7px; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 15px; font-size: 14px; }
        h3 { font-size: 10px; margin: 10px 0 5px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Mail Configuration Report</h2>
    </div>

    <h3>Statistics</h3>
    <table>
        <tr>
            <th>Total Contacts</th>
            <td>{{ $stats['total'] }}</td>
            <th>Sent Successfully</th>
            <td>{{ $stats['sent'] }}</td>
        </tr>
        <tr>
            <th>Failed</th>
            <td>{{ $stats['failed'] }}</td>
            <th>Success Rate</th>
            <td>{{ $stats['total'] > 0 ? round(($stats['sent'] / $stats['total']) * 100, 2) : 0 }}%</td>
        </tr>
    </table>

    <h3>Configuration Details</h3>
    <table>
        <tr>
            <th width="30%">Corporate Debtor</th>
            <td>{{ $mailConfiguration->user->name }}</td>
        </tr>
        <tr>
            <th>Subject</th>
            <td>{{ $mailConfiguration->subject }}</td>
        </tr>
        <tr>
            <th>Send Type</th>
            <td>{{ is_string($mailConfiguration->send_type) ? $mailConfiguration->send_type : $mailConfiguration->send_type->value }}</td>
        </tr>
        <tr>
            <th>Scheduled At</th>
            <td>{{ $mailConfiguration->scheduled_at ? \Carbon\Carbon::parse($mailConfiguration->scheduled_at)->format('d-m-Y H:i') : '-' }}</td>
        </tr>
    </table>

    <h3>Recipient Details</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 12%;">Contact Name</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 7%;">Status</th>
                <th style="width: 12%;">Sent At</th>
                <th style="width: 12%;">Delivered At</th>
                <th style="width: 13%;">Message ID</th>
                <th style="width: 13%;">Error Message</th>
                <th style="width: 13%;">Bounce Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $log->contact->name }}</td>
                <td>{{ $log->contact->email }}</td>
                <td>{{ is_string($log->status) ? $log->status : $log->status->value }}</td>
                <td>{{ $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('d-m-Y H:i') : '-' }}</td>
                <td>{{ $log->delivered_at ? \Carbon\Carbon::parse($log->delivered_at)->format('d-m-Y H:i') : '-' }}</td>
                <td>{{ $log->message_id ? substr($log->message_id, 0, 20) . '...' : '-' }}</td>
                <td>{{ $log->error_message ?? '-' }}</td>
                <td>{{ $log->bounce_reason ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
