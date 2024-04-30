<?php

use Eelcol\LaravelScrapers\Providers\Generic;
use Eelcol\LaravelScrapers\Providers\HttpApi;
use Eelcol\LaravelScrapers\Providers\Proxy;
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

        'proxy' => [
            'host' => env('SCRAPER_PROXY_HOST'),
            'port' => env('SCRAPER_PROXY_PORT'),
            'user' => env('SCRAPER_PROXY_USER'),
            'pass' => env('SCRAPER_PROXY_PASS'),
            'provider' => Proxy::class,
        ],

        'generic' => [
            'url' => env('SCRAPER_GENERIC_URL'),
            'token' => env('SCRAPER_GENERIC_TOKEN'),
            'provider' => Generic::class,
        ]
    ],
];
