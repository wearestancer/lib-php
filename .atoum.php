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
