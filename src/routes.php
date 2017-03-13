<?php

use App\Action\PaymentAction;

$app->map(['GET', 'POST'], '/', PaymentAction::class);
