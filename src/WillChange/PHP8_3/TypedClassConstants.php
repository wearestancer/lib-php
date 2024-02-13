<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_3;

use Attribute;

/**
 * A class constant will have a type declaration.
 *
 * @see https://wiki.php.net/rfc/typed_class_constants
 */
#[Attribute]
final class TypedClassConstants extends Base
{
}
