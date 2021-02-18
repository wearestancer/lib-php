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
    $script->enableBranchAndPathCoverage();

    // Show default report
    $script->addDefaultReport();

    if (!getenv('CI')) {
        $path = __DIR__ . '/reports/coverage';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // HTML report
        $coverage = new mageekguy\atoum\reports\coverage\html();
        $coverage
            ->addWriter(new mageekguy\atoum\writers\std\out())
            ->setOutPutDirectory($path)
        ;
        $runner->addReport($coverage);
    }

    // xunit report
    $xunitFile = getenv('ATOUM_XUNIT_FILENAME');

    if ($xunitFile) {
        $path = pathinfo($xunitFile, PATHINFO_DIRNAME);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $xunit = new mageekguy\atoum\reports\asynchronous\xunit();
        $xunit->addWriter(new mageekguy\atoum\writers\file(__DIR__ . '/' . $xunitFile));
        $runner->addReport($xunit);
    }

    // clover report
    $covFile = getenv('ATOUM_COVERAGE_FILENAME');

    if ($covFile) {
        $path = pathinfo($covFile, PATHINFO_DIRNAME);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $clover = new mageekguy\atoum\reports\cobertura();
        $clover->addWriter(new mageekguy\atoum\writers\file(__DIR__ . '/' . $covFile));
        $runner->addReport($clover);
    }
}
