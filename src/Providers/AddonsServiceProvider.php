<?php

namespace JuCloud\Core\Providers;

use Illuminate\Contracts\Foundation\Application;
use JuCloud\Core\Addons;

class AddonsServiceProvider
{
    /**
     * @param Application $app
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function bootstrap(Application $app)
    {
        $addons = new Addons();
        $providers = $addons->getProvidersMap();
        if ($providers) {
            array_map(function ($provider) use ($app) {
                $app->register($provider);
            }, $providers);
        }
    }
}
