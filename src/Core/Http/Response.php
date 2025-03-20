<?php

namespace Hertz\ProductService\Core\Http;

class Response
{
    private $body;
    private $status;
    private $isBuffered = false;

    public function __construct($body, StatusCode $status = StatusCode::OK)
    {
        $this->body = $body;
        $this->status = $status;
    }

    public function buffer()
    {
        if (!$this->isBuffered) {
            ob_start();
            $this->isBuffered = true;
        }
    }

    private function flush()
    {
        if ($this->isBuffered) {
            $this->body = ob_get_clean();
            $this->isBuffered = false;
        }
    }

    public function send()
    {
        if ($this->isBuffered) {
            $this->flush();
        }
        http_response_code($this->status->value);
        echo $this->body;
    }
}