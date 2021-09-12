<?php

namespace Fastwf\Devtool;

use Fastwf\Asset\AssetApplication;
use Fastwf\Core\Router\Mount;
use Fastwf\Core\Settings\ConfigurationSettings;
use Fastwf\Core\Settings\ExceptionSettings;
use Fastwf\Core\Settings\RouteSettings;

use Fastwf\Devtool\Components\GlobalExceptionHandler;


/**
 * The last module to include to the engine settings to help to debug the application.
 * 
 * **Configuration**
 * 
 * The key `server.modeProduction` allows to activate or not the module.  
 * When it's set to 'no' the debug system is actvated, else not.
 */
class DevelopmentModule implements ConfigurationSettings, RouteSettings, ExceptionSettings
{

    private $modeProduction;

    /**
     * {@inheritDoc}
     */
    public function configure($engine, $configuration)
    {
        $this->modeProduction = $configuration->getBoolean('server.modeProduction', false);
    }

    /**
     * {@inheritDoc}
     */
    public function getExceptionHandlers($engine)
    {
        return [
            new GlobalExceptionHandler($this->modeProduction),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutes($engine)
    {
        return $this->modeProduction
            ? []
            : [
                new Mount([
                    "path" => "__debug",
                    "routes" => function () {
                        return [
                            new AssetApplication(__DIR__ . '/../static', 'static')
                        ];
                    }
                ]),
            ];
    }

}
