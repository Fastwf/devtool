<?php

namespace Fastwf\Devtool\Components;

use Fastwf\Core\Components\ExceptionHandler;
use Fastwf\Core\Http\HttpException;
use Fastwf\Core\Http\Frame\HttpResponse;

use Fastwf\Devtool\Formatters\HtmlExceptionFormatter;


/**
 * Global exception handler that help to debug all unhandled exceptions.
 * 
 * According to the application mode (production or not) the exception and stack trace is displayed or not in html page.
 */
class GlobalExceptionHandler implements ExceptionHandler 
{

    private $production;

    public function __construct($modeProduction)
    {
        $this->production = $modeProduction;
    }

    /**
     * {@inheritDoc}
     */
    public function catch($exception, $request, $response)
    {
        if (!($exception instanceof HttpException)) {
            // In all case, debug the stack trace in root logger
            //  TODO: change when logger is implemented in fastwf/core
            $stderr = fopen('php://stderr', 'w');
            \fwrite($stderr, $exception->getMessage() . PHP_EOL);
            \fwrite($stderr, $exception->getTraceAsString() . PHP_EOL);
            \fclose($stderr);
    
            // Build the html response
            if ($this->production === true)
            {
                // Create an empty response 500
                $body = "";
            }
            else
            {
                // Format the stack trace as html page
                $body = (new HtmlExceptionFormatter($exception))->toHtml();
            }
    
            return new HttpResponse(500, [], $body);
        }

        return null;
    }

}
