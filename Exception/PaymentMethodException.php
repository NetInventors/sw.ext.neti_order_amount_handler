<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiOrderAmountHandler\Exception;

use Doctrine\ORM\UnexpectedResultException;
use NetiOrderAmountHandler\Components\Setup;

class PaymentMethodException extends \RuntimeException
{
    public static function fromUnexpectedResultException(UnexpectedResultException $exception)
    {
        return new static(
            sprintf(
                'Unexpected number of results returned for payment method "%s". ' .
                'It may not be installed or exist more than once.',
                Setup::ZERO_AMOUNT_PAYMENT_NAME
            ), $exception
        );
    }
}