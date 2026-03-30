<?php

namespace Eelcol\LaravelScrapers\Tests\Unit;

use Eelcol\LaravelScrapers\Facades\Scraper;
use Eelcol\LaravelScrapers\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ScraperProxyTest extends TestCase
{
	/** @test */
	public function it_should_send_username_password(): void
	{
        $this->prepareForProxy();
        $capturedOptions = null;

        Http::fake(function ($request, $options) use (&$capturedOptions) {
            $capturedOptions = $options;
            return Http::response('{"body": "html", "initial-status-code": 200}', 200);
        });

        Scraper::get('http://httpbin.org/ip');

        $this->assertNotNull($capturedOptions, 'Expected a request to be made');
        $this->assertArrayHasKey('proxy', $capturedOptions);
        $this->assertStringContainsString('laravel-user:some-password', $capturedOptions['proxy']);
	}

    /** @test */
    public function it_should_use_proxy(): void
    {
        $this->prepareForProxy();

        try {
            Scraper::get('http://httpbin.org/ip');
        } catch (RequestException $e) {
            if (str_contains($e, "Could not resolve proxy: 123.457.789")) {
                $correct = true;
            }
        }

        $this->assertTrue(isset($correct));
    }

    protected function prepareForProxy(): void
    {
        Config::set('scraper.current', 'proxy');
        Config::set('scraper.providers.proxy.host', '123.457.789');
        Config::set('scraper.providers.proxy.port', '987');
        Config::set('scraper.providers.proxy.user', 'laravel-user');
        Config::set('scraper.providers.proxy.pass', 'some-password');
    }
}