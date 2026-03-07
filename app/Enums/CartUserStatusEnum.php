<?php

namespace App\Enums;

enum CartUserStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
