<?php

namespace Eelcol\LaravelScrapers\Contracts;

use Eelcol\LaravelScrapers\Support\ScrapeResponse;

interface Scraper
{
    public function instantiate(array $headers, bool $rememberCookies, bool $premium): self;

    public function get(string $url): ScrapeResponse;

    public function post(string $url, array $data = []): ScrapeResponse;

    public function image(string $url): ScrapeResponse;
}
