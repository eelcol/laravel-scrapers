<?php

namespace Eelcol\LaravelScrapers\Facades;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Facade;

/**
 * @method static get(string $url): Response;
 * @method static image(string $url): Response;
 * @method static provider(string $provider): \Eelcol\LaravelScrapers\Support\ScraperManager;
 * @method static premium(): \Eelcol\LaravelScrapers\Support\ScraperManager;
 * @method static resolve(): \Eelcol\LaravelScrapers\Contracts\Scraper;
 * @method static test(): void;
 */
class Scraper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'scraper';
    }
}
