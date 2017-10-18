<?php

namespace UWDOEM\Connection;

interface ConnectionInterface
{

    /**
     * @param string       $baseUrl
     * @param string       $sslKey
     * @param string       $sslCert
     * @param string|null  $sslKeyPassword
     * @param boolean|null $verbose
     * @param array        $options
     * @throws \Exception If the provided $sslKey or $sslCert paths are not valid.
     */
    public function __construct(
        $baseUrl,
        $sslKey,
        $sslCert,
        $sslKeyPassword = null,
        $verbose = null,
        array $options = []
    );

    /** @return void */
    public function __destruct();

    /**
     * Execute a GET request to a given URL, with optional parameters.
     *
     * @param string   $url
     * @param string[] $params Array of query parameter $key=>$value pairs.
     * @return mixed The server's response
     */
    public function execGET($url, array $params = []);

    /**
     * Execute a POST request to a given URL, with optional parameters.
     *
     * @param string   $url
     * @param string[] $params Array of POST parameter $key=>$value pairs.
     * @return mixed The server's response.
     */
    public function execPOST($url, array $params = []);
}
