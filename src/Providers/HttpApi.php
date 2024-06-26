<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Eelcol\LaravelScrapers\Support\ScrapeResponse;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HttpApi implements Scraper
{
    protected array $headers = [];

    protected bool $remember_cookies = false;

    protected CookieJar $cookieJar;

    public function instantiate(array $headers, bool $rememberCookies, bool $premium): self
    {
        $this->remember_cookies = $rememberCookies;

        $this->headers = $headers;

        return $this;
    }

    public function get(string $url): ScrapeResponse
    {
        $response = Http::contentType('application/json')
            ->acceptJson()
            ->withHeaders($this->headers)
            ->when(isset($this->cookieJar), function ($r) {
                $r->withOptions(['cookies' => $this->cookieJar]);
            })
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36')
            ->get($url);

        return $this->processResponse($response);
    }

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse
    {
        $response = Http::contentType('application/json')
            ->acceptJson()
            ->bodyFormat($body_format)
            ->withHeaders($this->headers)
            ->when(isset($this->cookieJar), function ($r) {
                $r->withOptions(['cookies' => $this->cookieJar]);
            })
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36')
            ->post($url, $data);

        return $this->processResponse($response);
    }

    public function image(string $url): ScrapeResponse
    {
        $response = Http::withUserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36'
        )->get($url);

        return ScrapeResponse::fromResponse($response);
    }

    protected function processResponse(Response $response): ScrapeResponse
    {
        if ($this->remember_cookies) {
            $this->cookieJar = $response->cookies();
        }

        return ScrapeResponse::fromResponse($response);
    }
}
