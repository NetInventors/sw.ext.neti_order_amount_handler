<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     sbrueggenolte
 */

namespace NetiOrderAmountHandler;

use NetiOrderAmountHandler\Components\Setup;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

/**
 * Class NetiOrderAmountHandler
 *
 * @package NetiOrderAmountHandler
 */
class NetiOrderAmountHandler extends Plugin
{
    /**
     * @var Setup
     */
    private $setup;

    /**
     * @param InstallContext $context
     * @throws \Exception
     */
    public function install(InstallContext $context)
    {
        $this->setup()->addPaymentMethod();
    }

    /**
     * @param UpdateContext $context
     * @throws \Exception
     */
    public function update(UpdateContext $context)
    {
        $this->setup()->addPaymentMethod();

        $context->scheduleClearCache(UpdateContext::CACHE_LIST_ALL);
    }

    /**
     * @param ActivateContext $context
     * @throws \Exception
     */
    public function activate(ActivateContext $context)
    {
        $this->setup()->setPaymentMethodActivation(true);

        $context->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
    }

    /**
     * @param DeactivateContext $context
     * @throws \Exception
     */
    public function deactivate(DeactivateContext $context)
    {
        $this->setup()->setPaymentMethodActivation(false);

        $context->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }

    /**
     * @param UninstallContext $context
     * @throws \Exception
     */
    public function uninstall(UninstallContext $context)
    {
        $this->setup()->setPaymentMethodActivation(false);

        $context->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
    }

    /**
     * @return Setup
     */
    private function setup()
    {
        if (!$this->setup instanceof Setup) {
            $this->setup = new Setup($this->container);
        }

        return $this->setup;
    }
}
