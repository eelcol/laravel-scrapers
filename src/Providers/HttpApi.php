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
        return Http::contentType('application/json')
            ->acceptJson()
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36')
            ->get($url);
    }

    function image(string $url): Response
    {
        return Http::withUserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36'
        )->get($url);
    }
}
