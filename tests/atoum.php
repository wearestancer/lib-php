<?php

namespace ild78\Tests;

class atoum extends \atoum\test
{
    public function __construct(
        adapter $adapter = null,
        annotations\extractor $annotationExtractor = null,
        asserter\generator $asserterGenerator = null,
        test\assertion\manager $assertionManager = null,
        \closure $reflectionClassFactory = null
    ) {
        parent::__construct($adapter, $annotationExtractor, $asserterGenerator, $assertionManager, $reflectionClassFactory);

        $this->getAsserterGenerator()->addNamespace('ild78\Tests\asserters');
    }
}
