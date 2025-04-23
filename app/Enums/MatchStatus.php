<?php

declare(strict_types=1);

namespace App\Enums;

enum MatchStatus: string
{
    case LIVE = 'Live';
    case COMPLETED = 'Completed';
    case SUSPENDED = 'Suspended';
    case SCHEDULED = 'Scheduled';
}
