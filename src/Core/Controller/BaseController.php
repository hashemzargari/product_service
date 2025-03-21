<?php

namespace Hertz\ProductService\Core\Controller;

use Throwable;
use Hertz\ProductService\Core\Http\Request;
use Hertz\ProductService\Core\Http\Response;
use Hertz\ProductService\Core\Http\StatusCode;
use Hertz\ProductService\Core\Exception\ActionNotFoundException;
use Hertz\ProductService\Core\Schema\Dto;
use Hertz\ProductService\Core\Logger\Logger;

abstract class BaseController
{
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * Handle the incoming request and return a response
     * 
     * @param Request $request The incoming request
     * @return Response The response to send back
     */
    public function handleRequest(Request $request): Response
    {
        try {
            // Get the action method name from the route
            $action = $request->getQuery('action', 'index') . 'Action';

            $this->logger->debug('Processing controller action', [
                'controller' => get_class($this),
                'action' => $action,
                'method' => $request->getMethod(),
                'path' => $request->getPath()
            ]);

            // Check if the action method exists
            if (!method_exists($this, $action)) {
                $this->logger->warning('Action method not found', [
                    'controller' => get_class($this),
                    'action' => $action
                ]);
                throw new ActionNotFoundException($action);
            }

            // Call the action method
            $response = $this->$action($request);
            if ($response instanceof Response) {
                $this->logger->info('Action completed successfully', [
                    'controller' => get_class($this),
                    'action' => $action,
                    'status' => $response->getStatusCode()
                ]);
                return $response;
            }
            return new Response($response);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Default index action that should be overridden by child classes
     * 
     * @param Request $request The incoming request
     * @return Response The response to send back
     */
    abstract protected function indexAction(Request $request): Response|Dto|null;

    /**
     * Handle any exceptions that occur during request processing
     * 
     * @param Throwable $e The exception that was thrown
     * @return Response A response with appropriate error status and message
     */
    protected function handleException(Throwable $e): Response
    {
        $status = StatusCode::INTERNAL_SERVER_ERROR;
        $message = 'Internal Server Error';

        if ($e instanceof \InvalidArgumentException) {
            $status = StatusCode::BAD_REQUEST;
            $message = 'Bad Request';
        } elseif ($e instanceof \RuntimeException) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
            $message = 'Internal Server Error';
        } elseif ($e instanceof ActionNotFoundException) {
            $status = StatusCode::NOT_FOUND;
            $message = 'Action not found';
        } elseif ($e instanceof \Exception) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
            $message = 'Internal Server Error';
        }

        $this->logger->error('Exception occurred during request processing', [
            'controller' => get_class($this),
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        $response = [
            'error' => true,
            'message' => $message,
            'details' => $e->getMessage()
        ];

        return new Response(json_encode($response), $status);
    }
}