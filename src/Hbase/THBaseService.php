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

    public function getMultiple($table, $gets);

    public function put($table, TPut $put);

    public function checkAndPut($table, $row, $family, $qualifier, $value, TPut $put);

    public function putMultiple($table, $puts);

    public function deleteSingle($table, TDelete $deleteSingle);

    public function deleteMultiple($table, $deletes);

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
     * @return \Hbase\TGet[]
     */
    public function getMultiple($table, $gets)
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
     * @return mixed
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
     * @return mixed
     */
    public function putMultiple($table, $puts)
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
     * @return mixed
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
    public function deleteMultiple($table, $deletes)
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
     * @return mixed
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
     * @return mixed
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
                'etype' => 12,
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
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
                'etype' => 12,
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


/**
 * Class TProtocolController
 *
 * @package Hbase
 */
class TProtocolController
{

    protected $input = null;
    protected $output = null;

    protected $seqid = 0;

    public function __construct(TProtocol $input, TProtocol $output = null)
    {
        $this->input  = $input;
        $this->output = $output ? : $input;
    }

    private function write($args)
    {
        $xfer = 0;
        $xfer += $this->output->writeStructBegin(get_class($this));

        foreach ($args as $key => $arg) {
            if ($arg['value'] !== null) {
                $this->writeArgByType($key, $arg, $xfer);
            }
        }

        $xfer += $this->output->writeFieldStop();
        $xfer += $this->output->writeStructEnd();
        return $xfer;
    }

    private function writeArgByType($key, $item, &$xfer)
    {
        $output = $this->output;
        $type   = $item['type'];

        $callbacks = array(
            TType::BOOL   =>
                function (&$xfer, $item) use ($output) {
                    $xfer += $output->writeBool($item['value']);
                },
            TType::STRING =>
                function (&$xfer, $item) use ($output) {
                    $xfer += $output->writeString($item['value']);
                },
            TType::STRUCT =>
                function (&$xfer, $item) use ($output) {
                    if (is_object($item['value']) && isset($item['value']::$_TSPEC)) {

                        $subArgs = $item['value']::$_TSPEC;
                        foreach ($subArgs as &$subArg) {
                            $subArg['value'] = $item['value']->{$subArg['var']};
                        }

                        $xfer += $this->write($subArgs);
                    } else {
                        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
                    }
                },
        );

        $callbacks[TType::LST] = function (&$xfer, $items) use ($output, $callbacks) {
            if (!is_array($items['value'])) {
                throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
            }
            $output->writeListBegin($items['etype'], count($items['value']));

            foreach ($items['value'] as $item) {
                $callbacks[$items['elem']['type']]($xfer, $item);
            }
            $output->writeListEnd();
            $xfer += $output->writeFieldEnd();
        };

        if (isset($callbacks[$type])) {
            $xfer += $output->writeFieldBegin($item['var'], $type, $key);
            $callbacks[$type]($xfer, $item);
            $xfer += $output->writeFieldEnd();
        } else {
            throw new \InvalidArgumentException('Invalid type:' . $type);
        }
    }

    /**
     * @param $args
     * @return int
     */
    private function read(&$args)
    {
        $xfer  = 0;
        $fname = null;
        $ftype = 0;
        $fid   = 0;
        $xfer += $this->input->readStructBegin($fname);
        while (true) {
            $xfer += $this->input->readFieldBegin($fname, $ftype, $fid);
            if ($ftype == TType::STOP) {
                break;
            }

            if (isset($args[$fid])) {
                $this->setArg($args[$fid], $ftype, $xfer);
            } else {
                $xfer += $this->input->skip($ftype);
                break;
            }

            $xfer += $this->input->readFieldEnd();
        }
        $xfer += $this->input->readStructEnd();
        return $xfer;
    }


    private function setArg(&$item, $ftype, &$xfer)
    {
        $type          = $item['type'];
        $item['value'] = null;

        $callbacks = array(
            TType::BOOL   =>
                function (&$item, &$xfer) {
                    $xfer += $this->input->readBool($item['value']);
                },
            TType::BYTE   =>
                function (&$item, &$xfer) {
                    $xfer += $this->input->readByte($item['value']);
                },
            TType::DOUBLE =>
                function (&$item, &$xfer) {
                    $xfer += $this->input->readDouble($item['value']);
                },
            TType::I16    =>
                function (&$item, &$xfer) {
                    $xfer += $this->input->readI16($item['value']);
                },
            TType::I32    =>
                function (&$item, &$xfer) {
                    $xfer += $this->input->readI32($item['value']);
                },
            TType::I64    =>
                function (&$item, &$xfer) {
                    $xfer += $this->input->readI64($item['value']);
                },
            TType::STRING =>
                function (&$item, &$xfer) {
                    $xfer += $this->input->readString($item['value']);
                },
            TType::STRUCT =>
                function (&$item, &$xfer) {
                    $class = $item['class'];

                    $item['value'] = new $class();

                    if (isset($item['value']::$_TSPEC)) {
                        $subArgs = $item['value']::$_TSPEC;
                        $xfer += $this->read($subArgs);

                        foreach ($subArgs as $subArg) {
                            if (isset($subArg['value'])) {
                                $item['value']->{$subArg['var']} = $subArg['value'];
                            }
                        }
                    } else {
                        throw new \InvalidArgumentException('Invalid argument: ' . $class);
                    }

                }
        );

        $callbacks[TType::LST] = function (&$items, &$xfer) use ($callbacks) {
            $items['value'] = array();
            $size           = 0;
            $etype          = 0;
            $xfer += $this->input->readListBegin($etype, $size);

            for ($i = 0; $i < $size; ++$i) {
                $item = $items['elem'] + array('value' => null);
                $callbacks[$items['elem']['type']]($item, $xfer);
                $items['value'] = $item;
            }
            $xfer += $this->input->readListEnd();
        };

        if (isset($callbacks[$type])) {
            $callbacks[$type]($item, $xfer);
        } else {
            $xfer += $this->input->skip($ftype);
        }
    }

    public function sendCommand($command, $args = array())
    {
        $this->output->writeMessageBegin($command, TMessageType::CALL, $this->seqid);
        $this->write($args);
        $this->output->writeMessageEnd();
        $this->output->getTransport()->flush();
    }

    /**
     * @param array $args
     * @throws \Thrift\Exception\TApplicationException
     * @throws \Exception
     * @return mixed
     */
    public function recv($args = array())
    {
        $rseqid = 0;
        $fname  = null;
        $mtype  = 0;

        $this->input->readMessageBegin($fname, $mtype, $rseqid);
        if ($mtype == TMessageType::EXCEPTION) {
            $x = new TApplicationException();
            $x->read($this->input);
            $this->input->readMessageEnd();
            throw $x;
        }

        $this->read($args);
        $this->input->readMessageEnd();

        foreach ($args as $arg) {
            if ($arg['type'] == TType::STRUCT && $arg['value'] instanceof \Exception) {
                throw $arg['value'];
            } else if ($arg['value'] !== null) {
                return $arg['value'];
            }
        }
        return null;
    }
}
