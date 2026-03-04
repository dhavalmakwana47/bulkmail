<?php

namespace App\Exports;

use App\Models\MailConfiguration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MailReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $mailConfiguration;

    public function __construct(MailConfiguration $mailConfiguration)
    {
        $this->mailConfiguration = $mailConfiguration;
    }

    public function collection()
    {
        return $this->mailConfiguration->recipientLogs()->with('contact')->get();
    }

    public function headings(): array
    {
        return [
            'Contact Name',
            'Email',
            'Status',
            'Sent At',
            'Delivered At',
            'Message ID',
            'Error Message',
            'Bounce Reason'
        ];
    }

    public function map($log): array
    {
        return [
            $log->contact->name,
            $log->contact->email,
            is_string($log->status) ? $log->status : $log->status->value,
            $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('d-m-Y H:i:s') : '-',
            $log->delivered_at ? \Carbon\Carbon::parse($log->delivered_at)->format('d-m-Y H:i:s') : '-',
            $log->message_id ?? '-',
            $log->error_message ?? '-',
            $log->bounce_reason ?? '-'
        ];
    }
}
