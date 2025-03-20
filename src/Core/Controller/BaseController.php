<?php

namespace Hertz\ProductService\Core\Controller;

use Hertz\ProductService\Core\Http\Request;
use Hertz\ProductService\Core\Http\Response;
use Throwable;
use Hertz\ProductService\Core\Http\StatusCode;
use Hertz\ProductService\Core\Exception\ActionNotFoundException;

abstract class BaseController
{
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

            // Check if the action method exists
            if (!method_exists($this, $action)) {
                throw new ActionNotFoundException($action);
            }

            // Call the action method
            return $this->$action($request);
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
    abstract protected function indexAction(Request $request): Response;


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

        $response = [
            'error' => true,
            'message' => $message,
            'details' => $e->getMessage()
        ];

        return new Response(json_encode($response), $status);
    }
}