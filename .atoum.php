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


// Reports (and bonus branch coverage)
if (extension_loaded('xdebug') === true) {
    $script
        ->enableBranchAndPathCoverage()
        ->addDefaultReport()
    ;

    // HTML report
    if (!getenv('CI')) {
        $coverage = new mageekguy\atoum\reports\coverage\html();
        $coverage
            ->addWriter(new mageekguy\atoum\writers\std\out())
            ->setOutPutDirectory(__DIR__ . '/reports/coverage')
        ;
        $runner->addReport($coverage);
    }

    // xunit report
    $xunitFile = getenv('XUNIT_FILE');

    if ($xunitFile) {
        $xunit = new mageekguy\atoum\reports\asynchronous\xunit();
        $xunit->addWriter(new mageekguy\atoum\writers\file(__DIR__ . '/' . $xunitFile));
        $runner->addReport($xunit);
    }

    // clover report
    $covFile = getenv('COVERAGE_FILE');

    if ($covFile) {
        $clover = new mageekguy\atoum\reports\sonar\clover();
        $clover->addWriter(new mageekguy\atoum\writers\file(__DIR__ . '/' . $covFile));
        $runner->addReport($clover);
    }
}
