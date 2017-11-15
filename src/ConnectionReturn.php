<?php

namespace UWDOEM\Connection;

/**
 * Class ConnectionReturn
 *
 * Container class representing a data call to a UW web service
 *
 * @package UWDOEM\Connection
 */
class ConnectionReturn
{
    /** @var mixed */
    protected $data = null;

    /** @var array */
    protected $curlInfo = null;

    /**
     * @param mixed $data
     * @param array $curlInfo
     */
    public function __construct($data, array $curlInfo)
    {
        $this->data = $data;
        $this->curlInfo = $curlInfo;
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array|null
     */
    public function getInfo()
    {
        return $this->curlInfo;
    }
}
