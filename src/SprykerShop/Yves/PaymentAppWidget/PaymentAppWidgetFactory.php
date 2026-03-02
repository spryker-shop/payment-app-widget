<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\PaymentAppWidget;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;
use SprykerShop\Yves\PaymentAppWidget\Checker\ExpressCheckoutPaymentChecker;
use SprykerShop\Yves\PaymentAppWidget\Checker\ExpressCheckoutPaymentCheckerInterface;
use SprykerShop\Yves\PaymentAppWidget\Dependency\Client\PaymentAppWidgetToLocaleClientInterface;
use SprykerShop\Yves\PaymentAppWidget\Dependency\Client\PaymentAppWidgetToPaymentAppClientInterface;
use SprykerShop\Yves\PaymentAppWidget\Dependency\Client\PaymentAppWidgetToPaymentClientInterface;
use SprykerShop\Yves\PaymentAppWidget\Dependency\Client\PaymentAppWidgetToQuoteClientInterface;
use SprykerShop\Yves\PaymentAppWidget\Dependency\Client\PaymentAppWidgetToSalesClientInterface;
use SprykerShop\Yves\PaymentAppWidget\Expander\CurrencyScriptQueryStringParameterExpander;
use SprykerShop\Yves\PaymentAppWidget\Expander\LocaleScriptQueryStringParameterExpander;
use SprykerShop\Yves\PaymentAppWidget\Expander\QuoteCustomerExpander;
use SprykerShop\Yves\PaymentAppWidget\Expander\QuoteCustomerExpanderInterface;
use SprykerShop\Yves\PaymentAppWidget\Expander\ScriptQueryStringExpander;
use SprykerShop\Yves\PaymentAppWidget\Expander\ScriptQueryStringExpanderInterface;
use SprykerShop\Yves\PaymentAppWidget\Expander\ScriptQueryStringParameterExpanderInterface;
use SprykerShop\Yves\PaymentAppWidget\Form\DataProvider\ExpressCheckoutFormDataProvider;
use SprykerShop\Yves\PaymentAppWidget\Form\ExpressCheckoutForm;
use SprykerShop\Yves\PaymentAppWidget\Form\Transformer\ArrayToInitializePreOrderPaymentRequestTransferTransformer;
use SprykerShop\Yves\PaymentAppWidget\Generator\ExpressCheckoutRedirectGenerator;
use SprykerShop\Yves\PaymentAppWidget\Initializer\PreOrderPaymentInitializer;
use SprykerShop\Yves\PaymentAppWidget\Initializer\PreOrderPaymentInitializerInterface;
use SprykerShop\Yves\PaymentAppWidget\Mapper\PaymentMapper;
use SprykerShop\Yves\PaymentAppWidget\Mapper\PaymentMapperInterface;
use SprykerShop\Yves\PaymentAppWidget\Order\Order;
use SprykerShop\Yves\PaymentAppWidget\Order\OrderInterface;
use SprykerShop\Yves\PaymentAppWidget\Reader\ExpressCheckoutPaymentMethodWidgetReader;
use SprykerShop\Yves\PaymentAppWidget\Reader\ExpressCheckoutPaymentMethodWidgetReaderInterface;
use SprykerShop\Yves\PaymentAppWidget\Reader\PaymentMethodScriptReader;
use SprykerShop\Yves\PaymentAppWidget\Reader\PaymentMethodScriptReaderInterface;
use SprykerShop\Yves\PaymentAppWidget\Reader\RequestRouteReader;
use SprykerShop\Yves\PaymentAppWidget\Reader\RequestRouteReaderInterface;
use SprykerShop\Yves\PaymentAppWidget\Resolver\CheckoutStepResolver;
use SprykerShop\Yves\PaymentAppWidget\Resolver\CheckoutStepResolverInterface;
use SprykerShop\Yves\PaymentAppWidget\Resolver\ExpressCheckoutPaymentMethodWidgetResolver;
use SprykerShop\Yves\PaymentAppWidget\Resolver\ExpressCheckoutPaymentMethodWidgetResolverInterface;
use SprykerShop\Yves\PaymentAppWidget\Strategy\PayPalExpressCheckoutPaymentWidgetRenderStrategy;
use SprykerShop\Yves\PaymentAppWidget\Updater\QuoteUpdater;
use SprykerShop\Yves\PaymentAppWidget\Updater\QuoteUpdaterInterface;
use SprykerShop\Yves\PaymentAppWidgetExtension\Dependency\Plugin\ExpressCheckoutPaymentWidgetRenderStrategyPluginInterface;
use Symfony\Cmf\Component\Routing\ChainRouterInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @method \SprykerShop\Yves\PaymentAppWidget\PaymentAppWidgetConfig getConfig()
 */
