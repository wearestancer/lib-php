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

/**
 * Prepare parameter/property data.
 *
 * Will define type, return type and descriptions for each line of documentation.
 *
 * @param string $action The action done on this property(get, set, etc...).
 * @param array $data An array containing all the data necessary to create the property/method doc.
 * @return array
 *
 * @phpstan-param DataModel $data
 * @phpstan-return DataModel
 */
function prepareData(string $action, array $data): array
{
    if ($data) {
        if (!$data['fullDesc'] && $data['desc']) {
            $desc = $data['desc'] . '.';
            $data['desc'] = $desc;

            if ($desc[1] === strtoupper($desc[1])) { // Prevent to lowercase acronyms
                $data['fullDesc'] = ucfirst($action) . ' ' . $desc;
            } else {
                $data['fullDesc'] = ucfirst($action) . ' ' . lcfirst($desc);
            }
        }

        // Finding the type.

        $types = is_array($data['type']) ? $data['type'] : [$data['type']];
        $rewriteTypes = fn(string $value): string => $value === 'bool' ? 'boolean' : $value;
        $typeNullable = $typeNonNullable = implode('|', array_map($rewriteTypes, $types));

        if ($data['list']) {
            foreach ($types as &$type) {
                $type .= '[]';
            }

            $typeNullable = $typeNonNullable = implode('|', $types);
        } else if (is_null($data['value']) && $data['nullable']) {
            $typeNullable = '?' . $typeNonNullable;

            if (count($types) > 1) {
                $typeNullable = $typeNonNullable . '|null';
            }
        }

        if ($action === 'get') {
            $data['type'] = $typeNullable;
        } else {
            $data['type'] = $typeNonNullable;
        }
    }

    return $data;
}

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
            'alreadyDocumented' => true,
        ],
    ];

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

            if ($return === 'bool') {
                $return = 'boolean';
            }

            if ($tmp->allowsNull()) {
                $return = '?' . $return;
            }
        }

        $parameters = $method->getParameters();

        // Registering the "new" method.
        $methods[$name] = array_merge([
            'alreadyDocumented' => $name === $snake,
            'desc' => array_shift($lines),
            'name' => $snake,
            'parameters' => $parameters,
            'static' => $method->isStatic(),
            'return' => $return,
        ], $methods[$name] ?? []);

        // Registering properties based on methods
        if (empty($parameters) && strpos($name, 'get') !== 0 && !in_array($name, ['delete', 'populate', 'send'])) {
            foreach ([$name, $snake] as $tmp) {
                if (!array_key_exists($tmp, $model)) {
                    $model[$tmp] = [
                        'getter' => [],
                        'property' => [
                            'desc' => 'Alias for `' . $reflect->getName() . '::' . $name . '()`',
                            'nullable' => false,
                            'type' => $return,
                        ],
                        'restricted' => true,
                    ];
                }
            }
        }
    }

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

    // Merging everything together.
    foreach ($classData as $name => $data) {
        $model[$name] = array_merge($model[$name] ?? [], $data);
    }

    ksort($model);

    // Now we have every property and methods, we can create the documentation.
    foreach ($model as $name => $data) {
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

        // Preparing getter and setter data.

        $getterMethod = 'get' . ucfirst($name);
        $setterMethod = 'set' . ucfirst($name);

        $getter = prepareData('get', $data);
        $setter = [];
        $property = prepareData('get', $data);

        if (array_key_exists('getter', $data)) {
            if ($data['getter']) {
                $getter = prepareData('get', array_merge($data, $data['getter']));
            } else {
                $getter = [];
            }
        }

        if (array_key_exists('property', $data)) {
            if ($data['property']) {
                $property = prepareData('get', array_merge($data, $data['property']));
            } else {
                $property = [];
            }
        }

        if (!$data['restricted']) {
            $setter = prepareData('set', array_merge($data, array_key_exists('setter', $data) ? $data['setter'] : []));
        }

        $data = prepareData('get', $data);

        // Do we need to create getter methods?
        if ($getter) {
            if (!method_exists($obj, $getterMethod)) {
                $doc[] = implode(' ', [
                    '@method',
                    $getter['type'],
                    $getterMethod . '()',
                    $getter['fullDesc'],
                ]);
            }

            $doc[] = implode(' ', [
                '@method',
                $getter['type'],
                Stancer\Helper::camelCaseToSnakeCase($getterMethod) . '()',
                $getter['fullDesc'],
            ]);

            $methods[$getterMethod]['alreadyDocumented'] = true;
        }

        // Do we need to create setter methods?
        if ($setter) {
            if (!method_exists($obj, $setterMethod)) {
                $doc[] = implode(' ', [
                    '@method',
                    '$this',
                    $setterMethod . '(' . $setter['type'] . ' $' . $name . ')',
                    $setter['fullDesc'],
                ]);
            }

            $methods[$setterMethod]['alreadyDocumented'] = true;

            $doc[] = implode(' ', [
                '@method',
                '$this',
                vsprintf('%s(%s $%s)', [
                    Stancer\Helper::camelCaseToSnakeCase($setterMethod),
                    $setter['type'],
                    Stancer\Helper::camelCaseToSnakeCase($name),
                ]),
                $setter['fullDesc'],
            ]);
        }

        // Now, the properties.

        if ($property) {
            $doc[] = implode(' ', [
                '@property' . ($data['restricted'] ? '-read' : ''),
                $property['type'],
                '$' . $name,
                $property['desc'],
            ]);

            if (Stancer\Helper::camelCaseToSnakeCase($name) !== $name) {
                $doc[] = implode(' ', [
                    '@property' . ($data['restricted'] ? '-read' : ''),
                    $property['type'],
                    '$' . Stancer\Helper::camelCaseToSnakeCase($name),
                    $property['desc'],
                ]);
            }
        }
    }

    // Ok, now we check for methods defined in the object which will have an automatic alias.

    $notDocumentedMethods = array_filter($methods, fn($m) => !$m['alreadyDocumented']);

    if ($notDocumentedMethods) {
        foreach ($notDocumentedMethods as $data) {
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

                if ($type === 'bool') {
                    $type = 'boolean';
                }

                // Does the parameter has a default value?
                if ($param->isDefaultValueAvailable()) {
                    $tmp = $param->getDefaultValue();
                    $coerce = function($val) use ($type) {
                        if (is_null($val)) {
                            return 'null';
                        }

                        if ($type === 'boolean') {
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
