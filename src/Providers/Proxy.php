<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Eelcol\LaravelScrapers\Exceptions\ProxyInformationMissing;
use Eelcol\LaravelScrapers\Support\ScrapeResponse;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Proxy implements Scraper
{
    protected array $headers = [];

    protected bool $remember_cookies = false;

    protected CookieJar $cookieJar;

    /**
     * @throws ProxyInformationMissing
     */
    public function instantiate(array $headers, bool $rememberCookies, bool $premium): self
    {
        $this->remember_cookies = $rememberCookies;

        $this->headers = $headers;

        if (!config('scraper.providers.proxy.host') || !config('scraper.providers.proxy.port')) {
            throw new ProxyInformationMissing("Host or port are missing.");
        }

        return $this;
    }

    protected function getProxyOption(): string
    {
        $string = "";
        if (config('scraper.providers.proxy.user') && config('scraper.providers.proxy.pass')) {
            $string = config('scraper.providers.proxy.user') . ":" . config('scraper.providers.proxy.pass') . "@";
        }

        return $string . config('scraper.providers.proxy.host') . ":" . config('scraper.providers.proxy.port');
    }

    public function get(string $url): ScrapeResponse
    {
        $response = Http::contentType('application/json')
            ->acceptJson()
            ->withHeaders($this->headers)
            ->when(isset($this->cookieJar), function ($r) {
                $r->withOptions(['cookies' => $this->cookieJar]);
            })
            ->withOptions([
                'proxy' => $this->getProxyOption(),
            ])
            ->timeout(15)
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
            ->withOptions([
                'proxy' => $this->getProxyOption(),
            ])
            ->timeout(15)
            ->post($url, $data);

        return $this->processResponse($response);
    }

    public function image(string $url): ScrapeResponse
    {
        return $this->get($url);
    }

    protected function processResponse(Response $response): ScrapeResponse
    {
        if ($this->remember_cookies) {
            $this->cookieJar = $response->cookies();
        }

        return ScrapeResponse::fromResponse($response);
    }
}
