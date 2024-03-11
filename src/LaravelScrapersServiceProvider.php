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
        $this->mergeConfigFrom(__DIR__ . '/../config/scraper.php', 'scraper');

        $this->app->bind('scraper', function ($app) {
            return new ScraperManager(config('scraper'));
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        //
    }
}
