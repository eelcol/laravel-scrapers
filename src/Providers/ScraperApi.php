<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use GuzzleHttp\Client;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ScraperApi implements Scraper
{
    protected bool $premium = false;

    public function setPremium(bool $premium): self
    {
        $this->premium = $premium;

        return $this;
    }

    function get(string $url): Response
    {
        $url = "http://api.scraperapi.com?api_key=".config('scraper.providers.scraperapi.key') . "&follow_redirect=true&country_code=eu&url=" . urlencode($url);

        return Http::get($url);
    }

    function image(string $url): Response
    {
        $url = "http://api.scraperapi.com?api_key=".config('scraper.providers.scraperapi.key') . "&follow_redirect=true&binary_target=true&country_code=eu&url=" . urlencode($url);

        return Http::get($url);
    }
}
