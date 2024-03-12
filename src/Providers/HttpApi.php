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

    protected array $cookies = [];

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

        return $this->processResponse($response);
    }

    public function post(string $url, array $data = []): ScrapeResponse
    {
        $domainParts = parse_url($url);
        $domain = $domainParts['host'];

        $response = Http::contentType('application/json')
            ->acceptJson()
            ->withHeaders($this->headers)
            ->withCookies($this->prepareCookies(), $domain)
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36')
            ->asForm()
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

    protected function processResponse(Response $response)
    {
        $headers = $response->headers();

        if ($this->remember_cookies && isset($headers['Set-Cookie'])) {
            foreach ($headers['Set-Cookie'] as $c) {
                $cookie = substr($c, 0, strpos($c, ";"));
                if (!str_contains($cookie, "=")) {
                    $this->cookies[] = ['name' => $cookie, 'value' => ""];
                } else {
                    $cookieName = substr($cookie, 0, strpos($cookie, "="));
                    $cookieValue = substr($cookie, strpos($cookie, "=") + 1);
                    $this->cookies[] = ['name' => $cookieName, 'value' => $cookieValue];
                }
            }
        }

        return ScrapeResponse::fromResponse($response);
    }

    protected function prepareCookies(): array
    {
        $cookies = [];
        foreach ($this->cookies as $cookie) {
            $cookies[$cookie['name']] = $cookie['value'];
        }

        return $cookies;
    }
}
