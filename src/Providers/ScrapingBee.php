<?php

namespace Eelcol\LaravelScrapers\Providers;

use Eelcol\LaravelScrapers\Contracts\Scraper;
use Eelcol\LaravelScrapers\Support\Lock;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ScrapingBee implements Scraper
{
    protected bool $premium = false;

    public function setPremium(bool $premium): self
    {
        $this->premium = $premium;

        return $this;
    }

    function get(string $url): Response
    {
        return Lock::create('scrapingbee', config('scraper.concurrency'), 40, function () use ($url) {
            $url = "https://app.scrapingbee.com/api/v1/?api_key=" . config('scraper.providers.scrapingbee.key') . "&render_js=false&country_code=nl&url=" . urlencode($url);

            if ($this->premium) {
                $url .= "&premium_proxy=true";
            }

            return Http::get($url);
        });
    }

    function image(string $url): Response
    {
        return Lock::create('scrapingbee', config('scraper.concurrency'), 40, function () use ($url) {
            $url = "https://app.scrapingbee.com/api/v1/?api_key=" . config('scraper.providers.scrapingbee.key') . "&render_js=false&country_code=nl&forward_headers=true&url=" . urlencode($url);

            if ($this->premium) {
                $url .= "&premium_proxy=true";
            }

            return Http::get($url);
        });
    }
}
