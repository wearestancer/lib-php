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

    if (version_compare(PHP_VERSION, '8.1', '<')) {
        #[Attribute(Attribute::TARGET_METHOD)]
        final class ReturnTypeWillChange
        {
        }
    }

    if (version_compare(PHP_VERSION, '8.2', '<')) {
        #[Attribute(Attribute::TARGET_PARAMETER)]
        final class SensitiveParameter
        {
        }
    }

    if (version_compare(PHP_VERSION, '8.3', '<')) {
        #[Attribute(Attribute::TARGET_METHOD)]
        final class Override
        {
        }
    }
}

// phpcs:enable
