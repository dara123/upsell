<?php

namespace App\Action;

use Faker\Factory as FakerFactory;
use Rebilly\Client;
use Rebilly\Entities\PaymentMethod;
use Rebilly\Entities\PaymentMethodInstrument;
use Rebilly\Http\Exception\UnprocessableEntityException;
use Slim\Http\Request;
use Slim\Http\Response;
use Web\Component\Payload;
use Web\Component\Responder;

class PaymentAction
{
    private $client;
    private $responder;
    private $templatePath;

    /**
     * PaymentAction constructor.
     * @param Client $client
     * @param Responder $responder
     */
    public function __construct(Responder $responder, Client $client, $templatePath)
    {
        $this->responder = $responder;
        $this->client = $client;
        $this->templatePath = $templatePath;
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
        $faker = FakerFactory::create();
        $result = [];
        if ($request->isPost()) {
            try {
                // Create new customer
                $customer = $this->client->customers()->create([]);
                // Create new contact
                $contact = $this->client->contacts()->create([]);

                $payPalParams = [
                    'customerId' => $customer->getId(),
                    'contactId' => $contact->getId(),
                ];
                $paymentInstrument = $this->client->payPalAccounts()->create($payPalParams);

                $activation = [
                    'websiteId' => 'web',
                    'customerId' => $customer->getId(),
                    'currency' => 'USD',
                    'redirectURLs' => [
                        'success' => 'google.com',
                        'decline' => 'yahoo.com',
                        'cancel' => 'yahoo.com',
                        'error' => 'yahoo.com',
                    ],
                ];
                $paymentInstrument = $this->client->payPalAccounts()->activate($activation, $paymentInstrument->getId());

                if ($paymentInstrument->getStatus() === 'inactive') {
                    $message = 'There was an error when activating your PayPal account.';

                    return $result;
                }

                if ($paymentInstrument->getApprovalLink()) {
                    $approvalUrl = $paymentInstrument->getApprovalLink();
                } else {
                    return $result;
                }
                $paymentMethodInstrument = PaymentMethodInstrument::createFromData(['method' => PaymentMethod::METHOD_PAYPAL]);
                $paymentMethodInstrument->setPayPalAccountId($paymentInstrument->getId());

                $customer->setDefaultPaymentInstrument($paymentMethodInstrument);
                // update default payment method
                $this->client->customers()->update($customer->getId(), $customer);

                $paymentForm = [
                    'websiteId' => 'web',
                    'customerId' => $customer->getId(),
                    'currency' => 'USD',
                    'amount' => 5,
                    'description' => 'test 1 click upsell PayPal',
                    'method' => PaymentMethod::METHOD_PAYPAL,
                    'paymentInstrument' => [
                        'payPalAccountId' => $paymentInstrument->getId(),
                    ],
                    'billingContactId' => 'string',
                ];

                $payment = $this->client->payments()->create($paymentForm);

                if ($payment->getResult() === 'approved') {
                    echo 'approved';
                    die;
                }

                return $result;
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
                'faker' => $faker,
                'assetPath' => '/',
            ]
        );

        return $this->responder->invoke($response, $payload);
    }
}
