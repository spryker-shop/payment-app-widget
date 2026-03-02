<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PaymentAppWidget\Mapper;

use Generated\Shared\Transfer\InitializePreOrderPaymentRequestTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface PaymentMapperInterface
{
    public function mapToPaymentTransfer(
        InitializePreOrderPaymentRequestTransfer $initializePreOrderPaymentRequestTransfer,
        QuoteTransfer $quoteTransfer,
        PaymentTransfer $paymentTransfer
    ): PaymentTransfer;
}
