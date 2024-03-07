<?php

$runner
    ->addTestsFromDirectory(__DIR__ . '/tests/unit')
    ->addTestsFromDirectory(__DIR__ . '/tests/functional')
;

// Extensions

// Reports (and bonus branch coverage)
if (extension_loaded('xdebug') === true) {
    // Show default report
    $script->addDefaultReport();

    if (!getenv('CI')) {
        $path = __DIR__ . '/reports/coverage';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // HTML report
        $coverage = new atoum\atoum\reports\coverage\html();
        $coverage
            ->addWriter(new atoum\atoum\writers\std\out())
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

        $xunit = new atoum\atoum\reports\asynchronous\xunit();
        $xunit->addWriter(new atoum\atoum\writers\file(__DIR__ . '/' . $xunitFile));
        $runner->addReport($xunit);
    }

    // clover report
    $covFile = getenv('ATOUM_COVERAGE_FILENAME');

    if ($covFile) {
        $path = pathinfo($covFile, PATHINFO_DIRNAME);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $clover = new atoum\atoum\reports\cobertura();
        $clover->addWriter(new atoum\atoum\writers\file(__DIR__ . '/' . $covFile));
        $runner->addReport($clover);
    }
}
