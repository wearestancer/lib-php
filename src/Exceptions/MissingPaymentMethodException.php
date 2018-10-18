<?php
declare(strict_types=1);

namespace ild78\Exceptions;

/**
 * Exception thrown when no payment method was setted before a pay tentative.
 */
class MissingPaymentMethodException extends BadMethodCallException
{
}
