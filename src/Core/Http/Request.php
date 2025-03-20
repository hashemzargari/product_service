<?php

namespace Hertz\ProductService\Core\Http;

class Request
{
    private $path;
    private $method;
    private $params;
    private $headers;
    private $body;

    public function __construct()
    {
        $this->path = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = $this->getHeaders();
        $this->params = $this->getQueryParams();
        $this->body = $this->getRequestBody();
    }

    /**
     * Get the request path
     */
    public function getPath(bool $withQuery = false): string
    {
        if ($withQuery) {
            return $this->path;
        }

        return explode('?', $this->path)[0];
    }

    /**
     * Get the request method (GET, POST, etc.)
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get all request headers
     */
    private function getHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    /**
     * Get a specific header value
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * Get all query parameters
     */
    public function getQueryParams(): array
    {
        return $_GET;
    }

    /**
     * Get a specific query parameter
     */
    public function getQuery(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Get request body data
     */
    private function getRequestBody(): array
    {
        $body = file_get_contents('php://input');
        if (empty($body)) {
            return $_POST;
        }

        $contentType = $this->getHeader('Content-Type');
        if (strpos($contentType, 'application/json') !== false) {
            return json_decode($body, true) ?? [];
        }

        return $_POST;
    }

    /**
     * Get a specific body parameter
     */
    public function getBody(string $key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * Validate if request is AJAX
     */
    public function isAjax(): bool
    {
        return isset($this->headers['X-Requested-With']) &&
            strtolower($this->headers['X-Requested-With']) === 'xmlhttprequest';
    }

    /**
     * Validate if request is secure (HTTPS)
     */
    public function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            $_SERVER['SERVER_PORT'] == 443;
    }

    /**
     * Get client IP address
     */
    public function getClientIp(): string
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    /**
     * Validate if request has specific content type
     */
    public function hasContentType(string $type): bool
    {
        $contentType = $this->getHeader('Content-Type');
        return strpos($contentType, $type) !== false;
    }

    /**
     * Validate if request has specific parameter
     */
    public function has(string $key, string $type = 'any'): bool
    {
        $value = $this->getQuery($key) ?? $this->getBody($key);

        if ($value === null) {
            return false;
        }

        switch ($type) {
            case 'string':
                return is_string($value);
            case 'int':
            case 'integer':
                return is_numeric($value) && (int) $value == $value;
            case 'float':
                return is_numeric($value);
            case 'array':
                return is_array($value);
            case 'bool':
            case 'boolean':
                return is_bool($value);
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            default:
                return true;
        }
    }
}