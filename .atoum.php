<?php

$runner
    ->addTestsFromDirectory(__DIR__ . '/tests/unit')
    ->addTestsFromDirectory(__DIR__ . '/tests/functional')
;

// Extensions

// autoloop
$runner
    ->getExtension(mageekguy\atoum\autoloop\extension::class)
        ->setWatchedFiles(array(__DIR__ . '/src'))
;

// JSON schema
$runner->addExtension(new mageekguy\atoum\jsonSchema\extension($script));

// Reports (for HTML coverage)
if (extension_loaded('xdebug') === true) {
    $script->addDefaultReport();

    $coverage = new mageekguy\atoum\reports\coverage\html();
    $coverage
        ->addWriter(new mageekguy\atoum\writers\std\out())
        ->setOutPutDirectory(__DIR__ . '/reports/coverage')
    ;

    $runner->addReport($coverage);

    // xnit report
    $xunit = new mageekguy\atoum\reports\asynchronous\xunit();
    $xunit
        ->addWriter(new atoum\writers\file(__DIR__ . '/reports/atoum.xunit.xml'))
    ;

    $runner->addReport($xunit);
}
