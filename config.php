<?php
/**
 * @var $this \NetiFoundation\Service\PluginManager\Base
 */

return [
    'form' => [
        [
            'scope'      => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'name'       => 'setStatusCompletelyPaidForZeroAmountOrders',
            'value'      => true,
            'type'       => 'boolean',
            'label'      => [
                'de_DE' => 'Bestellungen mit Rechnungssumme 0 automatisch auf "Komplett bezahlt" stellen.',
                'en_GB' => 'Set "free of charge" orders automatically to the payment status "completely paid".'
            ],
            'description' => [],
        ],
        [
            'scope'      => \Shopware\Models\Config\Element::SCOPE_SHOP,
            'name'       => 'useOwnPaymentMethodForZeroAmountOrders',
            'value'      => true,
            'type'       => 'boolean',
            'label'      => [
                'de_DE' => '"Kostenlos"-Zahlungsart automatisch bei 0,- Bestellsummen anwenden',
                'en_GB' => 'Automatically use "free of charge" payment method for orders with an amount of 0.'
            ],
            'description' => [
                'de_DE' => 'Viele Zahlungsdienstleister haben Probleme mit der Verarbeitung von 0,- Bestellsummen, die mit dieser Einstellung umgangen werden können.',
                'en_GB' => 'Many payment providers cannot process order amounts of 0, which can be bypassed by this setting.',
            ],
        ],
    ]
];
