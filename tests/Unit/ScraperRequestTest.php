<?php

namespace Eelcol\LaravelScrapers\Tests\Unit;

use Eelcol\LaravelScrapers\Facades\Scraper;
use Eelcol\LaravelScrapers\Providers\ScrapingBee;
use Eelcol\LaravelScrapers\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ScraperRequestTest extends TestCase
{
	/** @test */
	public function it_should_call_scrapingbee_url(): void
	{
        Config::set('scraper.providers.scrapingbee.key', '123');

        Http::fake(function ($response) {
            return Http::response('{"body": "html", "initial-status-code": 200}', 200);
        });

        Scraper::get('https://www.nu.nl');

        Http::assertSent(function ($request) {
            return $request->url() == "https://app.scrapingbee.com/api/v1/?api_key=123&render_js=false&country_code=nl&forward_headers=true&json_response=true&url=https%3A%2F%2Fwww.nu.nl";
        });

        Http::fake(function ($response) {
            return Http::response('{"body": "html", "initial-status-code": 200}', 200);
        });

        Scraper::premium()->get('https://www.nu.nl');

        Http::assertSent(function ($request) {
            return $request->url() == "https://app.scrapingbee.com/api/v1/?api_key=123&render_js=false&country_code=nl&forward_headers=true&json_response=true&url=https%3A%2F%2Fwww.nu.nl&premium_proxy=true";
        });
	}
}