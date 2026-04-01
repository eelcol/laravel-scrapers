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

    protected ?string $body = null;

    protected array $options = [];

    public function instantiate(array $headers, bool $rememberCookies, ?string $body = null, ?bool $premium = false, array $options = []): self
    {
        $this->remember_cookies = $rememberCookies;

        $this->headers = $headers;

        $this->body = $body;

        $this->options = $options;

        return $this;
    }

    public function getCookies(): array
    {
        return $this->buildCookies();
    }

    public function get(string $url, array $options = []): ScrapeResponse
    {
        $response = Http::withToken(config('scraper.providers.generic.token'))
            ->asForm()
            ->timeout(60)
            ->post(config('scraper.providers.generic.url'), array_merge([
                'method' => 'get',
                'url' => $url,
                'headers' => $this->headers,
                'cookies' => $this->buildCookies()
            ], $this->options, $options));

        return $this->processResponse($response);
    }

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse
    {
        $response = Http::withToken(config('scraper.providers.generic.token'))
            ->asForm()
            ->timeout(60)
            ->post(config('scraper.providers.generic.url'), array_merge($this->options, [
                'method' => 'post',
                'url' => $url,
                'headers' => $this->headers,
                'cookies' => $this->buildCookies(),
                'body_format' => $body_format,
                'data' => $data,
                'body' => $this->body
            ]));

        return $this->processResponse($response);
    }

    public function image(string $url): ScrapeResponse
    {
        $response = Http::withToken(config('scraper.providers.generic.token'))->asForm()
            ->timeout(60)
            ->post(config('scraper.providers.generic.url'), array_merge($this->options, [
                'method' => 'image',
                'url' => $url,
                'headers' => $this->headers,
                'cookies' => $this->buildCookies(),
            ]));

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
