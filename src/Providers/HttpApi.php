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

    protected ?string $body = null;

    protected array $options = [];

    public function instantiate(array $headers, bool $rememberCookies, ?string $body = null, ?bool $premium = false, array $options = []): self
    {
        $this->remember_cookies = $rememberCookies;
        $this->headers = $headers;
        $this->body = $body;
        $this->options = $options;

        // Premium is not possible in this provider

        return $this;
    }

    protected function mergeClientOptions(array $requestOptions = []): array
    {
        $merged = array_merge($this->options, $requestOptions);
        if (isset($this->cookieJar)) {
            $merged = array_merge($merged, ['cookies' => $this->cookieJar]);
        }

        return $merged;
    }

    public function get(string $url, array $options = []): ScrapeResponse
    {
        $clientOptions = $this->mergeClientOptions($options);

        $response = Http::contentType('application/json')
            ->acceptJson()
            ->withHeaders($this->headers)
            ->when($clientOptions !== [], function ($r) use ($clientOptions) {
                $r->withOptions($clientOptions);
            })
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36')
            ->get($url);

        return $this->processResponse($response);
    }

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse
    {
        $clientOptions = $this->mergeClientOptions();

        $response = Http::contentType('application/json')
            ->acceptJson()
            ->bodyFormat($body_format)
            ->withHeaders($this->headers)
            ->when($clientOptions !== [], function ($r) use ($clientOptions) {
                $r->withOptions($clientOptions);
            })
            ->when(isset($this->body), function ($r) {
                $r->withBody($this->body);
            })
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36')
            ->post($url, $data);

        return $this->processResponse($response);
    }

    public function image(string $url): ScrapeResponse
    {
        $clientOptions = $this->mergeClientOptions();

        $response = Http::withUserAgent(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36'
        )->when($clientOptions !== [], function ($r) use ($clientOptions) {
            $r->withOptions($clientOptions);
        })->get($url);

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
