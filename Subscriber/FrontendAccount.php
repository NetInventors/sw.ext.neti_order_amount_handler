<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   shopware_stable
 * @author     hrombach
 */

namespace NetiOrderAmountHandler\Subscriber;

use Enlight\Event\SubscriberInterface;
use NetiOrderAmountHandler\Components\Setup;

class FrontendAccount implements SubscriberInterface
{
    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return ['Enlight_Controller_Action_PostDispatchSecure_Frontend_Account' => 'onPostDispatchSecure'];
    }

    public function onPostDispatchSecure(\Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Account $subject */
        $subject = $args->getSubject();

        $paymentMeans = $subject->View()->getAssign('sPaymentMeans');

        foreach ($paymentMeans as $key => $paymentMean) {
            if (Setup::ZERO_AMOUNT_PAYMENT_NAME === $paymentMean['name']) {
                unset($paymentMeans[$key]);
            }
        }

        $subject->View()->assign('sPaymentMeans', $paymentMeans);
    }
}