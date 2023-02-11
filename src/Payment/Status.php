<?php
declare(strict_types=1);

namespace Stancer\Payment;

/**
 * List of a payment status.
 */
class Status
{
    public const AUTHORIZE = 'authorize';
    public const AUTHORIZED = 'authorized';
    public const CANCELED = 'canceled';
    public const CAPTURE = 'capture';
    public const CAPTURE_SENT = 'capture_sent';
    public const CAPTURED = 'captured';
    public const DISPUTED = 'disputed';
    public const EXPIRED = 'expired';
    public const FAILED = 'failed';
    public const REFUSED = 'refused';
    public const TO_CAPTURE = 'to_capture';
}
