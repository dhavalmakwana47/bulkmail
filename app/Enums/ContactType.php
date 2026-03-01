<?php

namespace App\Enums;

enum ContactType: string
{
    case BIDDER = 'BIDDER';
    case MEMBER = 'MEMBER';
    case OTHER = 'OTHER';
}
