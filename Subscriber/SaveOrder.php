<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiOrderAmountHandler\Subscriber;

use Enlight\Event\SubscriberInterface;
use NetiFoundation\Service\PluginManager\ConfigInterface;
use NetiOrderAmountHandler\Struct\PluginConfig;
use Shopware\Models\Order\Status;

class SaveOrder implements SubscriberInterface
{
    /**
     * @var PluginConfig
     */
    private $config;

    /**
     * SaveOrder constructor.
     *
     * @param ConfigInterface $configService
     *
     * @throws \Exception
     */
    public function __construct(ConfigInterface $configService)
    {
        $this->config = $configService->getPluginConfig($this);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Order_SaveOrder_FilterParams' => 'onSaveOrderFilterParams',
        ];
    }

    /**
     * In case custom payment method is disabled, but updating payment status is enabled.
     *
     * @param \Enlight_Event_EventArgs $args
     *
     * @return mixed
     */
    public function onSaveOrderFilterParams(\Enlight_Event_EventArgs $args)
    {
        $return = $args->getReturn();

        $isNet = 1 === $return['net'];
        if ($this->config->isUseOwnPaymentMethodForZeroAmountOrders() ||
            !$this->config->isSetStatusCompletelyPaidForZeroAmountOrders() ||
            (.0 < ($isNet ? $return['invoice_amount_net'] : $return['invoice_amount'])) ||
            (.0 < ($isNet ? $return['invoice_shipping'] : $return['invoice_shipping_net']))
        ) {
            return $return;
        }

        $return['cleared'] = Status::PAYMENT_STATE_COMPLETELY_PAID;

        return $return;
    }
}