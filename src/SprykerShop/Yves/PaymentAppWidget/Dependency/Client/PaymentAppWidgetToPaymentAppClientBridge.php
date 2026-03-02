<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PaymentAppWidget\Dependency\Client;

use Generated\Shared\Transfer\ExpressCheckoutPaymentRequestTransfer;
use Generated\Shared\Transfer\ExpressCheckoutPaymentResponseTransfer;
use Generated\Shared\Transfer\PaymentCustomerRequestTransfer;
use Generated\Shared\Transfer\PaymentCustomerResponseTransfer;
use Generated\Shared\Transfer\PreOrderPaymentRequestTransfer;
use Generated\Shared\Transfer\PreOrderPaymentResponseTransfer;

class PaymentAppWidgetToPaymentAppClientBridge implements PaymentAppWidgetToPaymentAppClientInterface
{
    /**
     * @var \Spryker\Client\PaymentApp\PaymentAppClientInterface
     */
    protected $paymentAppClient;

    /**
     * @param \Spryker\Client\PaymentApp\PaymentAppClientInterface $paymentAppClient
     */
    public function __construct($paymentAppClient)
    {
        $this->paymentAppClient = $paymentAppClient;
    }

    public function processExpressCheckoutPaymentRequest(
        ExpressCheckoutPaymentRequestTransfer $expressCheckoutPaymentRequestTransfer
    ): ExpressCheckoutPaymentResponseTransfer {
        return $this->paymentAppClient->processExpressCheckoutPaymentRequest($expressCheckoutPaymentRequestTransfer);
    }

    public function getCustomer(
        PaymentCustomerRequestTransfer $paymentCustomerRequestTransfer
    ): PaymentCustomerResponseTransfer {
        return $this->paymentAppClient->getCustomer($paymentCustomerRequestTransfer);
    }

    public function initializePreOrderPayment(PreOrderPaymentRequestTransfer $preOrderPaymentRequestTransfer): PreOrderPaymentResponseTransfer
    {
        return $this->paymentAppClient->initializePreOrderPayment($preOrderPaymentRequestTransfer);
    }

    public function cancelPreOrderPayment(PreOrderPaymentRequestTransfer $preOrderPaymentRequestTransfer): PreOrderPaymentResponseTransfer
    {
        return $this->paymentAppClient->cancelPreOrderPayment($preOrderPaymentRequestTransfer);
    }
}
