<?php
namespace Hbase;

use Thrift\Exception\TApplicationException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Type\TMessageType;
use Thrift\Type\TType;

interface THBaseServiceIf
{
    public function exists($table, TGet $get);

    public function get($table, TGet $get);

    public function getMultiple($table, array $gets);

    public function put($table, TPut $put);

    public function checkAndPut($table, $row, $family, $qualifier, $value, TPut $put);

    public function putMultiple($table, array $puts);

    public function deleteSingle($table, TDelete $deleteSingle);

    public function deleteMultiple($table, array $deletes);

    public function checkAndDelete($table, $row, $family, $qualifier, $value, TDelete $deleteSingle);

    public function increment($table, TIncrement $increment);

    public function append($table, TAppend $append);

    public function openScanner($table, TScan $scan);

    public function getScannerRows($scannerId, $numRows);

    public function closeScanner($scannerId);

    public function mutateRow($table, TRowMutations $rowMutations);

    public function getScannerResults($table, TScan $scan, $numRows);
}

if (!class_exists('\Hbase\TprotocolController') && file_exists(__DIR__ . '/TProtocolController.php')) {
    include_once __DIR__ . '/TProtocolController.php';
}

class THBaseServiceClient implements THBaseServiceIf
{
    public function __construct(TProtocol $input, TProtocol $output = null)
    {
        $this->protocol = new TProtocolController($input, $output);
    }

    private function sendCommand($command, $args = array())
    {
        $this->protocol->sendCommand($command, $args);
    }

    /**
     * @param array $args
     * @throws \Thrift\Exception\TApplicationException
     * @throws \Exception
     * @return mixed
     */
    private function recv($args = array())
    {
        return $this->protocol->recv($args);
    }

