<?php

$runner
    ->addTestsFromDirectory(__DIR__ . '/tests/unit')
    ->addTestsFromDirectory(__DIR__ . '/tests/functional')
;

// Extensions

// autoloop
$runner
    ->getExtension(mageekguy\atoum\autoloop\extension::class)
        ->setWatchedFiles(array(__DIR__ . '/src', __DIR__ . '/tests/Stub'))
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
    $version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    $xunitPath = __DIR__ . '/reports/atoum-' . $version . '.xunit.xml';
    $xunit = new mageekguy\atoum\reports\asynchronous\xunit();
    $xunit
        ->addWriter(new atoum\writers\file($xunitPath))
    ;

    $runner->addReport($xunit);
}
