<?php
declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Squiz.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Squiz.Classes.ClassDeclaration.NewlinesAfterCloseBrace
// phpcs:disable Squiz.Classes.ClassDeclaration.SpaceBeforeCloseBrace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Commenting.ClassComment.Missing
// phpcs:disable Squiz.Commenting.InlineComment.SpacingAfter

if (version_compare(PHP_VERSION, '8.0', '>=')) {
    // Attributes are available since PHP8, before they will be seen as comments.

    if (!class_exists('ReturnTypeWillChange')) {
        #[Attribute(Attribute::TARGET_METHOD)]
        final class ReturnTypeWillChange
        {
        }
    }

    if (!class_exists('SensitiveParameter')) {
        #[Attribute(Attribute::TARGET_PARAMETER)]
        final class SensitiveParameter
        {
        }
    }

    if (!class_exists('Override')) {
        #[Attribute(Attribute::TARGET_METHOD)]
        final class Override
        {
        }
    }
}

// phpcs:enable