    /**
     * @param string $table
     * @param \Hbase\TGet $get
     * @return bool
     */
    public function exists($table, TGet $get)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'get',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TGet',
                'value' => $get,
            ),
        );
        $result = array(
            0 => array(
                'var'  => 'success',
                'type' => TType::BOOL,
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TGet $get
     * @return \Hbase\TResult
     */
    public function get($table, TGet $get)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'get',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TGet',
                'value' => $get,
            ),
        );
        $result = array(
            0 => array(
                'var'   => 'success',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TResult',
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TGet[] $gets
     * @return \Hbase\TResult[]
     */
    public function getMultiple($table, array $gets)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'gets',
                'type'  => TType::LST,
                'etype' => TType::STRUCT,
                'elem'  => array(
                    'type'  => TType::STRUCT,
                    'class' => '\\Hbase\\TGet',
                ),
                'value' => $gets,
            ),
        );
        $result = array(
            0 => array(
                'var'   => 'success',
                'type'  => TType::LST,
                'etype' => TType::STRUCT,
                'elem'  => array(
                    'type'  => TType::STRUCT,
                    'class' => '\\Hbase\\TResult',
                ),
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TPut $put
     * @return void
     */
    public function put($table, TPut $put)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'put',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TPut',
                'value' => $put,
            ),
        );
        $result = array(
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param string $row
     * @param string $family
     * @param string $qualifier
     * @param string $value
     * @param \Hbase\TPut $put
     * @return bool
     */
    public function checkAndPut($table, $row, $family, $qualifier, $value, TPut $put)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'row',
                'type'  => TType::STRING,
                'value' => $row,
            ),
            3 => array(
                'var'   => 'family',
                'type'  => TType::STRING,
                'value' => $family,
            ),
            4 => array(
                'var'   => 'qualifier',
                'type'  => TType::STRING,
                'value' => $qualifier,
            ),
            5 => array(
                'var'   => 'value',
                'type'  => TType::STRING,
                'value' => $value,
            ),
            6 => array(
                'var'   => 'put',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TPut',
                'value' => $put,
            ),
        );
        $result = array(
            0 => array(
                'var'  => 'success',
                'type' => TType::BOOL,
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TPut[] $puts
     * @return void
     */
    public function putMultiple($table, array $puts)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'puts',
                'type'  => TType::LST,
                'etype' => TType::STRUCT,
                'elem'  => array(
                    'type'  => TType::STRUCT,
                    'class' => '\\Hbase\\TPut',
                ),
                'value' => $puts,
            ),
        );
        $result = array(
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TDelete $deleteSingle
     * @return void
     */
    public function deleteSingle($table, TDelete $deleteSingle)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'deleteSingle',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TDelete',
                'value' => $deleteSingle,
            ),
        );
        $result = array(
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TDelete[] $deletes
     * @return \Hbase\TDelete[]
     */
    public function deleteMultiple($table, array $deletes)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'deletes',
                'type'  => TType::LST,
                'etype' => TType::STRUCT,
                'elem'  => array(
                    'type'  => TType::STRUCT,
                    'class' => '\\Hbase\\TDelete',
                ),
                'value' => $deletes,
            ),
        );
        $result = array(
            0 => array(
                'var'   => 'success',
                'type'  => TType::LST,
                'etype' => TType::STRUCT,
                'elem'  => array(
                    'type'  => TType::STRUCT,
                    'class' => '\\Hbase\\TDelete',
                ),
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param string $row
     * @param string $family
     * @param string $qualifier
     * @param string $value
     * @param \Hbase\TDelete $deleteSingle
     * @return bool
     */
    public function checkAndDelete($table, $row, $family, $qualifier, $value, TDelete $deleteSingle)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'row',
                'type'  => TType::STRING,
                'value' => $row,
            ),
            3 => array(
                'var'   => 'family',
                'type'  => TType::STRING,
                'value' => $family,
            ),
            4 => array(
                'var'   => 'qualifier',
                'type'  => TType::STRING,
                'value' => $qualifier,
            ),
            5 => array(
                'var'   => 'value',
                'type'  => TType::STRING,
                'value' => $value,
            ),
            6 => array(
                'var'   => 'deleteSingle',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TDelete',
                'value' => $deleteSingle,
            ),
        );
        $result = array(
            0 => array(
                'var'  => 'success',
                'type' => TType::BOOL,
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TIncrement $increment
     * @return \Hbase\TResult
     */
    public function increment($table, TIncrement $increment)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'increment',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIncrement',
                'value' => $increment,
            ),
        );
        $result = array(
            0 => array(
                'var'   => 'success',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TResult',
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TAppend $append
     * @return \Hbase\TResult
     */
    public function append($table, TAppend $append)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'append',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TAppend',
                'value' => $append,
            ),
        );
        $result = array(
            0 => array(
                'var'   => 'success',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TResult',
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TScan $scan
     * @return void
     */
    public function openScanner($table, TScan $scan)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'scan',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TScan',
                'value' => $scan,
            ),
        );
        $result = array(
            0 => array(
                'var'  => 'success',
                'type' => TType::I32,
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param  $scannerId
     * @param  $numRows
     * @return \Hbase\TResult[]
     */
    public function getScannerRows($scannerId, $numRows)
    {
        $args   = array(
            1 => array(
                'var'   => 'scannerId',
                'type'  => TType::I32,
                'value' => $scannerId,
            ),
            2 => array(
                'var'   => 'numRows',
                'type'  => TType::I32,
                'value' => $numRows,
            ),
        );
        $result = array(
            0 => array(
                'var'   => 'success',
                'type'  => TType::LST,
                'etype' => TType::STRUCT,
                'elem'  => array(
                    'type'  => TType::STRUCT,
                    'class' => '\\Hbase\\TResult',
                ),
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
            2 => array(
                'var'   => 'ia',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIllegalArgument',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param  $scannerId
     * @return void
     */
    public function closeScanner($scannerId)
    {
        $args   = array(
            1 => array(
                'var'   => 'scannerId',
                'type'  => TType::I32,
                'value' => $scannerId,
            ),
        );
        $result = array(
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
            2 => array(
                'var'   => 'ia',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIllegalArgument',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TRowMutations $rowMutations
     * @return void
     */
    public function mutateRow($table, TRowMutations $rowMutations)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'rowMutations',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TRowMutations',
                'value' => $rowMutations,
            ),
        );
        $result = array(
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }

    /**
     * @param string $table
     * @param \Hbase\TScan $scan
     * @param  $numRows
     * @return \Hbase\TResult[]
     */
    public function getScannerResults($table, TScan $scan, $numRows)
    {
        $args   = array(
            1 => array(
                'var'   => 'table',
                'type'  => TType::STRING,
                'value' => $table,
            ),
            2 => array(
                'var'   => 'scan',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TScan',
                'value' => $scan,
            ),
            3 => array(
                'var'   => 'numRows',
                'type'  => TType::I32,
                'value' => $numRows,
            ),
        );
        $result = array(
            0 => array(
                'var'   => 'success',
                'type'  => TType::LST,
                'etype' => TType::STRUCT,
                'elem'  => array(
                    'type'  => TType::STRUCT,
                    'class' => '\\Hbase\\TResult',
                ),
            ),
            1 => array(
                'var'   => 'io',
                'type'  => TType::STRUCT,
                'class' => '\\Hbase\\TIOError',
            ),
        );

        $this->sendCommand(__FUNCTION__, $args);
        return $this->recv($result);
    }
}
