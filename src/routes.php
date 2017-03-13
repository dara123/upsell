<?php

use App\Action\AccountAction;
use App\Action\PaymentAction;
use App\Action\UpsellAction;

$app->map(['GET', 'POST'], '/', AccountAction::class);

$app->map(['GET', 'POST'], '/payment/{customerId}', PaymentAction::class);
$app->map(['GET', 'POST'], '/upsell/{customerId}', UpsellAction::class);
