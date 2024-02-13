<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_0;

use Attribute;

/**
 * Return type will be change to `static`.
 *
 * @see https://wiki.php.net/rfc/static_return_type
 */
#[Attribute]
final class StaticReturnType extends Base
{
}
