<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     sbrueggenolte
 */

use NetiOrderAmountHandler\Components\Setup;
use Shopware\Models\Order\Status as OrderStatus;

/**
 * Class Shopware_Controllers_Frontend_NetiZeroOrderAmountPayment
 */
class Shopware_Controllers_Frontend_NetiZeroOrderAmountPayment extends Shopware_Controllers_Frontend_Payment
{
    /**
     * Does all of the action
     * @throws \Exception
     */
    public function indexAction()
    {
        if (Setup::ZERO_AMOUNT_PAYMENT_NAME !== $this->getPaymentShortName()) {
            $this->redirect(['controller' => 'checkout']);

            return;
        }

        if (0.0 < $this->getAmount()) {
            $this->redirect(['controller' => 'checkout', 'action' => 'confirm', 'zero_payment_error' => true]);

            return;
        }

        $time = time();
        $this->saveOrder(
            uniqid('ZeroAmount#') . $time,
            uniqid() . $time,
            OrderStatus::PAYMENT_STATE_COMPLETELY_PAID
        );
        $this->redirect(['controller' => 'checkout', 'action' => 'finish']);
    }
}
