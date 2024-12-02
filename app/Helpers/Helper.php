<?php

use App\Models\BlacklistDomain;
use Carbon\Carbon;

function getDateFormat(string $date = null): ?Carbon
{
    return isset($date)
        ? date('Y-m-d h:i:s A', strtotime($date))
        : '';
}