<?php

use Eelcol\LaravelScrapers\Providers\HttpApi;
use Eelcol\LaravelScrapers\Providers\ScraperApi;
use Eelcol\LaravelScrapers\Providers\ScrapingBee;

return [
    'current' => env('SCRAPER_PROVIDER', 'scrapingbee'),

    'concurrency' => env('SCRAPER_MAX_CONCURRENCY', null),

    'providers' => [
        'scraperapi' => [
            'key' => env('SCRAPERAPI_KEY'),
            'provider' => ScraperApi::class,
        ],

        'scrapingbee' => [
            'key' => env('SCRAPINGBEE_KEY'),
            'provider' => ScrapingBee::class,
        ],

        'http' => [
            'provider' => HttpApi::class,
        ],
    ],
];
