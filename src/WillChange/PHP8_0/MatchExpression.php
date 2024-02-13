<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_0;

use Attribute;

/**
 * A `switch` will be changed in `match`.
 *
 * @see https://www.php.net/manual/en/control-structures.match.php
 */
#[Attribute]
final class MatchExpression extends Base
{
}
