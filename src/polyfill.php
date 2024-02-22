<?php
declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Squiz.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Squiz.Classes.ClassDeclaration.NewlinesAfterCloseBrace
// phpcs:disable Squiz.Classes.ClassDeclaration.SpaceBeforeCloseBrace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Commenting.ClassComment.Missing

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

// phpcs:enable
