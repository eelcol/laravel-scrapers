<?php

namespace Eelcol\LaravelScrapers\Tests\Unit;

use Eelcol\LaravelScrapers\Exceptions\ScraperProviderNotFound;
use Eelcol\LaravelScrapers\Facades\Scraper;
use Eelcol\LaravelScrapers\Providers\HttpApi;
use Eelcol\LaravelScrapers\Providers\ScraperApi;
use Eelcol\LaravelScrapers\Providers\ScrapingBee;
use Eelcol\LaravelScrapers\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class ScraperResolveTest extends TestCase
{
	/** @test */
	public function it_should_resolve_scrapingbee_as_default(): void
	{
        $this->assertEquals(
            get_class(Scraper::resolve()),
            ScrapingBee::class
        );
	}

    /** @test */
    public function it_should_resolve_scraperapi_from_config(): void
    {
        Config::set('scraper.current', 'scraperapi');

        $this->assertEquals(
            get_class(Scraper::resolve()),
            ScraperApi::class
        );
    }

    /** @test */
    public function it_should_resolve_scrapingbee_from_config(): void
    {
        Config::set('scraper.current', 'scrapingbee');

        $this->assertEquals(
            get_class(Scraper::resolve()),
            ScrapingBee::class
        );
    }

    /** @test */
    public function it_should_resolve_http_api_class_when_testing(): void
    {
        Config::set('scraper.current', 'scrapingbee');
        Scraper::test();

        $this->assertEquals(
            get_class(Scraper::resolve()),
            HttpApi::class,
        );
    }

    /** @test */
    public function it_should_throw_an_error_with_an_unknown_provider(): void
    {
        Config::set('scraper.current', 'non-existing');

        $this->expectException(ScraperProviderNotFound::class);

        Scraper::resolve();
    }
}