<?php
declare(strict_types=1);

// Next line is required, we can not force type in function signature, it triggers a fatal error.
// phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing

namespace ild78\Http;

use ild78;

/**
 * Basic HTTP request
 */
class Request
{
    use MessageTrait;
}
