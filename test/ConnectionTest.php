<?php

use UWDOEM\Connection\Connection;

class MockConnection extends Connection {

    public function getCurl() {
        return $this->curl;
    }

    public function getOptions() {
        return $this->options;
    }

    protected function makeSlug($url) {
        $url = str_replace([$this->baseurl], [""], $url);
        $url = str_replace(["?", "/", ".", "="], ["-", "-", "-", "-"], $url);

        if (strlen($url) > 63) {
            $url = md5($url);
        }

        return $url;
    }

    public function exec() {
        $this->addXUwActAs();

        curl_setopt_array($this->curl, $this->options);

        $url = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);

        return file_get_contents(getcwd() . "/test/responses/{$this->makeSlug($url)}.json");
    }
    
}

class ConnectionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException              \Exception
     * @expectedExceptionMessageRegExp #No such file found for SSL key at.*#
     */
    public function testErrorNoSuchSSLKey() {
        new MockConnection(
            "http://localhost/",
            getcwd() . "/" . (string)rand() . ".key",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            "self-signed-password"
        );
    }

    /**
     * @expectedException              \Exception
     * @expectedExceptionMessageRegExp #No such file found for SSL certificate at.*#
     */
    public function testErrorNoSuchSSLCert() {
        new MockConnection(
            "http://localhost/",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            getcwd() . "/" . (string)rand() . ".crt",
            "self-signed-password"
        );
    }

    public function testCreateInstance() {

        $connection = new MockConnection(
            "http://localhost/",
            getcwd() . "/test/test-certs/self.signed.test.certs.key",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            "self-signed-password"
        );

        $this->assertTrue($connection instanceof Connection);
    }

    public function testGet() {

        $connection = new MockConnection(
            "http://localhost/",
            getcwd() . "/test/test-certs/self.signed.test.certs.key",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            "self-signed-password"
        );

        $resp = $connection->execGET("person-javerage-full.json");
        $resp = json_decode($resp, true);

        $this->assertEquals("James Average Student", $resp["DisplayName"]);

        $this->assertEquals(0, $connection->getOptions()[CURLOPT_POST]);
    }

    public function testPost() {

        $connection = new MockConnection(
            "http://localhost/",
            getcwd() . "/test/test-certs/self.signed.test.certs.key",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            "self-signed-password"
        );

        $resp = $connection->execPOST("person-javerage-full.json");
        $resp = json_decode($resp, true);

        $this->assertEquals("James Average Student", $resp["DisplayName"]);

        $this->assertEquals(1, $connection->getOptions()[CURLOPT_POST]);
    }
}