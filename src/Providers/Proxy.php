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

    protected array $options = [];

    /**
     * @throws ProxyInformationMissing
     */
    public function instantiate(array $headers, bool $rememberCookies, ?string $body = null, ?bool $premium = false, array $options = []): self
    {
        $this->remember_cookies = $rememberCookies;

        $this->headers = $headers;

        $this->options = $options;

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

    public function get(string $url, array $options = []): ScrapeResponse
    {
        $clientOptions = array_merge(
            ['proxy' => $this->getProxyOption()],
            $this->options,
            $options
        );
        if (isset($this->cookieJar)) {
            $clientOptions = array_merge($clientOptions, ['cookies' => $this->cookieJar]);
        }

        $response = Http::withHeaders($this->headers)
            ->withOptions($clientOptions)
            ->timeout(15)
            ->get($url);

        return $this->processResponse($response);
    }

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse
    {
        $clientOptions = array_merge(
            ['proxy' => $this->getProxyOption()],
            $this->options
        );
        if (isset($this->cookieJar)) {
            $clientOptions = array_merge($clientOptions, ['cookies' => $this->cookieJar]);
        }

        $response = Http::bodyFormat($body_format)
            ->withHeaders($this->headers)
            ->withOptions($clientOptions)
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
