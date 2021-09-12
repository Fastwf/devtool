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
        function willFail()
        {
            throw new \Exception("Function that always fail!");
        }

        try
        {
            willFail();
        }
        catch (\Exception $e)
        {
            $formatter = new HtmlExceptionFormatter($e);

            $body = $formatter->toHtml();

            $this->assertTrue(\strpos($body, "500 Internal Server Error") !== false);
            $this->assertTrue(\strpos($body, '<span class="function">Fastwf\Tests\Formatters\willFail</span>') !== false);
            $this->assertTrue(\strpos($body, 'line <span class="line">19</span>') !== false);
        }
    }

}