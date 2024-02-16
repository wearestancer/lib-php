<?php

require __DIR__ . '/../vendor/autoload.php';

interface Documentation {}

// Default model values.
$defaultModel = [
    'desc' => null,
    'fullDesc' => null,
    'list' => false,
    'method' => null,
    'nullable' => true,
    'required' => false,
    'restricted' => false,
    'type' => 'string',
    'value' => null,
];

// Initialize specific classes.
$classes = [
    Stancer\Config::class => [
        'instance' => [],
    ],
    Stancer\Core\AbstractObject::class => [
        'instance' => new class() extends Stancer\Core\AbstractObject implements Documentation {},

        'throws' => [
            [Stancer\Exceptions\BadMethodCallException::class, 'when calling an unknown method'],
            [Stancer\Exceptions\BadPropertyAccessException::class, 'when calling an unknown property'],
        ],
    ],
];

// Iterate through our sources.
$baseDirectory = realpath(__DIR__ . '/../src') . '/';
$directory = new RecursiveDirectoryIterator($baseDirectory);
$iterator = new RecursiveIteratorIterator($directory);

foreach ($iterator as $file) {
    if ($file instanceof SplFileInfo) {
        // We only handle PHP files.
        if ($file->getExtension() === 'php') {
            $path = str_replace($baseDirectory, '', $file->getRealPath());
            $className = 'Stancer\\' . str_replace('/', '\\', str_replace('.' . $file->getExtension(), '', $path));

            // Skip some files, like the polyfill...
            if ($path === 'polyfill.php') {
                continue;
            }

            // the documentation helper...
            if (strpos($className, 'Stancer\\Core\\Documentation\\') !== false) {
                continue;
            }

            // the HTTP client (as it does not have magical properties)...
            if (strpos($className, 'Stancer\\Http\\') !== false) {
                continue;
            }

            // interfaces, for the same reason...
            if (strpos($className, 'Stancer\\Interfaces\\') !== false) {
                continue;
            }

            // and traits, same again.
            if (strpos($className, 'Stancer\\Traits\\') !== false) {
                continue;
            }

            if (!array_key_exists($className, $classes)) {
                $classes[$className] = [];
            }

            $classes[$className]['filepath'] = $file->getRealPath();
        }
    }
}

