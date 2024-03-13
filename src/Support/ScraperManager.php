<?php

namespace Eelcol\LaravelScrapers\Support;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Eelcol\LaravelScrapers\Exceptions\ScraperProviderNotFound;
use Eelcol\LaravelScrapers\Providers\HttpApi;
use Illuminate\Http\Client\Response;
use Psr\Http\Message\ResponseInterface;

class ScraperManager
{
    protected array $config;

    protected string $current;

    protected bool $premium = false;

    protected bool $test = false;

    protected bool $remember_cookies = false;

    protected array $headers = [];

    protected array $resolved = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->current = $this->config['current'];
    }

    public function provider(string $provider): self
    {
        $this->current = $provider;

        return $this;
    }

    public function premium(): self
    {
        $this->premium = true;

        return $this;
    }

    public function test(): void
    {
        $this->test = true;
    }

    public function rememberCookies(bool $bool = true): self
    {
        $this->remember_cookies = $bool;

        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function get(string $url): ScrapeResponse
    {
        return $this->resolve()->get($url);
    }

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse
    {
        return $this->resolve()->post($url, $data, $body_format);
    }

    public function image(string $url): ScrapeResponse
    {
        return $this->resolve()->image($url);
    }

    public function debug(string $url, array $data = []): void
    {
        $this->resolve()->debug($url, $data);
    }

    /**
     * @throws ScraperProviderNotFound
     */
    public function resolve(): Scraper
    {
        if ($this->test) {
            if (!array_key_exists('test', $this->resolved)) {
                $this->resolved['test'] = app(HttpApi::class);
            }

            return $this->resolved['test']->instantiate(
                headers: $this->headers,
                rememberCookies: $this->remember_cookies,
                premium: $this->premium
            );
        }

        if (!isset($this->config['providers'][$this->current])) {
            throw new ScraperProviderNotFound($this->current);
        }

        if (!array_key_exists($this->config['providers'][$this->current]['provider'], $this->resolved)) {
            $this->resolved[$this->config['providers'][$this->current]['provider']] = app($this->config['providers'][$this->current]['provider']);
        }

        return $this->resolved[$this->config['providers'][$this->current]['provider']]
            ->instantiate(
                headers: $this->headers,
                rememberCookies: $this->remember_cookies,
                premium: $this->premium
            );
    }
}
