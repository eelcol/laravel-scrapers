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

    public function get(string $url): Response
    {
        return $this->resolve()->get($url);
    }

    public function image(string $url): Response
    {
        return $this->resolve()->image($url);
    }

    /**
     * @throws ScraperProviderNotFound
     */
    public function resolve(): Scraper
    {
        if ($this->test) {
            return app(HttpApi::class)->setPremium($this->premium);
        }

        if (!isset($this->config['providers'][$this->current])) {
            throw new ScraperProviderNotFound($this->current);
        }

        return app($this->config['providers'][$this->current]['provider'])
            ->setPremium($this->premium);
    }
}
