<?php

namespace UWDOEM\Connection;

/**
 * Class Connection
 *
 * Container class representing a connection to a UW web service
 *
 * @package UWDOEM\Connection
 */
class Connection implements ConnectionInterface
{
    /** @var string */
    protected $baseUrl;

    /** @var resource */
    protected $curl;

    /** @var array */
    protected $options = [];

    /** @var resource */
    protected $logFile = null;

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
        $verbose = false,
        array $options = []
    ) {
    

        $this->baseUrl = $baseUrl;

        if (file_exists($sslKey) === false) {
            throw new \Exception("No such file found for SSL key at $sslKey.");
        }

        if (file_exists($sslCert) === false) {
            throw new \Exception("No such file found for SSL certificate at $sslCert.");
        }

        // Get cURL resource
        $this->curl = curl_init();

        // Set cURL parameters
        $this->addOptions(
            [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSLKEY => $sslKey,
                CURLOPT_SSLCERT => $sslCert
            ]
        );

        if ($sslKeyPassword !== null) {
            $this->addOptions([
                CURLOPT_SSLKEYPASSWD => $sslKeyPassword,
            ]);
        }

        $this->addOptions($options);

        if ($verbose === true) {
            /** @var  $logResource */
            $this->logFile = fopen('php://temp', 'w+');

            $this->addOptions(
                [
                    CURLOPT_VERBOSE => 1,
                    CURLOPT_STDERR => $this->logFile,
                ]
            );
        }
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * Execute a GET request to a given URL, with optional parameters.
     *
     * @param string   $url
     * @param string[] $params Array of query parameter $key=>$value pairs.
     * @return ConnectionReturn The server's response
     */
    public function execGET($url, array $params = [])
    {
        $url = $this->baseUrl . $url;

        // Build the query from the parameters
        if ($params !== []) {
            $url .= '?' . http_build_query($params);
        }

        // Set request options
        $this->addOptions([
            CURLOPT_URL => $url,
            CURLOPT_HTTPGET => true,
        ]);

        return $this->exec();
    }

    /**
     * Execute a POST request to a given URL, with optional parameters.
     *
     * @param string   $url
     * @param string[] $params Array of POST parameter $key=>$value pairs.
     * @return ConnectionReturn The server's response.
     */
    public function execPOST($url, array $params = [])
    {
        $url = $this->baseUrl . $url;

        $this->addOptions([
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
        ]);

        return $this->exec();
    }

    /**
     * @param array $options
     * @return void
     */
    protected function addOptions(array $options)
    {
        $this->options = $options + $this->options;
    }

    /**
     * @return ConnectionReturn
     * @throws \Exception If cURL encounters an error.
     */
    protected function exec()
    {
        curl_setopt_array($this->curl, $this->options);

        $resp = $this->doExec();

        if (curl_errno($this->curl) !== 0) {
            $errorText = 'Request Error:' . curl_error($this->curl);

            if ($this->logFile !== null) {
                rewind($this->logFile);
                $errorText .= " " . stream_get_contents($this->logFile);
            }

            throw new \Exception($errorText);
        }

        return $resp;
    }

    /**
     * @return ConnectionReturn
     */
    protected function doExec()
    {
        $resp = curl_exec($this->curl);
        $info = curl_getinfo($this->curl);
        return new ConnectionReturn($resp, $info);
    }
}
