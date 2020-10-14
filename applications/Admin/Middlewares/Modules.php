<?php

namespace Middlewares\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use System\Base\BaseMiddleware;

class Modules extends BaseMiddleware implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
	{
		// echo 'Modules Middleware <br>';
		//Do Something...
		return $handler->handle($request);

		//OR..

		// invoke the rest of the middleware stack and your controller resulting
		// in a returned response object
		// $response = $handler->handle($request);

		// ...
		// do something with the response
		// return $response;
	}
}