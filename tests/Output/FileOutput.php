<?php

namespace Fastwf\Tests\Output;

use Fastwf\Api\Engine\Output\HttpOutputInterface;

/**
 * Output class helper to control the content of the response.
 */
class FileOutput implements HttpOutputInterface
{

    public $filename;
    
    public $status;
    public $headers = [];

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function sendStatus($status)
    {
        $this->status = $status;
    }

    public function sendHeader($header)
    {
        $this->headers[] = $header;
    }

    public function getResponseStream()
    {
        return \fopen($this->filename, 'w');
    }

    public function getResponseContent()
    {
        return \file_get_contents($this->filename);
    }

}