<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Eelcol\LaravelScrapers\Exceptions\ScrapeCallError;
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

            return Http::timeout(60)
                ->withHeaders($this->buildHeaders())
                ->get($url);
        });

        return $this->processResponse($response);
    }

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse
    {
        $response = Lock::create('scrapingbee', config('scraper.concurrency'), 40, function () use ($url, $data, $body_format) {
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

            return Http::timeout(60)
                ->withHeaders($this->buildHeaders())
                ->bodyFormat($body_format)
                ->post($url, $data);
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

            return Http::timeout(60)->get($url);
        });
    }

    /**
     * @throws ScrapeCallError
     */
    protected function processResponse(Response $response): ScrapeResponse
    {
        $json = $response->json();
        if (is_null($json)) {
            throw new ScrapeCallError("Invalid JSON returned");
        }

        if (isset($json['errors'])) {
            // handle errors
            throw new ScrapeCallError(json_encode($json['errors']));
        }

        if (!isset($json['initial-status-code'])) {
            throw new ScrapeCallError($response->body());
        }

        if ($json['initial-status-code'] == 403) {
            throw new ScrapeCallError(json_encode($json['body']));
        }

        if ($this->remember_cookies) {
            if (!empty($json['cookies'])) {
                $this->cookies = $json['cookies'] + $this->cookies;
            } elseif (isset($json['headers']['Set-Cookie'])) {
                $cookies = explode("/,/", $json['headers']['Set-Cookie']);
                foreach ($cookies as $c) {
                    $cookie = substr($c, 0, strpos($c, ";"));
                    if (!str_contains($cookie, "=")) {
                        $this->cookies[] = ['name' => $cookie, 'value' => ""];
                    } else {
                        $cookieName = substr($cookie, 0, strpos($cookie, "="));
                        $cookieValue = substr($cookie, strpos($cookie, "=")+1);
                        $this->cookies[] = ['name' => $cookieName, 'value' => $cookieValue];
                    }
                }
            }
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
