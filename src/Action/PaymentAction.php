<?php

namespace App\Action;

use Rebilly\Client;
use Rebilly\Entities\PaymentMethod;
use Slim\Http\Request;
use Slim\Http\Response;
use Web\Component\Responder;

class PaymentAction
{
    private $client;
    private $responder;

    /**
     * PaymentAction constructor.
     * @param Client $client
     * @param Responder $responder
     */
    public function __construct(Responder $responder, Client $client)
    {
        $this->responder = $responder;
        $this->client = $client;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $customerId = $request->getAttribute('customerId');
        $customer = $this->client->customers()->load($customerId);

        $paymentForm = [
            'websiteId' => 'web',
            'customerId' => $customer->getId(),
            'currency' => 'USD',
            'amount' => 9.95,
            'description' => 'test 1 click upsell PayPal',
            'method' => PaymentMethod::METHOD_PAYPAL,
            'paymentInstrument' => [
                'payPalAccountId' => $customer->getDefaultPaymentInstrument()->getPayPalAccountId(),
            ],
        ];

        $payment = $this->client->payments()->create($paymentForm);

        if ($payment->getResult() === 'approved') {

        }
    }
}
