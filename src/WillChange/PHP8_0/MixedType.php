<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_0;

use Attribute;

/**
 * A parameter type or a return type will changed to mixed.
 *
 * @see https://wiki.php.net/rfc/mixed_type_v2
 */
#[Attribute]
final class MixedType extends Base
{
}
