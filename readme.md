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

- When writing a test that triggers the scraper, you probably do not want to use ScraperAPI or ScrapingBee. In that case, include the test method in your test:

```
...
Scraper::test();
...
```

# Installation

Require this package with composer.

````
composer require eelcol/laravel-scrapers
````

Add to your env:
```
SCRAPER_PROVIDER=see below
SCRAPER_MAX_CONCURRENCY=5
SCRAPERAPI_KEY=
SCRAPINGBEE_KEY=
```

When using a different proxy, add the following variables to your .env:
````
SCRAPER_PROXY_HOST=
SCRAPER_PROXY_PORT=
SCRAPER_PROXY_USER=
SCRAPER_PROXY_PASS=
````

The following values are allowed for `SCRAPER_PROVIDER`

- scrapingbee
- scraperapi
- http
- proxy

Use `http` for normal HTTP requests, without using a scraper provider. Use `proxy` to use a custom defined proxy.