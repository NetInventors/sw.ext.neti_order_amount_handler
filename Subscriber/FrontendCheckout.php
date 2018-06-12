<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     sbrueggenolte
 */

namespace NetiOrderAmountHandler\Subscriber;

use Doctrine\ORM\UnexpectedResultException;
use Enlight\Event\SubscriberInterface;
use NetiFoundation\Service\PluginManager\ConfigInterface;
use NetiOrderAmountHandler\Components\Setup;
use NetiOrderAmountHandler\Exception\PaymentMethodException;
use NetiOrderAmountHandler\Struct\PluginConfig;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Payment\Payment as PaymentModel;

class FrontendCheckout implements SubscriberInterface
{
    /**
     * @var \Enlight_Components_Session_Namespace
     */
    private $session;

    /**
     * @var ModelManager
     */
    private $em;

    /**
     * @var PluginConfig
     */
    private $config;

    /**
     * @var \Shopware_Components_Modules
     */
    private $modules;

    /**
     * Payment constructor.
     *
     * @param \Enlight_Components_Session_Namespace $session
     * @param ModelManager                          $em
     * @param ConfigInterface                       $configService
     * @param \Shopware_Components_Modules          $modules
     *
     * @throws \Exception
     */
    public function __construct(
        \Enlight_Components_Session_Namespace $session,
        ModelManager $em,
        ConfigInterface $configService,
        \Shopware_Components_Modules $modules
    ) {
        $this->config  = $configService->getPluginConfig('NetiOrderAmountHandler');
        $this->session = $session;
        $this->em      = $em;
        $this->modules = $modules;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onPreDispatchFrontendCheckout',
        ];
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function onPreDispatchFrontendCheckout(\Enlight_Controller_ActionEventArgs $args)
    {
        $isZeroAmountOrder = $this->isZeroAmountOrder();
        $args->getSubject()->View()->assign('netiIsZeroAmountOrder', $isZeroAmountOrder);
        $args->getSubject()->View()->assign(
            'netiHidePaymentSelect',
            $isZeroAmountOrder && $this->config->isUseOwnPaymentMethodForZeroAmountOrders()
        );

        switch ($args->getRequest()->getActionName()) {
            case 'index':
            case 'confirm':
                $this->handleIndexConfirm($args);

                return;
            case 'shippingPayment':
            case 'cart':
                $this->handleShippingPayment($args);

                return;
        }
    }

    /**
     * @return bool
     */
    private function isZeroAmountOrder()
    {
        if (null !== $orderVariables = $this->session->get('sOrderVariables')) {
            return !(.0 < \round($orderVariables['sAmount'], 2) || .0 < \round($orderVariables['sAmountWithTax'], 2));
        }

        return !(.0 < \round($this->session->get('sBasketAmount'), 2));
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    private function handleIndexConfirm(\Enlight_Controller_ActionEventArgs $args)
    {
        if (!$this->config->isUseOwnPaymentMethodForZeroAmountOrders() || !$this->isZeroAmountOrder()) {
            return;
        }

        /** @var \ArrayObject $orderVariables */
        $orderVariables = $this->session->get('sOrderVariables', new \ArrayObject());

        $zeroPaymentIdQuery = $this->em->createQueryBuilder()
                                       ->from(PaymentModel::class, 'p')
                                       ->select('p.id')
                                       ->where('p.name = :name')
                                       ->setParameter('name', Setup::ZERO_AMOUNT_PAYMENT_NAME)
                                       ->setMaxResults(1)
                                       ->getQuery();

        try {
            $zeroPaymentId = $zeroPaymentIdQuery->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            throw PaymentMethodException::fromUnexpectedResultException($e);
        }

        $sAdmin                             = $this->modules->Admin();
        $view                               = $args->getSubject()->View();
        $zeroPaymentMean                    = $sAdmin->sGetPaymentMeanById($zeroPaymentId, $sAdmin->sGetUserData());
        $sUserData                          = $view->getAssign('sUserData');
        $sUserData['additional']['payment'] = $zeroPaymentMean;
        $orderVariables['sPayment']         = $zeroPaymentMean;
        $orderVariables['sUserData']        = $sUserData;

        $this->session->offsetSet('sOrderVariables', $orderVariables);
        $this->session->offsetSet('sPaymentID', $zeroPaymentId);
        $view->assign('sPayment', $zeroPaymentMean);
        $view->assign('sUserData', $sUserData);
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    private function handleShippingPayment(\Enlight_Controller_ActionEventArgs $args)
    {
        if (!$this->config->isUseOwnPaymentMethodForZeroAmountOrders()) {
            return;
        }

        $view = $args->getSubject()->View();

        $sPayments = $view->getAssign('sPayments');

        foreach ($sPayments as $key => $payment) {
            if (Setup::ZERO_AMOUNT_PAYMENT_NAME === $payment['name']) {
                unset($sPayments[$key]);
            }
        }

        $view->assign('sPayments', $sPayments);
    }
}