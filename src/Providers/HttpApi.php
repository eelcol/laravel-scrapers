<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Eelcol\LaravelScrapers\Support\ScrapeResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HttpApi implements Scraper
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
        $response = Http::contentType('application/json')
            ->acceptJson()
            ->withHeaders($this->headers)
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36')
            ->get($url);

        return ScrapeResponse::fromResponse($response);
    }

    public function post(string $url, array $data = []): ScrapeResponse
    {
        $response = Http::contentType('application/json')
            ->acceptJson()
            ->withHeaders($this->headers)
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36')
            ->asForm()
            ->post($url, $data);

        return ScrapeResponse::fromResponse($response);
    }

    public function image(string $url): ScrapeResponse
    {
        $response = Http::withUserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36'
        )->get($url);

        return ScrapeResponse::fromResponse($response);
    }
}
