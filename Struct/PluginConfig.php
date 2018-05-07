<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     sbrueggenolte
 */

namespace NetiOrderAmountHandler\Struct;

use NetiFoundation\Struct\AbstractClass;

/**
 * Class PluginConfig
 *
 * @package NetiOrderAmountHandler\Struct
 */
class PluginConfig extends AbstractClass
{
    /**
     * @var bool - Set "free of charge" orders automatically to the payment status "completely paid".
     */
    protected $setStatusCompletelyPaidForZeroAmountOrders = true;

    /**
     * @var bool - Automatically use "free of charge" payment method for orders with an amount of 0.
     */
    protected $useOwnPaymentMethodForZeroAmountOrders = true;

    /**
     * @return bool
     */
    public function isSetStatusCompletelyPaidForZeroAmountOrders()
    {
        return $this->setStatusCompletelyPaidForZeroAmountOrders;
    }

    /**
     * @return bool
     */
    public function isUseOwnPaymentMethodForZeroAmountOrders()
    {
        return $this->useOwnPaymentMethodForZeroAmountOrders;
    }
}
