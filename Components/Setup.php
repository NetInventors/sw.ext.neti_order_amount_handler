<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     sbrueggenolte
 */

namespace NetiOrderAmountHandler\Components;

use Shopware\Models\Payment\Payment;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Setup
{
    const ZERO_AMOUNT_PAYMENT_NAME = 'neti_zero_order_amount';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Setup constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Creates a payment method for zero amount orders
     *
     * @throws \Exception
     */
    public function addPaymentMethod()
    {
        $em                = $this->container->get('models');
        $zeroAmountPayment = $em->getRepository(Payment::class)->findOneBy(['name' => self::ZERO_AMOUNT_PAYMENT_NAME]);

        if ($zeroAmountPayment instanceof Payment) {
            return;
        }

        $zeroAmountPayment = (new Payment())
            ->setName(self::ZERO_AMOUNT_PAYMENT_NAME)
            ->setDescription('Kostenlos')
            ->setAction('NetiZeroOrderAmountPayment')
            ->setActive(false)
            ->setPosition(0)
            ->setAdditionalDescription(
                '<div id="payment_desc">' .
                'Kann nur verwendet werden, wenn die Rechnungssumme 0 ist.</div>'
            );

        $em->persist($zeroAmountPayment);
        $em->flush($zeroAmountPayment);
    }

    /**
     * Creates a payment method for zero amount orders
     *
     * @param bool $active
     *
     * @throws \Exception
     */
    public function setPaymentMethodActivation($active)
    {
        $em      = $this->container->get('models');
        $payment = $em->getRepository(Payment::class)->findOneBy(['name' => self::ZERO_AMOUNT_PAYMENT_NAME]);

        if (!$payment instanceof Payment) {
            return;
        }

        $payment->setActive($active);
        $em->flush($payment);
    }
}