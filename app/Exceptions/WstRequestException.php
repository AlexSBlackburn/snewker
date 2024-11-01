<?php

namespace App\Exceptions;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class WstRequestException extends RequestException
{
    protected function prepareMessage(Response $response)
    {
        return $response->json('errors.0.detail');
    }
}
