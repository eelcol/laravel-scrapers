<?php

namespace Eelcol\LaravelScrapers\Contracts;

use Illuminate\Http\Client\Response;

interface Scraper
{
    public function setPremium(bool $premium): self;

    public function get(string $url): Response;

    public function image(string $url): Response;
}
