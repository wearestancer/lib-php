<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_0;

use Attribute;

/**
 * Return type will have union.
 *
 * @see https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.union
 */
#[Attribute]
final class UnionType extends Base
{
}
