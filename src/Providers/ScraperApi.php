<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Eelcol\LaravelScrapers\Support\ScrapeResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ScraperApi implements Scraper
{
    protected bool $premium = false;

    protected array $headers = [];

    protected bool $remember_cookies = false;

    public function instantiate(array $headers, bool $rememberCookies, bool $premium): self
    {
        $this->premium = $premium;

        $this->remember_cookies = $rememberCookies;

        $this->headers = $headers;

        return $this;
    }

    public function get(string $url): ScrapeResponse
    {
        $url = "http://api.scraperapi.com?api_key=".config('scraper.providers.scraperapi.key') . "&follow_redirect=true&country_code=eu&url=" . urlencode($url);

        return ScrapeResponse::fromResponse(
            Http::get($url)
        );
    }

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse
    {
        $url = "http://api.scraperapi.com?api_key=".config('scraper.providers.scraperapi.key') . "&follow_redirect=true&country_code=eu&url=" . urlencode($url);

        return ScrapeResponse::fromResponse(
            Http::bodyFormat($body_format)->post($url, $data)
        );
    }

    public function image(string $url): ScrapeResponse
    {
        $url = "http://api.scraperapi.com?api_key=".config('scraper.providers.scraperapi.key') . "&follow_redirect=true&binary_target=true&country_code=eu&url=" . urlencode($url);

        return ScrapeResponse::fromResponse(
            Http::get($url)
        );
    }
}
