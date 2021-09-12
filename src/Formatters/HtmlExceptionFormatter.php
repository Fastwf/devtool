<?php

namespace Fastwf\Devtool\Formatters;


/**
 * The formatter class that allows to create exception stack trace web page.
 */
class HtmlExceptionFormatter
{

    private $exception;

    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    /// PRIVATE METHODS

    /**
     * Create the <head> tag content as string
     *
     * @return string the head formatted.
     */
    private function getHead()
    {
        return '<meta charset="UTF-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            . '<link rel="stylesheet" href="/__debug/static/css/style.css">'
            . '<title>500 Internal Server Error</title>';
    }

    /**
     * Create the <body> tag content as string
     *
     * @return string the body formatted.
     */
    private function getBody()
    {
        $className = $this->parseExceptionName();

        return '<header class="main-header">'
            . '<h1>500 Internal Server Error</h1>'
            . '</header>'
            . '<section class="container">'
            . '<header class="exception-block">'
            . '<div class="namespace">' . $className["namespace"] . '</div>'
            . '<div class="exception">' . $className["name"] . '</div>'
            . self::getLocation($this->exception->getFile(), $this->exception->getLine())
            . '<div class="message">' . $this->exception->getMessage() . '</div>'
            . '</header>'
            . '</span></div>'
            . $this->getStackTrace()
            . '</section>';
    }

    /**
     * Generate the full stack trace html formatted.
     *
     * @return string the stack trace as html
     */
    private function getStackTrace()
    {
        $traces = "";

        foreach ($this->exception->getTrace() as $trace)
        {
            $traces .= $this->getTrace($trace);
        }

        return $traces;
    }

    /**
     * Parse the exception name to extract it's namespace and class name.
     *
     * @return array an array containing 'namespace' and 'name' keys.
     */
    private function parseExceptionName()
    {
        $groups = [];

        // Do not check the result because the class name respect the following regex
        //  See https://www.php.net/manual/en/language.variables.basics.php for details
        \preg_match(
            '/^(([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*\\\\)+)?([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)/',
            \get_class($this->exception),
            $groups
        );

        return [
            'namespace' => $groups[1] === '' ? '\\' : $groups[1],
            'name' => $groups[3],
        ];
    }

    /// PUBLIC METHODS

    /**
     * Generate the html web page of the stack trace.
     *
     * @return string the html page
     */
    public function toHtml()
    {
        return '<!DOCTYPE html>'
            . '<html lang="en">'
            . '<head>'
            . $this->getHead()
            . '</head>'
            . '<body>'
            . $this->getBody()
            . '</body>'
            . '</html>';
    }

    /// STATIC METHODS

    /**
     * Format the trace entry as html.
     *
     * @param array $trace the trace item from the exception
     * @return string the trace formatted as html
     */
    private static function getTrace($trace)
    {
        return '<div class="trace">'
            . '<div class="call">'
            . self::getCall($trace)
            . '</div>'
            . self::getLocation($trace["file"], $trace["line"])
            . '</div>';
    }

    /**
     * Format the function call from the trace.
     *
     * @param array $trace the trace item from the exceptions
     * @return string the function call formatted as html.
     */
    private static function getCall($trace)
    {
        $call = "";

        if (\array_key_exists('class', $trace))
        {
            $call = '<span class="class">' . $trace['class'] . '</span>'
                . '<span class="type">' . $trace['type'] . '</span>';
            $functionClass = "method";
        }
        else
        {
            $functionClass = "function";
        }

        return $call
            . '<span class="' . $functionClass . '">' . $trace['function'] . '</span>'
            . '<span class="parentheses">(</span>'
            . \join(", ", \array_map('self::formatArg', \array_key_exists('args', $trace) ? $trace['args'] : []))
            . '<span class="parentheses">)</span>';
    }

    /**
     * Format the location in file system using file path and line in file.
     *
     * @param string $file the path to the file in filesystem
     * @param int $line the line in the file corresponding to the function call.
     * @return string the location formatted as html.
     */
    private static function getLocation($file, $line)
    {
        return '<div class="location">in <span class="file-path">'
        . $file
        . '</span> line <span class="line">'
        . $line
        . '</span></div>';
    }

    /**
     * Format arguments as html.
     * 
     * int, double, string, boolean and null are respected and for arrays and any instance of class the representation is simplified.
     *
     * @param mixed $arg
     * @return string the html representation of the argument.
     */
    private static function formatArg($arg)
    {
        $type = \gettype($arg);

        if ($type === 'object')
        {
            $value = 'object(' . \get_class($arg) . ')';
        }
        else if ($type === 'array')
        {
            $value = 'array(' . count($arg) . ')';
        }
        else
        {
            $value = \json_encode($arg);
        }

        return "<span class=\"$type\">$value</span>";
    } 

}
