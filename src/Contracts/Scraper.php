<?php

namespace Eelcol\LaravelScrapers\Contracts;

use Eelcol\LaravelScrapers\Support\ScrapeResponse;

interface Scraper
{
    public function instantiate(array $headers, bool $rememberCookies, ?string $body = null, ?bool $premium = false): self;

    public function get(string $url, array $options = []): ScrapeResponse;

    public function post(string $url, array $data = [], string $body_format = 'form_params'): ScrapeResponse;

    public function image(string $url): ScrapeResponse;
}
