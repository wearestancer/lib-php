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


// Reports
if (extension_loaded('xdebug') === true) {
    $script->addDefaultReport();

    // HTML report
    $coverage = new mageekguy\atoum\reports\coverage\html();
    $coverage
        ->addWriter(new mageekguy\atoum\writers\std\out())
        ->setOutPutDirectory(__DIR__ . '/reports/coverage')
    ;
    $runner->addReport($coverage);

    $version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    $path = __DIR__ . '/reports/';

    // xunit report
    $xunit = new mageekguy\atoum\reports\asynchronous\xunit();
    $xunit->addWriter(new mageekguy\atoum\writers\file($path . 'atoum-' . $version . '.xunit.xml'));
    $runner->addReport($xunit);
}
