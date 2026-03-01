<?php

namespace App\Enums;

enum ActionType: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case VIEW = 'view';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case STATUS_CHANGE = 'status_change';
    case IMPORT = 'import';
    case EXPORT = 'export';
    case SEND = 'send';
    case RESEND = 'resend';
}
