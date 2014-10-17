<?php
use Hbase\AlreadyExists;
use Hbase\ColumnDescriptor;
use Hbase\HbaseClient;
use Hbase\Mutation;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TSocket;

include_once 'vendor/autoload.php';
//            "vendor/apache/Hbase/THBaseService.php",
//            "vendor/apache/Hbase/Types.php",
include_once "gen-php/Hbase/Hbase.php";
include_once "gen-php/Hbase/Types.php";

$socket = new TSocket('222.73.243.182', '9090');
$socket->setSendTimeout(10000); // Ten seconds (too long for production, but this is just a demo ;)
$socket->setRecvTimeout(20000); // Twenty seconds
$transport = new TBufferedTransport($socket);
$protocol  = new TBinaryProtocol($transport);
$client    = new HbaseClient($protocol);
$transport->open();
echo nl2br("listing tables...\n");
$tables = $client->getTableNames();
sort($tables);
foreach ($tables as $name) {
    echo nl2br("  found: {$name}\n");
}
$columns = array(
    new ColumnDescriptor(array(
        'name'        => 'entry:',
        'maxVersions' => 10
    )),
    new ColumnDescriptor(array(
        'name' => 'unused:'
    ))
);
$t       = "table1";
echo("creating table: {$t}\n");
try {
    $client->createTable($t, $columns);
} catch (AlreadyExists $ae) {
    echo("WARN: {$ae->message}\n");
}
$t = "test";
echo("column families in {$t}:\n");
$descriptors = $client->getColumnDescriptors($t);
asort($descriptors);
foreach ($descriptors as $col) {
    echo("  column: {$col->name}, maxVer: {$col->maxVersions}\n");
}
$t = "table1";
echo("column families in {$t}:\n");
$descriptors = $client->getColumnDescriptors($t);
asort($descriptors);
foreach ($descriptors as $col) {
    echo("  column: {$col->name}, maxVer: {$col->maxVersions}\n");
}
$t         = "table1";
$row       = "row_name";
$valid     = "foobar-\xE7\x94\x9F\xE3\x83\x93";
$mutations = array(
    new Mutation(array(
        'column' => 'entry:foo',
        'value'  => $valid
    )),
);
// 多记录批量提交(200提交一次时测试小记录大概在5000/s左右):  $rows = array('timestamp'=>$timestamp, 'columns'=>array('txt:col1'=>$col1, 'txt:col2'=>$col2, 'txt:col3'=>$col3));  $records = array(rowkey=>$rows,...);  $batchrecord = array();  foreach ($records as $rowkey => $rows) {      $timestamp = $rows['timestamp'];      $columns = $rows['columns'];      // 生成一条记录      $record = array();      foreach($columns as $column => $value) {          $col = new Mutation(array('column'=>$column, 'value'=>$value));          array_push($record, $col);      }      // 加入记录数组      $batchTmp = new BatchMutation(array('row'=>$rowkey, 'mutations'=>$record));      array_push($batchrecord, $batchTmp);  }  $ret = $hbase->mutateRows('test', $batchrecord);

$client->mutateRow($t, $row, $mutations, null);
$table_name   = "table1";
$row_name     = 'row_name';
$fam_col_name = 'entry:foo';
$arr          = $client->get($table_name, $row_name, $fam_col_name, null);
// $arr = array
foreach ($arr as $k => $v) {
// $k = TCell
    echo("value = {$v->value} , <br>  ");
    echo("timestamp = {$v->timestamp}  <br>");
}
$table_name = "table1";
$row_name   = "row_name";
$arr        = $client->getRow($table_name, $row_name, null);
// $client->getRow return a array
foreach ($arr as $k => $TRowResult) {
// $k = 0 ; non-use
// $TRowResultTRowResult = TRowResult
    var_dump($TRowResult);
}

//scannerOpenWithStop($tableName, $startRow, $stopRow, $columns);
$table_name = 'zTest';
$startRow   = "9-9-20120627-";
$stopRow    = "9-9-20120627_";
$columns    = Array('info:');
$result     = $client->scannerOpenWithStop($table_name, $startRow, $stopRow, $columns, null);
while (true) {
    $record = $client->scannerGet($result);
    if ($record == null) {
        break;
    }

    foreach ($record as $TRowResult) {
        $row    = $TRowResult->row;
        $column = $TRowResult->columns;
        foreach ($column as $family_column => $Tcell) {
            echo("$family_column={$Tcell->value}<br>");
            echo("timestamp is $Tcell->timestamp");
        }
    }
}
$transport->close();