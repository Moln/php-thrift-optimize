<?php
use Hbase\TGet;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TSocket;
use Hbase\THBaseServiceClient;

include_once 'vendor/autoload.php';
//include_once "gen-php/Hbase/THBaseService.php";
//include_once "gen-php/Hbase/Types.php";

include_once "source/Hbase/THBaseService.php";
include_once "source/Hbase/Types.php";

$socket = new TSocket('192.168.39.18', '9090');

$socket->setSendTimeout(10000);
$socket->setRecvTimeout(20000);
$transport = new TBufferedTransport($socket);
$protocol = new TBinaryProtocol($transport, true, true);
$client = new THBaseServiceClient($protocol);
$transport->open();

$get = new TGet(array('row' => 'row-1'));

//$result = $client->exists('test', $get);
//echo "\n exists: ";
//var_dump($result);
//
//$result = $client->get('test', $get);
//echo "\n get: ";
//var_dump($result);

//$result = $client->getMultiple('test', [new TGet(['row' => 'row2'])]);
//echo "\n getMultiple: ";
//var_dump($result);
//
$client->put(
    'test',
    new \Hbase\TPut(['row' => 'row2', 'columnValues' => array(new \Hbase\TColumnValue())]));
echo "\n put: success";

$transport->close();