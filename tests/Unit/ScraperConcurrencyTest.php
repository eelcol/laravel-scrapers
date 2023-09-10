<?php

namespace Eelcol\LaravelScrapers\Tests\Unit;

use Eelcol\LaravelScrapers\Facades\Scraper;
use Eelcol\LaravelScrapers\Providers\ScrapingBee;
use Eelcol\LaravelScrapers\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ScraperConcurrencyTest extends TestCase
{
	/** @test */
	public function it_should_wait_before_locks_are_freed(): void
	{
        Config::set('scraper.concurrency', 1);
        Http::fake(function ($response) {
            return Http::response(microtime(true), 200);
        });

        $microtime_plus_5 = microtime(true) + 5;
        Cache::shouldReceive('get')->once()->andReturn([$microtime_plus_5]);
        Cache::shouldReceive('put')->once();

        $microtime_now = microtime(true);
        $microtime_from_test = Scraper::get('https://www.nu.nl')->body();
        $time_spend = $microtime_from_test - $microtime_now;

        $this->assertTrue($time_spend > 45 && $time_spend < 46);
	}
}