<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_1;

use Attribute;

/**
 * Will be migrated to enumeration.
 *
 * @see https://www.php.net/manual/en/language.enumerations.php
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
final class Enumeration extends Base
{
}
