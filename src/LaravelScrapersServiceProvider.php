<?php 

namespace Eelcol\LaravelScrapers;

use Eelcol\LaravelScrapers\Support\ScraperManager;
use Illuminate\Routing\Router;

class LaravelScrapersServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('scraper', function ($app) {
            return new ScraperManager(config('scraper'));
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/scraper.php', 'scraper');
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->publishes([
            __DIR__.'/../config/scraper.php'   => config_path('scraper.php'),
        ], 'laravel-scraper');
    }
}
