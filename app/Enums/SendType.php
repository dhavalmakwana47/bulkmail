<?php

namespace App\Enums;

enum SendType: string
{
    case NOW = 'NOW';
    case SCHEDULED = 'SCHEDULED';
}
