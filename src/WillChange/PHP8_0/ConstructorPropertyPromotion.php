<?php
declare(strict_types=1);

namespace Stancer\WillChange\PHP8_0;

use Attribute;

/**
 * Class will be rewritten with constructor property promotion.
 *
 * @see https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor.promotion
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class ConstructorPropertyPromotion extends Base
{
}
