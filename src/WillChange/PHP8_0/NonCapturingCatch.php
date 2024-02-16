<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_0;

use Attribute;

/**
 * A try/catch will be changed to remove the exception catched.
 *
 * @see https://wiki.php.net/rfc/non-capturing_catches
 */
#[Attribute]
final class NonCapturingCatch extends Base
{
}
