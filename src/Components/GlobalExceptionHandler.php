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

    protected $engine;
    protected $production;

    public function __construct($engine, $modeProduction)
    {
        $this->engine = $engine;

        $this->production = $modeProduction;
    }

    /**
     * {@inheritDoc}
     */
    public function catch($exception, $request, $response)
    {
        if (!($exception instanceof HttpException)) {
            // In all case, debug the stack trace in root logger
            $this->engine
                ->getService('Logger')
                ->critical($exception->getMessage(), ['exception' => $exception]);

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
