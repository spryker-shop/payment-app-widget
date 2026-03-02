<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PaymentAppWidget\Initializer;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\PreOrderPaymentRequestTransfer;
use Generated\Shared\Transfer\PreOrderPaymentResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerShop\Yves\PaymentAppWidget\Dependency\Client\PaymentAppWidgetToPaymentAppClientInterface;

class PreOrderPaymentInitializer implements PreOrderPaymentInitializerInterface
{
    /**
     * @var \SprykerShop\Yves\PaymentAppWidget\Dependency\Client\PaymentAppWidgetToPaymentAppClientInterface
     */
    protected PaymentAppWidgetToPaymentAppClientInterface $paymentAppClient;

    public function __construct(PaymentAppWidgetToPaymentAppClientInterface $paymentAppClient)
    {
        $this->paymentAppClient = $paymentAppClient;
    }

    public function initializePreOrderPayment(
        PaymentTransfer $paymentTransfer,
        QuoteTransfer $quoteTransfer
    ): PreOrderPaymentResponseTransfer {
        $preOrderPaymentRequestTransfer = $this->createPreOrderPaymentRequestTransfer($paymentTransfer, $quoteTransfer);

        return $this->paymentAppClient->initializePreOrderPayment($preOrderPaymentRequestTransfer);
    }

    protected function createPreOrderPaymentRequestTransfer(
        PaymentTransfer $paymentTransfer,
        QuoteTransfer $quoteTransfer
    ): PreOrderPaymentRequestTransfer {
        return (new PreOrderPaymentRequestTransfer())
            ->setQuote($quoteTransfer)
            ->setPayment($paymentTransfer);
    }
}
