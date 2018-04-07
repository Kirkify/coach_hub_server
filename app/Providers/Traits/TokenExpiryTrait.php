<?php

namespace App\Providers\Traits;

use Carbon\Carbon;

trait TokenExpiryTrait {

    public function getTokenExpiry()
    {
        return Carbon::now()->addDays(1);
    }

    public function getRefreshTokenExpiry()
    {
        return now()->addDays(30);
    }
}