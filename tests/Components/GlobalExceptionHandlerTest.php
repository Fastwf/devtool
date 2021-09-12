<?php

namespace Fastwf\Tests\Components;

use PHPUnit\Framework\TestCase;

use Fastwf\Tests\Output\FileOutput;
use Fastwf\Tests\Engine\SimpleEngine;
use Fastwf\Core\Http\NotFoundException;
use Fastwf\Core\Utils\Logging\DefaultLogger;

use Fastwf\Devtool\Components\GlobalExceptionHandler;

class GlobalExceptionHandlerTest extends TestCase
{

    private const OUT_FILENAME = __DIR__ . "/out.txt";

    private $engine;

    protected function setUp(): void
    {
        $this->engine = new SimpleEngine(__DIR__ . '/../configuration.ini');
        $this->engine->registerService('Logger', new DefaultLogger('/dev/null'));
    }

    /**
     * @covers Fastwf\Devtool\Components\GlobalExceptionHandler
     */
    public function testHttpException()
    {
        $handler = new GlobalExceptionHandler($this->engine, false);

        $this->assertNull(
            $handler->catch(
                new NotFoundException("No such file or directory"),
                null,
                null
            )
        );
    }
    
    /**
     * @covers Fastwf\Devtool\Components\GlobalExceptionHandler
     */
    public function testAnyException()
    {
        $handler = new GlobalExceptionHandler($this->engine, true);

        $this->assertEquals(
            500,
            $handler->catch(
                new \ErrorException("No such file or directory"),
                null,
                null
            )->status
        );
    }
    
    /**
     * @covers Fastwf\Devtool\Components\GlobalExceptionHandler
     * @covers Fastwf\Devtool\Formatters\HtmlExceptionFormatter
     */
    public function testAnyExceptionDevelopment()
    {
        $handler = new GlobalExceptionHandler($this->engine, false);

        $output = new FileOutput(self::OUT_FILENAME);

        $handler->catch(
            new \ErrorException("No such file or directory"),
            null,
            null
        )->send($output);

        $this->assertNotEquals(
            0,
            \strlen(
                $output->getResponseContent()
            )
        );
    }

    protected function tearDown(): void
    {
        if (\file_exists(self::OUT_FILENAME))
        {
            \unlink(self::OUT_FILENAME);
        }
    }

}