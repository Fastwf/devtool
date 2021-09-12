<?php

namespace Fastwf\Tests\Formatters;

use PHPUnit\Framework\TestCase;

use Fastwf\Devtool\Formatters\HtmlExceptionFormatter;

class HtmlExceptionFormatterTest extends TestCase
{

    /**
     * @covers Fastwf\Devtool\Formatters\HtmlExceptionFormatter
     */
    public function testToHtml()
    {
        function willFail($argForCoverage)
        {
            throw new \Exception("Function that always fail ($argForCoverage)!");
        }

        try
        {
            willFail("test");
        }
        catch (\Exception $e)
        {
            $formatter = new HtmlExceptionFormatter($e);

            $body = $formatter->toHtml();

            $this->assertNotSame(false, \strpos($body, "500 Internal Server Error"));
            $this->assertNotSame(false, \strpos($body, '<span class="function">Fastwf\Tests\Formatters\willFail</span>'));
            $this->assertNotSame(false, \strpos($body, 'line <span class="line">19</span>'));
        }
    }

}