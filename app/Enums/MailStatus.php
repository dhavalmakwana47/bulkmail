<?php

namespace App\Enums;

enum MailStatus: string
{
    case PENDING = 'PENDING';
    case SENT = 'SENT';
    case DELIVERED = 'DELIVERED';
    case BOUNCED = 'BOUNCED';
    case FAILED = 'FAILED';
}
