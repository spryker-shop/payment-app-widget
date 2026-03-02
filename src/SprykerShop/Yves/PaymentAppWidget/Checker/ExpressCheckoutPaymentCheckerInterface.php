<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PaymentAppWidget\Checker;

use Generated\Shared\Transfer\QuoteTransfer;

interface ExpressCheckoutPaymentCheckerInterface
{
    public function isExpressCheckoutPayment(QuoteTransfer $quoteTransfer): bool;
}
