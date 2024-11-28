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

    /**
     * @param \Generated\Shared\Transfer\ExpressCheckoutPaymentRequestTransfer $expressCheckoutPaymentRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ExpressCheckoutPaymentResponseTransfer
     */
    public function processExpressCheckoutPaymentRequest(
        ExpressCheckoutPaymentRequestTransfer $expressCheckoutPaymentRequestTransfer
    ): ExpressCheckoutPaymentResponseTransfer {
        return $this->paymentAppClient->processExpressCheckoutPaymentRequest($expressCheckoutPaymentRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentCustomerRequestTransfer $paymentCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentCustomerResponseTransfer
     */
    public function getCustomer(
        PaymentCustomerRequestTransfer $paymentCustomerRequestTransfer
    ): PaymentCustomerResponseTransfer {
        return $this->paymentAppClient->getCustomer($paymentCustomerRequestTransfer);
    }
}