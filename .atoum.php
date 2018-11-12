<?php

$runner->addTestsFromDirectory(__DIR__ . '/tests/unit');


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
}
