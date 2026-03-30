<?php

namespace Eelcol\LaravelScrapers\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class UnknownScraperError extends Exception
{
    public function __construct(
        protected Response $response,
        string $message = 'Unknown scraper error.',
    ) {
        parent::__construct($message);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
