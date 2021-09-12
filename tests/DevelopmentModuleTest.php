<?php

namespace Fastwf\Tests;

use Fastwf\Core\Configuration;
use Fastwf\Core\Components\ExceptionHandler;

use PHPUnit\Framework\TestCase;

use Fastwf\Devtool\DevelopmentModule;

class DevelopmentModuleTest extends TestCase
{

    /**
     * @covers Fastwf\Devtool\DevelopmentModule
     */
    public function testConfigureDevelopment()
    {
        $module = new DevelopmentModule();

        $_ENV['SERVER_MODEPRODUCTION'] = "no";
        $config = new Configuration(__DIR__ . '/../resources/config.ini');

        $module->configure(null, $config);

        $this->assertNotEquals(0, count($module->getRoutes(null)));
    }

    /**
     * @covers Fastwf\Devtool\DevelopmentModule
     */
    public function testConfigureProduction()
    {
        $module = new DevelopmentModule();

        unset($_ENV['SERVER_MODEPRODUCTION']);
        $config = new Configuration(__DIR__ . '/../resources/config.ini');

        $module->configure(null, $config);

        $this->assertEquals(0, count($module->getRoutes(null)));
    }

    /**
     * @covers Fastwf\Devtool\DevelopmentModule
     * @covers Fastwf\Devtool\Components\GlobalExceptionHandler
     */
    public function testExceptionHandler()
    {
        $module = new DevelopmentModule();

        foreach ($module->getExceptionHandlers(null) as $handlers) {
            $this->assertTrue($handlers instanceof ExceptionHandler);
        }
    }

    /**
     * @covers Fastwf\Devtool\DevelopmentModule
     */
    public function testRoutes()
    {
        $module = new DevelopmentModule();

        $_ENV['SERVER_MODEPRODUCTION'] = 'no';
        $config = new Configuration(__DIR__ . '/../resources/config.ini');

        $module->configure(null, $config);

        $this->assertNotNull(
            $module->getRoutes(null)[0]
                ->match("__debug/static/css/style.css", "GET")
        );
    }

}
