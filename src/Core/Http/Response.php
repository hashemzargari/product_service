<?php

namespace Hertz\ProductService\Core\Http;

use Hertz\ProductService\Core\Schema\Dto;

class Response
{
    private $body;
    private $status;
    private $isBuffered = false;

    private $contentType = 'text/html';

    public function __construct($body, StatusCode $status = StatusCode::OK, ?string $contentType = null)
    {
        $this->body = $body;
        $this->status = $status;
        $this->contentType = $contentType ? $contentType : ($body instanceof Dto ? 'application/json' : 'text/html');
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
        header('Content-Type: ' . $this->contentType);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: true');

        if ($this->body instanceof Dto) {
            if ($this->contentType === 'application/json') {
                echo $this->body->toJson();
            } else {
                echo json_encode($this->body->toArray());
            }
        } else {
            echo $this->body;
        }
    }
}