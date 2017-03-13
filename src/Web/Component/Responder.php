<?php

namespace Web\Component;

use Aura\Payload_Interface\PayloadInterface;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class Responder
{
    protected $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function invoke(Response $response, PayloadInterface $payload)
    {

        return $this->renderer->render($response, $payload->getExtras()['template'], (array)$payload->getOutput());
    }
}
