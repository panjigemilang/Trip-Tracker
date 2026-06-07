<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case UPCOMING = 'upcoming';
    case CURRENT = 'current';
    case COMPLETED = 'completed';
    case SKIPPED = 'skipped';
    case CANCELLED = 'cancelled';
    case MISSED = 'missed';
}
