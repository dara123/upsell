<?php

namespace App\Action;

use Rebilly\Client;
use Rebilly\Entities\PaymentInstruments\PayPalInstrument;
use Rebilly\Entities\PaymentMethod;
use Rebilly\Entities\PaymentMethodInstrument;
use Rebilly\Http\Exception\UnprocessableEntityException;
use Slim\Http\Request;
use Slim\Http\Response;
use Web\Component\Payload;
use Web\Component\Responder;

class AccountAction
{
    private $client;
    private $responder;
    private $baseUrl;

    /**
     * PaymentAction constructor.
     *
     * @param Client $client
     * @param Responder $responder
     * @param string $baseUrl
     */
    public function __construct(Responder $responder, Client $client, $baseUrl)
    {
        $this->responder = $responder;
        $this->client = $client;
        $this->baseUrl = $baseUrl;
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
        if ($request->isPost()) {
            try {
                // Create new customer
                $customer = $this->client->customers()->create([]);
                // Create new contact
                $contact = $this->client->contacts()->create([
                    'customerId' => $customer->getId(),
                ]);

                $payPalParams = [
                    'customerId' => $customer->getId(),
                    'contactId' => $contact->getId(),
                ];
                $paymentInstrument = $this->client->payPalAccounts()->create($payPalParams);

                $activation = [
                    'websiteId' => 'web',
                    'currency' => 'USD',
                    'redirectURLs' => [
                        'success' => $this->baseUrl . '/payment/' . $customer->getId(),
                        'decline' => $this->baseUrl . '/decline',
                        'cancel' => $this->baseUrl . '/cancel',
                        'error' => $this->baseUrl . '/error',
                    ],
                ];
                $paymentInstrument = $this->client->payPalAccounts()->activate($activation, $paymentInstrument->getId());

                $paymentMethodInstrument = PaymentMethodInstrument::createFromData(['method' => PaymentMethod::METHOD_PAYPAL]);
                $paymentMethodInstrument->setPayPalAccountId($paymentInstrument->getId());

                $customer->setDefaultPaymentInstrument($paymentMethodInstrument);
                // update default payment method
                $this->client->customers()->update($customer->getId(), $customer);

                if ($paymentInstrument->getApprovalLink()) {
                    $approvalUrl = $paymentInstrument->getApprovalLink();

                    return $response->withRedirect($approvalUrl);
                }
            } catch (UnprocessableEntityException $e) {
                return $e->getErrors();
            } catch (\Exception $e) {
                return $e->getMessage();
            }

        }

        $template = 'index.phtml';
        $payload = new Payload($request);
        $payload->setExtras(['template' => $template]);
        $payload->addOutput(
            [
                'assetPath' => '/',
            ]
        );

        return $this->responder->invoke($response, $payload);
    }
}
