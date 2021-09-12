<?php

namespace Fastwf\Tests\Components;

use PHPUnit\Framework\TestCase;

use Fastwf\Tests\Output\FileOutput;
use Fastwf\Core\Http\NotFoundException;
use Fastwf\Devtool\Components\GlobalExceptionHandler;

class GlobalExceptionHandlerTest extends TestCase
{

    private const OUT_FILENAME = __DIR__ . "/out.txt";

    /**
     * @covers Fastwf\Devtool\Components\GlobalExceptionHandler
     */
    public function testHttpException()
    {
        $handler = new GlobalExceptionHandler(false);

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
        $handler = new GlobalExceptionHandler(true);

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
        $handler = new GlobalExceptionHandler(false);

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