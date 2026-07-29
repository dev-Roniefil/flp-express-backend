<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * API should never redirect to a web login route.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}