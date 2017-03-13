<?php

namespace Web\Component;

use Aura\Payload\Payload as AuraPayload;
use Slim\Http\Request;

class Payload extends AuraPayload
{
    public function __construct(Request $request)
    {
        $this->setOutput(
            [
                'csrfName' => $request->getAttribute('csrfName'),
                'csrfNameKey' => $request->getAttribute('csrfNameKey'),
                'csrfValue' => $request->getAttribute('csrfValue'),
                'csrfValueKey' => $request->getAttribute('csrfValueKey'),
                'assetPath' => $request->getAttribute('assetPath'),
                'auth' => $request->getAttribute('auth'),
                'rebillyJsUrl' => $request->getAttribute('rebillyJsUrl'),
            ]
        );
    }

    public function addOutput(array $output)
    {
        $this->setOutput(array_merge($this->getOutput(), $output));

        return $this;
    }
}