class PaymentAppWidgetFactory extends AbstractFactory
{
    /**
     * @param array<string, mixed>|null $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getExpressCheckoutForm(?array $data = null, array $options = []): FormInterface
    {
        return $this->getFormFactory()->create(ExpressCheckoutForm::class, $data, $options);
    }

    public function createExpressCheckoutFormDataProvider(): ExpressCheckoutFormDataProvider
    {
        return new ExpressCheckoutFormDataProvider();
    }

    /**
     * @return \Symfony\Component\Form\DataTransformerInterface<mixed, \Generated\Shared\Transfer\InitializePreOrderPaymentRequestTransfer>
     */
    public function createArrayToInitializePreOrderPaymentRequestTransferTransformer(): DataTransformerInterface
    {
        return new ArrayToInitializePreOrderPaymentRequestTransferTransformer();
    }

    public function createPaymentMapper(): PaymentMapperInterface
    {
        return new PaymentMapper();
    }

    public function createQuoteUpdater(): QuoteUpdaterInterface
    {
        return new QuoteUpdater($this->getQuoteClient());
    }

    public function createPreOrderPaymentInitializer(): PreOrderPaymentInitializerInterface
    {
        return new PreOrderPaymentInitializer($this->getPaymentAppClient());
    }

    public function getExpressCheckoutPaymentMethodWidgetResolver(): ExpressCheckoutPaymentMethodWidgetResolverInterface
    {
        return new ExpressCheckoutPaymentMethodWidgetResolver(
            $this->getPaymentClient(),
            $this->getQuoteClient(),
            $this->createExpressCheckoutRedirectGenerator(),
            $this->createExpressCheckoutPaymentMethodWidgetReader(),
        );
    }

    public function createExpressCheckoutPaymentMethodWidgetReader(): ExpressCheckoutPaymentMethodWidgetReaderInterface
    {
        return new ExpressCheckoutPaymentMethodWidgetReader(
            array_merge(
                $this->getCoreExpressCheckoutPaymentWidgetRenderStrategies(),
                $this->getExpressCheckoutPaymentWidgetRenderStrategyPlugins(),
            ),
            $this->getCsrfTokenManager(),
        );
    }

    /**
     * @return list<\SprykerShop\Yves\PaymentAppWidgetExtension\Dependency\Plugin\ExpressCheckoutPaymentWidgetRenderStrategyPluginInterface>
     */
    public function getCoreExpressCheckoutPaymentWidgetRenderStrategies(): array
    {
        return [
            $this->createPayPalExpressCheckoutPaymentWidgetRenderStrategy(),
        ];
    }

    public function createExpressCheckoutRedirectGenerator(): ExpressCheckoutRedirectGenerator
    {
        return new ExpressCheckoutRedirectGenerator(
            $this->getRouter(),
            $this->getConfig(),
        );
    }

    public function createScriptQueryStringExpander(): ScriptQueryStringExpanderInterface
    {
        return new ScriptQueryStringExpander(
            $this->getConfig(),
            $this->getScriptQueryStringParameterExpanders(),
        );
    }

    public function createPaymentMethodScriptReader(): PaymentMethodScriptReaderInterface
    {
        return new PaymentMethodScriptReader(
            $this->getConfig(),
            $this->createScriptQueryStringExpander(),
        );
    }

