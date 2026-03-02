<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PaymentAppWidget\Expander;

use Generated\Shared\Transfer\ExpressCheckoutPaymentMethodWidgetTransfer;
use Generated\Shared\Transfer\ScriptTransfer;

interface ScriptQueryStringExpanderInterface
{
    public function expandScriptQueryStringParameters(
        ScriptTransfer $scriptTransfer,
        ExpressCheckoutPaymentMethodWidgetTransfer $expressCheckoutPaymentMethodWidgetTransfer,
        string $appPaymentMethodKey
    ): ScriptTransfer;
}
