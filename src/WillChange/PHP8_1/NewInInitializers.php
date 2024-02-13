<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_1;

use Attribute;

/**
 * A parameter will change to initialize a new instance.
 *
 * @see https://wiki.php.net/rfc/new_in_initializers
 */
#[Attribute]
final class NewInInitializers extends Base
{
}