    public function createPayPalExpressCheckoutPaymentWidgetRenderStrategy(): ExpressCheckoutPaymentWidgetRenderStrategyPluginInterface
    {
        return new PayPalExpressCheckoutPaymentWidgetRenderStrategy(
            $this->getConfig(),
            $this->createPaymentMethodScriptReader(),
        );
    }

    public function createCurrencyScriptQueryStringParameterExpander(): ScriptQueryStringParameterExpanderInterface
    {
        return new CurrencyScriptQueryStringParameterExpander();
    }

    public function createLocaleScriptQueryStringParameterExpander(): ScriptQueryStringParameterExpanderInterface
    {
        return new LocaleScriptQueryStringParameterExpander($this->getLocaleClient());
    }

    /**
     * @return array<\SprykerShop\Yves\PaymentAppWidget\Expander\ScriptQueryStringParameterExpanderInterface>
     */
    public function getScriptQueryStringParameterExpanders(): array
    {
        return [
            $this->createCurrencyScriptQueryStringParameterExpander(),
            $this->createLocaleScriptQueryStringParameterExpander(),
        ];
    }

    public function createQuoteCustomerExpander(): QuoteCustomerExpanderInterface
    {
        return new QuoteCustomerExpander();
    }

    public function createCheckoutStepResolver(): CheckoutStepResolverInterface
    {
        return new CheckoutStepResolver($this->getConfig(), $this->createRequestRouteReader());
    }

    public function createExpressCheckoutPaymentChecker(): ExpressCheckoutPaymentCheckerInterface
    {
        return new ExpressCheckoutPaymentChecker();
    }

    public function createRequestRouteReader(): RequestRouteReaderInterface
    {
        return new RequestRouteReader($this->getRequestStack());
    }

    /**
     * @return list<\SprykerShop\Yves\PaymentAppWidgetExtension\Dependency\Plugin\ExpressCheckoutPaymentWidgetRenderStrategyPluginInterface>
     */
    public function getExpressCheckoutPaymentWidgetRenderStrategyPlugins(): array
    {
        return $this->getProvidedDependency(PaymentAppWidgetDependencyProvider::PLUGIN_EXPRESS_CHECKOUT_PAYMENT_WIDGET_RENDER_STRATEGY);
    }

    public function getSalesClient(): PaymentAppWidgetToSalesClientInterface
    {
        return $this->getProvidedDependency(PaymentAppWidgetDependencyProvider::CLIENT_SALES);
    }

    public function getQuoteClient(): PaymentAppWidgetToQuoteClientInterface
    {
        return $this->getProvidedDependency(PaymentAppWidgetDependencyProvider::CLIENT_QUOTE);
    }

    public function getPaymentClient(): PaymentAppWidgetToPaymentClientInterface
    {
        return $this->getProvidedDependency(PaymentAppWidgetDependencyProvider::CLIENT_PAYMENT);
    }

    public function getPaymentAppClient(): PaymentAppWidgetToPaymentAppClientInterface
    {
        return $this->getProvidedDependency(PaymentAppWidgetDependencyProvider::CLIENT_PAYMENT_APP);
    }

    public function getRouter(): ChainRouterInterface
    {
        return $this->getProvidedDependency(PaymentAppWidgetDependencyProvider::SERVICE_ROUTER);
    }

    public function getCsrfTokenManager(): CsrfTokenManagerInterface
    {
        return $this->getProvidedDependency(PaymentAppWidgetDependencyProvider::SERVICE_FORM_CSRF_PROVIDER);
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    public function getRequestStack(): RequestStack
    {
        return $this->getProvidedDependency(PaymentAppWidgetDependencyProvider::SERVICE_REQUEST_STACK);
    }

    public function getLocaleClient(): PaymentAppWidgetToLocaleClientInterface
    {
        return $this->getProvidedDependency(PaymentAppWidgetDependencyProvider::CLIENT_LOCALE);
    }

    public function createOrder(): OrderInterface
    {
        return new Order($this->getSalesClient());
    }
}
