<?php

namespace App\Constants;

class HttpStatusCodes
{
    const SUCCESS = 200;
    const CREATED = 201;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const ACCESS_DENIED = 403;
    const NOT_FOUND = 404;
    const SESSION_EXPIRED = 410;
    const UNPROCESSABLE_ENTITY = 422;
    const INTERNAL_SERVER_ERROR = 500;
    const MAINTENANCE = 503;
    const INVALID_RESPONSE = 505;
}
