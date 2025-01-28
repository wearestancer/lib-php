<?php

declare(strict_types=1);

if (!class_exists('SensitiveParameter')) {
    #[Attribute(Attribute::TARGET_PARAMETER)]
    final class SensitiveParameter {}
}

if (!class_exists('Override')) {
    #[Attribute(Attribute::TARGET_METHOD)]
    final class Override {}
}
