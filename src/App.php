<?php

namespace Rocket\Task;

use Rocket\Task\Service\RocketRoute;
use Silex\Application;
use Igorw\Silex\ConfigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * Application bootstrap
 */
class App extends Application
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->registerProviders();
        $this->defineServices();
        $this->defineRoutes();
        parent::boot();
    }

    /**
     * Register silex providers
     */
    protected function registerProviders()
    {
        $this->register(new ConfigServiceProvider(__DIR__."/../config.json"));
    }

    /**
     * Service definitions
     */
    protected function defineServices()
    {
        $this['rocketRoute'] = function() {
            return new RocketRoute($this['rocketRouteConfig']);
        };
    }

    /**
     * Route definitions
     */
    protected function defineRoutes()
    {
        $this->get('/', function(Request $request) {
            $icao = $request->get('icao', '');
            if(strlen($icao) != 4) {
                throw new \InvalidArgumentException("ICAO code should have exactly 4 characters");
            }

            /** @var RocketRoute $api */
            $api = $this['rocketRoute'];
            return $this->json($api->searchNotam($icao));
        });
    }
}