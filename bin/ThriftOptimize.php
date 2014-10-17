<?php
$root = dirname(__DIR__);
include $root . '/vendor/autoload.php';

$service = null;
foreach ($argv as $arg) {
    if ($arg == 'hbase') {
        $service = 'Hbase\THBaseService';
    }
}

InputName:
if (!$service) {
    echo "Input thrift service: ";
    $handle  = fopen("php://stdin", "r");
    $service = fgets($handle);
}
if (!$service) {
    goto InputName;
}

$service      = trim($service, "\\/\r\n");
$servicePath  = str_replace(['\\', '/', '.'], DIRECTORY_SEPARATOR, $service);
$serviceClass = '\\' . str_replace(['/', '.'], '\\', $service) . 'Client';

$names      = explode('\\', trim($serviceClass, '\\'));
$className  = substr(array_pop($names), 0, -6);
$namespace  = implode('\\', $names);
$typesPath  = implode(DIRECTORY_SEPARATOR, $names) . DIRECTORY_SEPARATOR . 'Types';
$typesClass = str_replace(DIRECTORY_SEPARATOR, '\\', $typesPath);

$serviceFile = $root . '/gen-php/' . $servicePath . '.php';
$typesFile   = $root . '/gen-php/' . $typesPath . '.php';

if (!file_exists($serviceFile)) {
    exit('File not exists: ' . $serviceFile);
}
if (!file_exists($typesFile)) {
    exit('File not exists: ' . $typesFile);
}

$c1 = get_declared_classes();
include_once $serviceFile;
$c2 = get_declared_classes();
include_once $typesFile;
$c3           = get_declared_classes();
$typesClasses = array_diff($c3, $c2);

if (!class_exists($serviceClass)) {
    exit('class not exists: ' . $serviceClass);
}

$outputFile = $root . '/source/' . $servicePath . '.php';
if (!is_dir(dirname($outputFile))) {
    mkdirs(dirname($outputFile));
}
file_put_contents($outputFile, "<?php\n" . gen_class());

include 'TypesOptimize.php';

function gen_class()
{
    global $serviceClass, $namespace, $className;

    $interfaceTpls = $methodTpls = array();
    $classPrefix   = substr($serviceClass, 0, -6);
    $obj           = new ReflectionClass($serviceClass);
    $methods       = get_class_methods($serviceClass);

    foreach ($methods as $key => $method) {
        if ($method == '__construct' || substr($method, 0, 5) == 'send_' || substr($method, 0, 5) == 'recv_') {
            unset($methods[$key]);
            continue;
        }

        $class  = $classPrefix . '_' . $method . '_args';
        $args   = new $class;
        $class  = $classPrefix . '_' . $method . '_result';
        $result = new $class;

        list($interfaceTpl, $methodTpl) = methodTemplate($obj->getMethod($method), $args::$_TSPEC, $result::$_TSPEC);
        $interfaceTpls[] = $interfaceTpl;
        $methodTpls[]    = $methodTpl;
    }

    ob_start();
    include __DIR__ . '/THBaseService.phtml';
    $content = ob_get_contents();
    ob_clean();

    return $content;
}

function methodTemplate(ReflectionMethod $method, $args, $results)
{
    //注释返回类型
    $returnNote = 'void';
    $paramsNote = '';

    $types = get_types_constans();
    foreach ($args as $key => $arg) {
        $args[$key]['value'] = '{{' . $arg['var'] . '}}';
    }

    //参数和返回变量
    $argsStr      = var_export($args, true);
    $resultStr    = var_export($results, true);
    $methodParams = array(); //方法参数

    foreach ($args as $arg) {
        $methodParams[$arg['var']] = null;

        $argsStr = str_replace('\'{{' . $arg['var'] . '}}\'', '$' . $arg['var'], $argsStr);
        $argsStr = str_replace('\'type\' => ' . $arg['type'], '\'type\' => TType::' . $types[$arg['type']], $argsStr);
        $argsStr = preg_replace('/=> \n\s+array \\(/i', '=> array(', $argsStr);

        //注释参数类型
        if (isset($arg['class'])) {
            $paramType                 = $arg['class'];
            $classNames                = explode('\\', $paramType);
            $methodParams[$arg['var']] = end($classNames);
        } else if ($arg['type'] == \Thrift\Type\TType::BOOL) {
            $paramType = 'bool';
        } else if ($arg['type'] == \Thrift\Type\TType::STRING) {
            $paramType = 'string';
        } else if ($arg['type'] == \Thrift\Type\TType::LST) {
            $paramType                 = $arg['elem']['class'] . '[]';
            $argsStr                   = str_replace(
                [
                    '\'etype\' => ' . $arg['etype'],
                    '\'type\' => ' . $arg['etype'],
                ],
                [
                    '\'etype\' => TType::' . $types[$arg['etype']],
                    '\'type\' => TType::' . $types[$arg['etype']],
                ],
                $argsStr
            );
            $methodParams[$arg['var']] = 'array';
        } else {
            $paramType = '';
        }

        $paramsNote .= '     * @param ' . $paramType . ' $' . $arg['var'] . PHP_EOL;
    }
    foreach ($results as $arg) {
        $resultStr =
            str_replace('\'type\' => ' . $arg['type'], '\'type\' => TType::' . $types[$arg['type']], $resultStr);
        $resultStr = preg_replace('/=> \n\s+array \\(/', '=> array(', $resultStr);

        //注释返回类型
        if ($arg['var'] == 'success') {
            if (isset($arg['class'])) {
                $returnNote = $arg['class'];
            } else if ($arg['type'] == \Thrift\Type\TType::BOOL) {
                $returnNote = 'bool';
            } else if ($arg['type'] == \Thrift\Type\TType::STRING) {
                $returnNote = 'string';
            } else if ($arg['type'] == \Thrift\Type\TType::LST) {
                $returnNote = $arg['elem']['class'] . '[]';

                $resultStr = str_replace(
                    [
                        '\'etype\' => ' . $arg['etype'],
                        '\'type\' => ' . $arg['etype'],
                    ],
                    [
                        '\'etype\' => TType::' . $types[$arg['etype']],
                        '\'type\' => TType::' . $types[$arg['etype']],
                    ],
                    $resultStr
                );
            }
        }
    }

    //函数名称
    $methodName = $method->getName();

    //函数参数
    $params = [];
    foreach ($methodParams as $param => $type) {
        if ($type) {
            $params[] = $type . ' $' . $param;
        } else {
            $params[] = '$' . $param;
        }
    }
    $methodParams = implode(', ', $params);

    $paramsNote = rtrim($paramsNote);

    $methodTpl = <<<EOT

    /**
$paramsNote
     * @return $returnNote
     */
    public function $methodName($methodParams)
    {
        \$args   = $argsStr;
        \$result = $resultStr;

        \$this->sendCommand(__FUNCTION__, \$args);
        return \$this->recv(\$result);
    }

EOT;

    $interfaceTpl = <<<EOT
    public function $methodName($methodParams);

EOT;

    return array($interfaceTpl, $methodTpl);
}

function get_types_constans()
{
    $typeObj   = new ReflectionClass('\Thrift\Type\TType');
    $constants = $typeObj->getConstants();
    unset($constants['UTF7']);
    return array_flip($constants);
}


function mkdirs($path, $mode = 0777)
{
    $paths = explode(
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