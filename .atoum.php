<?php

$runner->addTestsFromDirectory(__DIR__ . '/tests/unit');


// Extensions

// autoloop
$runner
    ->getExtension(mageekguy\atoum\autoloop\extension::class)
        ->setWatchedFiles(array(__DIR__ . '/src'))
;
