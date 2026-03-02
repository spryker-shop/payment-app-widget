<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PaymentAppWidget\Dependency\Client;

use Generated\Shared\Transfer\QuoteTransfer;

interface PaymentAppWidgetToQuoteClientInterface
{
    public function getQuote(): QuoteTransfer;

    public function setQuote(QuoteTransfer $quoteTransfer): void;
}
