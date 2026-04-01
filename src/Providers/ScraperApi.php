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

    protected array $options = [];

    public function instantiate(array $headers, bool $rememberCookies, ?string $body = null, ?bool $premium = false, array $options = []): self
    {
        $this->premium = $premium;

        $this->remember_cookies = $rememberCookies;

        $this->headers = $headers;

        $this->options = $options;

        return $this;
    }

    public function get(string $url, array $options = []): ScrapeResponse
    {
        $options = array_merge([
            'api_key' => config('scraper.providers.scraperapi.key'),
            'follow_redirect' => "true",
            'country_code' => 'eu',
            'url' => $url,
        ], $this->options, $options);

        $url = "http://api.scraperapi.com?" . http_build_query($options);

        return ScrapeResponse::fromResponse(
            Http::get($url)
        );
    }

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse
    {
        $query = array_merge([
            'api_key' => config('scraper.providers.scraperapi.key'),
            'follow_redirect' => 'true',
            'country_code' => 'eu',
            'url' => $url,
        ], $this->options);

        $fullUrl = 'http://api.scraperapi.com?' . http_build_query($query);

        return ScrapeResponse::fromResponse(
            Http::bodyFormat($body_format)->post($fullUrl, $data)
        );
    }

    public function image(string $url): ScrapeResponse
    {
        $query = array_merge([
            'api_key' => config('scraper.providers.scraperapi.key'),
            'follow_redirect' => 'true',
            'binary_target' => 'true',
            'country_code' => 'eu',
            'url' => $url,
        ], $this->options);

        $fullUrl = 'http://api.scraperapi.com?' . http_build_query($query);

        return ScrapeResponse::fromResponse(
            Http::get($fullUrl)
        );
    }
}
