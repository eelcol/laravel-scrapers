<?php

namespace Eelcol\LaravelScrapers\Tests\Unit;

use Eelcol\LaravelScrapers\Facades\Scraper;
use Eelcol\LaravelScrapers\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ScraperProxyTest extends TestCase
{
	/** @test */
	public function it_should_send_username_password(): void
	{
        $this->prepareForProxy();
        $expected_auth_header = base64_encode("laravel-user:some-password");

        Http::fake(function () {
            return Http::response('{"body": "html", "initial-status-code": 200}', 200);
        });

        Scraper::get('http://httpbin.org/ip');

        // check if username and password are sent
        Http::assertSent(function (Request $request) use ($expected_auth_header) {
            return $request->header('Authorization') == ["Basic " . $expected_auth_header];
        });
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