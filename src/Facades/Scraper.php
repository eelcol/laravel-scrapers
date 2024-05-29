<?php

namespace Eelcol\LaravelScrapers\Facades;

use Eelcol\LaravelScrapers\Support\ScrapeResponse;
use Eelcol\LaravelScrapers\Support\ScraperManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ScrapeResponse get(string $url);
 * @method static ScrapeResponse post(string $url, array $data = []);
 * @method static ScrapeResponse image(string $url);
 * @method static ScraperManager provider(string $provider);
 * @method static ScraperManager premium();
 * @method static ScraperManager rememberCookies(bool $bool = true);
 * @method static ScraperManager withHeaders(array $headers);
 * @method static \Eelcol\LaravelScrapers\Contracts\Scraper resolve();
 * @method static void test();
 */
class Scraper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-scraper';
    }
}
