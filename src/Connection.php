<?php

namespace UWDOEM\Connection;

/**
 * Class Connection
 *
 * Singleton class representing our connections to Person Web Service and Student Web Service
 *
 * @package UWDOEM\Person
 */
class Connection
{
    protected $baseurl;
    protected $curl;
    protected $options = [];

    public function __construct($baseurl, $sslkey, $sslcert, $sslkeypasswd = null)
    {

        $this->baseurl = $baseurl;

        if (!file_exists($sslkey)) {
            throw new \Exception("No such file found for SSL key at $sslkey.");
        }

        if (!file_exists($sslcert)) {
            throw new \Exception("No such file found for SSL certificate at $sslcert.");
        }

        // Get cURL resource
        $this->curl = curl_init();

        // Set cURL parameters
        curl_setopt_array($this->curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSLKEY => $sslkey,
            CURLOPT_SSLCERT => $sslcert,
        ));

        if (!is_null($sslkeypasswd)) {
            curl_setopt_array($this->curl, array(
                CURLOPT_SSLKEYPASSWD => $sslkeypasswd,
            ));
        }
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * Execute a GET request to a given URL, with optional parameters.
     *
     * @param string $url
     * @param string[] $params Array of query parameter $key=>$value pairs
     * @return mixed The server's response
     */
    public function execGET($url, $params = [])
    {
        $url = $this->baseurl . $url;

        // Build the query from the parameters
        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        // Set request options
        $this->addOptions([
            CURLOPT_URL => $url,
            CURLOPT_POST => false,
            CURLOPT_POSTFIELDS => [],
        ]);

        return $this->exec();
    }

    /**
     * Execute a POST request to a given URL, with optional parameters.
     *
     * @param string $url
     * @param string[] $params Array of POST parameter $key=>$value pairs
     * @return mixed The server's response
     */
    public function execPOST($url, $params = [])
    {
        $url = $this->baseurl . $url;

        $this->addOptions([
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
        ]);

        return $this->exec();
    }

    protected function addOptions(array $options)
    {
        $this->options = $this->options + $options;
    }

    protected function addXUwActAs()
    {
        // Grab the remote user, for inclusion on the
        if (array_key_exists("REMOTE_USER", $_SERVER)) {

            $user = $_SERVER["REMOTE_USER"];
            $user = strtok($user, '@');

            if (!array_key_exists(CURLOPT_HTTPHEADER, $this->options)) {
                $this->options[CURLOPT_HTTPHEADER] = [];
            }

            $this->options[CURLOPT_HTTPHEADER][] = ["X-UW-ACT-AS: $user"];
        }
    }

    protected function exec()
    {
        $this->addXUwActAs();

        curl_setopt_array($this->curl, $this->options);

        $resp = curl_exec($this->curl);

        if (curl_errno($this->curl)) {
            throw new \Exception('Request Error:' . curl_error($this->curl));
        }

        return $resp;
    }
}
