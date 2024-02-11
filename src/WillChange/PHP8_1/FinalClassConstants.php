<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_1;

use Attribute;

/**
 * A class constant will be declared as final.
 *
 * @see https://www.php.net/manual/en/language.oop5.final.php#language.oop5.final.example.php81
 */
#[Attribute]
final class FinalClassConstants extends Base
{
}
