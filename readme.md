# Laravel Scrapers

A Laravel package to scrape webpages using ScrapingBee and/or ScraperApi.

# Examples

- Scrape an URL:
```
$response = Scraper::get('https://www.nu.nl');
```

- Scrape an image:
```
$response = Scraper::image('https://via.placeholder.it/250x250.png');
```

- Use a specific scraper:
```
$response = Scraper::provider('scraperapi')->get('https://www.nu.nl');
```

- Use premium proxies:
```
$response = Scraper::premium()->get('https://www.nu.nl');
```

# Installation

Require this package with composer.

````
composer require eelcol/laravel-scrapers
````

Publish config:

```
php artisan config:publish --provider=laravel-scraper
```

Add to your env:
```
SCRAPER_PROVIDER=scrapingbee or scraperapi
SCRAPER_MAX_CONCURRENCY=5
SCRAPERAPI_KEY=
SCRAPINGBEE_KEY=
```
