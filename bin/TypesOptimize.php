<?php

if (!isset($typesClasses) || !isset($root)) {
    $root = dirname(__DIR__);
    include $root . '/vendor/autoload.php';

    InputTypesNamespace:
    if (!$namespace) {
        echo "Input thrift types namespace: ";
        $handle    = fopen("php://stdin", "r");
        $namespace = fgets($handle);
    }
    if (!$namespace) {
        goto InputTypesNamespace;
    }

    $namespace = trim($namespace, "\\/\r\n");

    $typesFile = $root . '/gen-php/' . $namespace . '.php';
    if (!file_exists($typesFile)) {
        exit('File not exists: ' . $typesFile);
    }

    $pre = get_declared_classes();
    include $typesFile;
    $post = get_declared_classes();

    $typesClasses = array_diff($post, $pre);
}

$content = explode("\r\n", file_get_contents($typesFile));

foreach ($typesClasses as $class) {
    $class = new ReflectionClass($class);
    if ($class->hasMethod('read')) {
        $method = $class->getMethod('read');
        for ($i = $method->getStartLine() - 1; $i < $method->getEndLine(); $i++) {
            unset($content[$i]);
        }

        $method = $class->getMethod('write');
        for ($i = $method->getStartLine() - 1; $i < $method->getEndLine(); $i++) {
            unset($content[$i]);
        }
    }
}


$outputFile = $root . '/source/' . $namespace . '/Types.php';
if (!is_dir(dirname($outputFile))) {
    mkdirs(dirname($outputFile));
}
file_put_contents($outputFile, implode("\n", $content));

if (!function_exists('mkdirs')) {
    function mkdirs($path, $mode = 0777)
    {
        $paths  = explode(
            DIRECTORY_SEPARATOR,
            str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, trim($path, '/\/'))
        );

        //windows
        if ($paths[0][1] == ':') {
            $root = array_shift($paths);
        } else {
            $root = '';
        }

        $result = false;
        foreach ($paths as $dir) {
            if ($dir == '.' || $dir == '..') {
                continue;
            }

            $root .= '/' . $dir;
            if (!is_dir($root)) {
                echo "mkdir: $root\n";
                $result = mkdir($root);
            }
        }
        return $result;
    }
}