<?php

namespace App\Types;

class ApiStatusCode
{
    const VALIDATION_ERROR = 422;
    const SUCCESS = 200;
    const AUTHENTICATION_ERROR = 401;
    const DISALLOWED_ERROR = 403;
    const LOCKOUT_ERROR = 429;
}