// Now we have our classes and paths, we can find the magic in it.
foreach ($classes as $className => $classData) {
    if (!array_key_exists('filepath', $classData)) {
        continue;
    }

    $file = $classData['filepath'];
    $doc = [];
    $model = [];

    // We need an instance for it.
    if (array_key_exists('instance', $classData)) {
        if ($classData['instance'] instanceof Documentation) {
            $obj = $classData['instance'];
        } else {
            $obj = new $className($classData['instance']);
        }
    } else {
        $obj = new $className();
    }

    unset($classData['filepath']);
    unset($classData['instance']);

    // Adding manual aliases.

    $aliases = [];

    foreach ($classData['aliases'] ?? [] as $name => $alias) {
        $aliases[$name] = [
            $alias,
            Stancer\Helper::camelCaseToSnakeCase($alias),
        ];
    }

    unset($classData['aliases']);

    // Adding manual exceptions.

    $throws = $classData['throws'] ?? [];

    if ($throws) {
        foreach ($throws as $data) {
            [$exception, $reason] = $data;

            $doc[] = sprintf('@throws %s %s.', $exception, ucfirst($reason));
        }
    }

    unset($classData['throws']);

    // Searching for the model.

    $reflect = new ReflectionClass($className);
    $model = [];

    if (method_exists($obj, 'getModel')) {
        $model = $obj->getModel();
    }

    $methods = [
        'jsonSerialize' => [
            'used' => true,
        ],
    ];

    // Checking for class attributes.

    $attributes = $reflect->getAttributes();

    foreach ($attributes as $attribute) {
        $instance = $attribute->newInstance();

        if ($instance instanceof Stancer\Core\Documentation\AddMethod) {
            $classData[] = $instance->getData();
        }

        if ($instance instanceof Stancer\Core\Documentation\AddProperty) {
            $classData[$instance->getName()] = $instance->getData();
        }
    }

    // Looking for the built-in method of the object.

    foreach ($reflect->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        $name = $method->getName();
        $snake = Stancer\Helper::camelCaseToSnakeCase($name);

        // Another look on attributes, on the method this time.

        $attributes = $method->getAttributes();

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();

            if ($instance instanceof Stancer\Core\Documentation\FormatProperty) {
                $prop = lcfirst(str_replace('get', '', $name));

                $classData[$prop] = $instance->getData();
            }
        }

        // Do not considere magic method.
        if (strpos($name, '__') === 0) {
            continue;
        }

        // Pass methods already detected.
        if ($name === $snake) {
            continue;
        }

        // Pass methods from the parent.
        if ($method->getDeclaringClass()->getName() !== $className) {
            continue;
        }

        // Find the method documentation (to create a PHPdoc for the snake_case version of it).
        $docstring = str_replace(['/**', '*/'], '', $method->getDocComment());
        $lines = array_filter(array_map(fn($line) => trim(substr($line, strpos($line, '*') + 1)), explode("\n", $docstring)));

        $tmp = $method->getReturnType();
        $return = '';

        if (is_null($tmp)) {
            $return = 'mixed';
        }

        if ($tmp instanceof ReflectionNamedType) {
            $return = $tmp->getName();

            if ($return === 'self' && !$method->isStatic()) {
                $return = '$this';
            }

            if ($tmp->allowsNull()) {
                $return = '?' . $return;
            }
        }

        // Registering the "new" method.
        $methods[$name] = array_merge([
            'desc' => array_shift($lines),
            'name' => $snake,
            'parameters' => $method->getParameters(),
            'static' => $method->isStatic(),
            'return' => $return,
            'used' => false,
        ], $methods[$name] ?? []);
    }

    // Merging everything together.
    foreach ($classData as $name => $data) {
        $model[$name] = array_merge($model[$name] ?? [], $data);
    }

    ksort($model);

    // Now we have every property and methods, we can create the documentation.
    foreach ($model as $name => $data) {
        if ($name === 'created' && !array_key_exists('desc', $data)) {
            continue;
        }

        $data = array_merge($defaultModel, $data);

        // It's a method, we create a "@method" entry.
        if ($data['method']) {
            /** @var array{desc?: string, parameters?: string[], return?: string, stan?: bool} $method */
            $method = $data['method'];

            $doc[] = trim(implode(' ', [
                '@' . (array_key_exists('stan', $method) && $method['stan'] ? 'phpstan-' : '') . 'method',
                $method['return'] ?? 'void',
                sprintf('%s(%s)', $method['name'] ?? $name, implode(', ', $method['parameters'] ?? [])),
                $method['desc'] ?? '',
            ]));

            continue;
        }

        // Preparing automatic getter and setter.

        $getter = 'get' . ucfirst($name);
        $setter = 'set' . ucfirst($name);

        $desc = '';
        $descGetter = '';
        $descSetter = '';

        // "desc" is used as a template, adding "Get" or "Set"
        // "fullDesc" is used as is
        if ($data['fullDesc']) {
            $descGetter = $data['fullDesc'];
            $descSetter = $data['fullDesc'];
        } elseif ($data['desc']) {
            $desc = $data['desc'] . '.';

            if ($desc[1] === strtoupper($desc[1])) { // Prevent to lowercase acronyms
                $descGetter = 'Get ' . $desc;
                $descSetter = 'Set ' . $desc;
            } else {
                $descGetter = 'Get ' . lcfirst($desc);
                $descSetter = 'Set ' . lcfirst($desc);
            }
        }

        // Finding the type.

        $types = is_array($data['type']) ? $data['type'] : [$data['type']];
        $typeNullable = $typeNonNullable = implode('|', $types);

        if ($data['list']) {
            foreach ($types as &$type) {
                $type .= '[]';
            }

            $typeNullable = $typeNonNullable = implode('|', $types);
        } else if (is_null($data['value']) && $data['nullable']) {
            $typeNullable = '?' . $typeNonNullable;
        }

        // Do we need to create getter methods?
        if ($data['generateMethodGetter'] ?? true) {
            if (!method_exists($obj, $getter)) {
                $doc[] = implode(' ', [
                    '@method',
                    $typeNullable,
                    $getter . '()',
                    $descGetter,
                ]);
            }

            $doc[] = implode(' ', [
                '@method',
                $typeNullable,
                Stancer\Helper::camelCaseToSnakeCase($getter) . '()',
                $descGetter,
            ]);

            $methods[$getter]['used'] = true;
        }

        // Do we need to create setter methods?
        if (!$data['restricted']) {
            if (!method_exists($obj, $setter)) {
                $doc[] = implode(' ', [
                    '@method',
                    '$this',
                    $setter . '(' . $typeNonNullable . ' $' . $name . ')',
                    $descSetter,
                ]);
            }

            $methods[$setter]['used'] = true;

            $doc[] = implode(' ', [
                '@method',
                '$this',
                vsprintf('%s(%s $%s)', [
                    Stancer\Helper::camelCaseToSnakeCase($setter),
                    $typeNonNullable,
                    Stancer\Helper::camelCaseToSnakeCase($name),
                ]),
                $descSetter,
            ]);
        }

        // Does the object have aliases?
        if ($obj instanceof Stancer\Traits\AliasTrait) {
            $alias = $obj->findAlias($getter);

            if ($alias) {
                $aliases[$getter] = $alias;
            }
        }

        // Adding alliases.
        if (array_key_exists($getter, $aliases)) {
            foreach ($aliases[$getter] as $method) {
                if (!method_exists($obj, $getter)) {
                    $doc[] = implode(' ', [
                        '@method',
                        $typeNullable,
                        $method . '()',
                        $descGetter,
                    ]);
                }

                $doc[] = implode(' ', [
                    '@method',
                    $typeNullable,
                    $method . '()',
                    $descGetter,
                ]);
            }
        }

        // Now, the properties.

        $doc[] = implode(' ', [
            '@property' . ($data['restricted'] ? '-read' : ''),
            $typeNullable,
            '$' . $name,
            $desc,
        ]);

        if (Stancer\Helper::camelCaseToSnakeCase($name) !== $name) {
            $doc[] = implode(' ', [
                '@property' . ($data['restricted'] ? '-read' : ''),
                $typeNullable,
                '$' . Stancer\Helper::camelCaseToSnakeCase($name),
                $desc,
            ]);
        }
    }

    // Ok, now we check for methods defined in the object which will have an automatic alias.

    $notUsedMethods = array_filter($methods, fn($m) => !$m['used']);

    if ($notUsedMethods) {
        foreach ($notUsedMethods as $data) {
            $parameters = [];

            foreach ($data['parameters'] as $param) {
                $name = '$' . Stancer\Helper::camelCaseToSnakeCase($param->getName());
                $type = '';
                $value = '';

                // Finding the type.
                if ($param->hasType()) {
                    $tmp = $param->getType();

                    // In PHP8, `getType()` may return a `ReflectionNamedType`
                    if ($tmp instanceof ReflectionNamedType) {
                        $type = $tmp->getName();
                    }
                }

                // Does the parameter has a default value?
                if ($param->isDefaultValueAvailable()) {
                    $tmp = $param->getDefaultValue();
                    $coerce = function($val) use ($type) {
                        if (is_null($val)) {
                            return 'null';
                        }

                        if ($type === 'bool') {
                            return $val ? 'true' : 'false';
                        }

                        return $val;
                    };

                    if (is_array($tmp)) {
                        $value = '[' . implode(', ', $coerce($tmp)) . ']';
                    } else {
                        $value = $coerce($tmp);
                    }
                }

                if ($value) {
                    $parameters[] = trim(sprintf('%s %s = %s', $type, $name, $value));
                } else {
                    $parameters[] = trim(sprintf('%s %s', $type, $name));
                }
            }

            // Adding it to the doc.
            $doc[] = implode(' ', [
                '@method' . ($data['static'] ? ' static' : ''),
                $data['return'],
                $data['name'] . '(' . implode(', ', $parameters) . ')',
                $data['desc'],
            ]);
        }
    }

    // We have all our documentation, we need to prepare it for insertion.

    // Every line will be grouped by tag ("@method", "@property", @property-read",...)
    // and ordered (to prevent conflicts and simplify review).

    $parts = [];

    foreach ($doc as $line) {
        $splitted = explode(' ', $line);
        $action = $splitted[0];
        $name = $splitted[2]; // the name of the property/method

        // If it's a static method, the index 2 will be "static" so we need to take the next one.
        if ($action === '@method' && $splitted[1] === 'static') {
            $name = $splitted[3];
        }

        // "@throws" can not splitted for ordering, we use the full line for this.
        if ($action === '@throws') {
            $name = $line;
        }

        if (!array_key_exists($action, $parts)) {
            $parts[$action] = [
                '', // the block sep
            ];
        }

        $parts[$action][$name] = $line;

        ksort($parts[$action]);
    }

    $doc = [];

    ksort($parts);

    // Merging every parts together.
    foreach ($parts as $part) {
        $doc = array_merge($doc, $part);
    }

    // Time for putting it in the file.

    $content = file_get_contents($file);
    $lines = [];
    $parsingTags = false;

    foreach (explode("\n", $content) as $line) {
        // At the first PHPdoc we handle, we erase everything to put our documentation.
        if (preg_match('/^\s\* @(?:phpstan-)?(?:method|property(?:-read)?|throws)/', $line)) {
            if (!$parsingTags) {
                $last = array_pop($lines);

                if ($last !== ' *') {
                    array_push($lines, $last);
                }

                $lines = array_merge($lines, array_map(fn($l) => ' * ' . $l, $doc));
            }

            $parsingTags = true;
        } elseif ($line !== ' *' || !$parsingTags) {
            $lines[] = $line;
        }
    }

    // The final step, write it.
    file_put_contents($file, implode("\n", array_map('rtrim', $lines)));
}