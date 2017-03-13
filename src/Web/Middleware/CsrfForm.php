<?php

namespace Web\Middleware;

use Slim\Csrf\Guard;
use Slim\Http\Request;
use Slim\Http\Response;

class CsrfForm
{
    /**
     * @var Guard
     */
    private $csrf;

    public function __construct(Guard $csrf)
    {
        $this->csrf = $csrf;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $nameKey = $this->csrf->getTokenNameKey();
        $valueKey = $this->csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);
        $request = $request->withAttribute('csrfNameKey', $nameKey)
            ->withAttribute('csrfName', $name)
            ->withAttribute('csrfValueKey', $valueKey)
            ->withAttribute('csrfValue', $value);

        return $next($request, $response);
    }
}
