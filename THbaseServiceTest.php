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
$protocol  = new TBinaryProtocol($transport, true, true);
$client    = new THBaseServiceClient($protocol);
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
//$client->put(
//    'test',
//    new \Hbase\TPut([
//        'row' => 'row3',
//        'columnValues' => [
//            new \Hbase\TColumnValue([
//                'family' => 'cl1',
//                'qualifier' => 'q1',
//                'value' => uniqid(),
//            ]),
//            new \Hbase\TColumnValue([
//                'family' => 'cl1',
//                'qualifier' => 'q2',
//                'value' => uniqid(),
//            ]),
//        ]
//    ])
//);
//echo "\n put: success";

//$result = $client->checkAndPut('test', 'row3', 'cl1', 'q1', 'aaaaaaaaaa',
//    new \Hbase\TPut([
//        'row' => 'row3',
//        'columnValues' => [
//            new \Hbase\TColumnValue([
//                'family' => 'cl1',
//                'qualifier' => 'q2',
//                'value' => uniqid(),
//            ]),
//        ]
//    ])
//);
//echo "\n checkAndPut: ";
//var_dump($result);

//$client->putMultiple('test', array(
//        new \Hbase\TPut([
//            'row'          => 'row3',
//            'columnValues' => [
//                new \Hbase\TColumnValue([
//                    'family'    => 'cl1',
//                    'qualifier' => 'q1',
//                    'value'     => mt_rand(),
//                ]),
//            ]
//        ]),
//        new \Hbase\TPut([
//            'row'          => 'row3',
//            'columnValues' => [
//                new \Hbase\TColumnValue([
//                    'family'    => 'cl1',
//                    'qualifier' => 'q2',
//                    'value'     => mt_rand(),
//                ]),
//            ]
//        ]),
//        new \Hbase\TPut([
//            'row'          => 'row3',
//            'columnValues' => [
//                new \Hbase\TColumnValue([
//                    'family'    => 'cl1',
//                    'qualifier' => 'q3',
//                    'value'     => mt_rand(),
//                ]),
//            ]
//        ]),
//    ));
//echo "\n putMultiple: success";

//$client->deleteSingle(
//    'test',
//    new \Hbase\TDelete([
//        'row' => 'row3',
//        'columns' => [
//            new \Hbase\TColumn([
//                'family' => 'cl1',
//                'qualifier' => 'q2',
//            ])
//        ],
//    ])
//);
//echo "\n deleteSingle: success";

//$client->deleteMultiple(
//    'test',
//    [
//        new \Hbase\TDelete([
//            'row'     => 'row3',
//            'columns' => [
//                new \Hbase\TColumn([
//                    'family'    => 'cl1',
//                    'qualifier' => 'q2',
//                ])
//            ],
//        ]),
//        new \Hbase\TDelete([
//            'row'     => 'row3',
//            'columns' => [
//                new \Hbase\TColumn([
//                    'family'    => 'cl1',
//                    'qualifier' => 'q3',
//                ])
//            ],
//        ]),
//    ]
//);
//echo "\n deleteMultiple: success";

//$result = $client->checkAndDelete('test', 'row3', 'cl1', 'q1', '1111',
//    new \Hbase\TDelete([
//        'row' => 'row3',
//        'columns' => [
//            new \Hbase\TColumn([
//                'family'    => 'cl1',
//                'qualifier' => 'q3',
//            ])
//        ]
//    ])
//);
//echo "\n checkAndDelete: ";
//var_dump($result);

$result = $client->increment(
    'product',
    new \Hbase\TIncrement([
        'row' => 'index',
        'columns' => [
            new \Hbase\TColumnIncrement([
                'family'    => 'counter',
                'qualifier' => 'num',
//                'amount'=>5,
            ]),
        ],
    ])
);
echo "\n increment: ";
var_dump($result);
exit;


//$result = $client->increment(
//    'test',
//    new \Hbase\TIncrement([
//        'row' => 'row3',
//        'columns' => [
//            new \Hbase\TColumnIncrement([
//                'family'    => 'cl1',
//                'qualifier' => 'q3',
//            ]),
//            new \Hbase\TColumnIncrement([
//                'family'    => 'cl1',
//                'qualifier' => 'q4',
//            ]),
//        ],
//    ])
//);
//echo "\n increment: ";
//var_dump($result);

//Invalid method
//$result = $client->append(
//    'test',
//    new \Hbase\TAppend([
//        'row' => 'row3',
//        'columns' => [
//            new \Hbase\TColumnValue([
//                'family'    => 'cl1',
//                'qualifier' => 'q1',
//                'value'     => uniqid(),
//            ]),
//            new \Hbase\TColumnValue([
//                'family'    => 'cl1',
//                'qualifier' => 'q2',
//                'value'     => uniqid(),
//            ]),
//        ],
//    ])
//);
//echo "\n increment: ";
//var_dump($result);

//$id = $result = $client->openScanner(
//    'test',
//    new \Hbase\TScan([
//        'startRow' => 'row3',
//        'endRow' => 'row4',
//    ])
//);
//echo "\n openScanner: ";
//var_dump($result);
//
//$result = $client->getScannerRows($id, 10);
//echo "\n getScannerRows: ";
//var_dump($result);


//bug
//$client->mutateRow(
//    'test',
//    new \Hbase\TRowMutations([
//        'row'       => 'row3',
//        'mutations' => [
//            new \Hbase\TMutation([
////                'put'          => new \Hbase\TPut([
////                        'row'          => 'row3',
////                        'columnValues' => [
////                            new \Hbase\TColumnValue([
////                                'family'    => 'cl1',
////                                'qualifier' => 'q5',
////                                'value'     => mt_rand(),
////                            ]),
////                        ]
////                    ]),
//                'deleteSingle' => new \Hbase\TDelete([
//                        'row'     => 'row3',
//                        'columns' => [
//                            new \Hbase\TColumn([
//                                'family'    => 'cl1',
//                                'qualifier' => 'q3',
//                            ])
//                        ]
//                    ])
//            ])
//        ]
//    ])
//);
//echo "\n mutateRow: success";


//$result = $client->getScannerResults(
//    'test',
//    new \Hbase\TScan([
//        'startRow' => 'row3',
//        'endRow' => 'row4',
//    ]),
//    10
//);
//echo "\n getScannerResults: ";
//var_dump($result);

$transport->close();