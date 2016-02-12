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

    /**
     * @param string      $baseUrl
     * @param string      $sslKey
     * @param string      $sslCert
     * @param string|null $sslKeyPassword
     * @throws \Exception If the provided $sslKey or $sslCert paths are not valid.
     */
    public function __construct($baseUrl, $sslKey, $sslCert, $sslKeyPassword = null)
    {

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
                CURLOPT_SSLCERT => $sslCert,
            ]
        );

        if ($sslKeyPassword !== null) {
            $this->addOptions([
                CURLOPT_SSLKEYPASSWD => $sslKeyPassword,
            ]);
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
     * @return mixed The server's response
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
     * @return mixed The server's response.
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
     * @return void
     */
    protected function addXUwActAs()
    {
        // Grab the remote user, for inclusion on the
        if (array_key_exists("REMOTE_USER", $_SERVER) === true) {

            $user = $_SERVER["REMOTE_USER"];
            $user = strtok($user, '@');

            if (array_key_exists(CURLOPT_HTTPHEADER, $this->options) === false) {
                $this->options[CURLOPT_HTTPHEADER] = [];
            }

            $this->options[CURLOPT_HTTPHEADER][] = "X-UW-ACT-AS: $user";
        }
    }

    /**
     * @return mixed
     * @throws \Exception If cURL encounters an error.
     */
    protected function exec()
    {
        $this->addXUwActAs();

        curl_setopt_array($this->curl, $this->options);

        $resp = $this->doExec();

        if (curl_errno($this->curl) !== 0) {
            throw new \Exception('Request Error:' . curl_error($this->curl));
        }

        return $resp;
    }

    /**
     * @return mixed
     */
    protected function doExec()
    {
        return curl_exec($this->curl);
    }
}
