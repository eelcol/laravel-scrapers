<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Eelcol\LaravelScrapers\Support\Lock;
use Eelcol\LaravelScrapers\Support\ScrapeResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\NoReturn;

class ScrapingBee implements Scraper
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
        $response = Lock::create('scrapingbee', config('scraper.concurrency'), 40, function () use ($url) {
            $url = "https://app.scrapingbee.com/api/v1/?api_key=" . config('scraper.providers.scrapingbee.key') . "&render_js=false&country_code=nl&forward_headers=true&json_response=true&url=" . urlencode($url);

            if ($this->premium) {
                $url .= "&premium_proxy=true";
            }

            if ($this->remember_cookies) {
                $cookies = $this->buildCookiesString();
                if ($cookies) {
                    $url .= "&cookies=" . urlencode($cookies);
                }
            }

            return Http::withHeaders($this->buildHeaders())->get($url);
        });

        return $this->processResponse($response);
    }

    public function post(string $url, array $data = []): ScrapeResponse
    {
        $response = Lock::create('scrapingbee', config('scraper.concurrency'), 40, function () use ($url, $data) {
            $url = "https://app.scrapingbee.com/api/v1/?api_key=" . config('scraper.providers.scrapingbee.key') . "&render_js=false&country_code=nl&forward_headers=true&json_response=true&url=" . urlencode($url);

            if ($this->premium) {
                $url .= "&premium_proxy=true";
            }

            if ($this->remember_cookies) {
                $cookies = $this->buildCookiesString();
                if ($cookies) {
                    $url .= "&cookies=" . urlencode($cookies);
                }
            }

            return Http::withHeaders($this->buildHeaders())->asForm()->post($url, $data);
        });

        return $this->processResponse($response);
    }

    #[NoReturn]
    public function debug(string $url, array $data = []): void
    {
        $url = "https://app.scrapingbee.com/api/v1/?api_key=" . config('scraper.providers.scrapingbee.key') . "&render_js=false&country_code=nl&forward_headers=true&json_response=true&url=" . urlencode($url);

        if ($this->premium) {
            $url .= "&premium_proxy=true";
        }

        if ($this->remember_cookies) {
            $cookies = $this->buildCookiesString();
            if ($cookies) {
                $url .= "&cookies=" . urlencode($cookies);
            }
        }

        dd([
            'url' => $url,
            'headers' => $this->buildHeaders(),
            'cookies' => $this->cookies,
        ]);
    }

    public function image(string $url): ScrapeResponse
    {
        return Lock::create('scrapingbee', config('scraper.concurrency'), 40, function () use ($url) {
            $url = "https://app.scrapingbee.com/api/v1/?api_key=" . config('scraper.providers.scrapingbee.key') . "&render_js=false&country_code=nl&forward_headers=true&url=" . urlencode($url);

            if ($this->premium) {
                $url .= "&premium_proxy=true";
            }

            if ($this->remember_cookies) {
                $cookies = $this->buildCookiesString();
                if ($cookies) {
                    $url .= "&cookies=" . urlencode($cookies);
                }
            }

            return Http::get($url);
        });
    }

    protected function processResponse(Response $response): ScrapeResponse
    {
        $json = $response->json();
        if (isset($json['errors'])) {
            // handle errors
        }

        if ($this->remember_cookies) {
            $this->cookies = $json['cookies'] + $this->cookies;
        }

        if (!is_string($json['body'])) {
            $json['body'] = json_encode($json['body']);
        }

        return ScrapeResponse::create(
            $json['body'],
            $json['initial-status-code'],
        );
    }

    protected function buildHeaders(): array
    {
        $newHeaders = [];

        foreach ($this->headers as $key => $value) {
            $newHeaders['Spb-' . $key] = $value;
        }

        return $newHeaders;
    }

    protected function buildCookiesString(): string
    {
        // cookie_name_1=cookie_value1,domain=scrapingbee.com;cookie_name_2=cookie_value_2;cookie_name_3=cookie_value_3,path=/
        $string = "";
        foreach ($this->cookies as $cookie) {
            if (!empty($string)) {
                $string .= ";";
            }

            $string .= $cookie['name'] . "=" . $cookie['value'];
        }

        return trim($string);
    }
}
