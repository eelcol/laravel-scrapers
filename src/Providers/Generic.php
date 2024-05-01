<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Eelcol\LaravelScrapers\Support\ScrapeResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Generic implements Scraper
{
    protected array $headers = [];

    protected bool $remember_cookies = false;

    protected array $cookies;

    public function instantiate(array $headers, bool $rememberCookies, bool $premium): self
    {
        $this->remember_cookies = $rememberCookies;

        $this->headers = $headers;

        return $this;
    }

    public function get(string $url): ScrapeResponse
    {
        $response = Http::withToken(config('scraper.providers.generic.token'))
            ->asForm()
            ->post(config('scraper.providers.generic.url'), [
                'method' => 'get',
                'url' => $url,
                'headers' => $this->headers,
                'cookies' => $this->buildCookies()
            ]);

        return $this->processResponse($response);
    }

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse
    {
        $response = Http::withToken(config('scraper.providers.generic.token'))
            ->asForm()
            ->post(config('scraper.providers.generic.url'), [
                'method' => 'post',
                'url' => $url,
                'headers' => $this->headers,
                'cookies' => $this->buildCookies(),
                'body_format' => $body_format,
                'data' => $data
            ]);

        return $this->processResponse($response);
    }

    public function image(string $url): ScrapeResponse
    {
        $response = Http::withToken(config('scraper.providers.generic.token'))->asForm()
            ->post(config('scraper.providers.generic.url'), [
                'method' => 'post',
                'url' => $url,
                'headers' => $this->headers,
                'cookies' => $this->buildCookies(),
            ]);

        return ScrapeResponse::fromResponse($response, true);
    }

    protected function buildCookies(): array
    {
        $cookies = [];
        if (isset($this->cookies)) {
            $cookies = $this->cookies;
        }

        return $cookies;
    }

    protected function processResponse(Response $response): ScrapeResponse
    {
        if ($this->remember_cookies) {
            $this->cookies = $response->json('cookies') ?? [];
        }

        return ScrapeResponse::fromResponse($response, true);
    }
}
