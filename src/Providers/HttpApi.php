<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HttpApi implements Scraper
{
    protected bool $premium = false;

    public function setPremium(bool $premium): self
    {
        $this->premium = $premium;

        return $this;
    }

    function get(string $url): Response
    {
        return Http::get($url);
    }

    function image(string $url): Response
    {
        return Http::get($url);
    }
}
