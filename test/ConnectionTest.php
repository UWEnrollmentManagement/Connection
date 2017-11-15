<?php

namespace UWDOEM\Connection\Test;

use PHPUnit_Framework_TestCase;

use UWDOEM\Connection\Connection;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException              \Exception
     * @expectedExceptionMessageRegExp #No such file found for SSL key at.*#
     */
    public function testErrorNoSuchSSLKey()
    {
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
    public function testErrorNoSuchSSLCert()
    {
        new MockConnection(
            "http://localhost/",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            getcwd() . "/" . (string)rand() . ".crt",
            "self-signed-password"
        );
    }

    protected function makeConnection($options = [])
    {


        return new MockConnection(
            "http://localhost/",
            getcwd() . "/test/test-certs/self.signed.test.certs.key",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            "self-signed-password",
            true,
            $options
        );
    }

    public function testCreateInstance()
    {
        $connection = $this->makeConnection();

        $this->assertTrue($connection instanceof Connection);
    }

    public function testGet()
    {
        $connection = $this->makeConnection();

        $resp = $connection->execGET("person-javerage-full.json");
        $data = json_decode($resp->getData(), true);

        $this->assertEquals("http://localhost/person-javerage-full.json", $resp->getInfo()["url"]);

        $this->assertEquals("James Average Student", $data["DisplayName"]);

        $this->assertEquals(1, $connection->getOptions()[CURLOPT_HTTPGET]);
    }

    public function testGetParams()
    {
        $connection = $this->makeConnection();

        $resp = $connection->execGET("person-javerage-full.json", ["first" => 1, "second" => 2]);
        $data = json_decode($resp->getData(), true);

        $this->assertEquals("http://localhost/person-javerage-full.json?first=1&second=2", $resp->getInfo()["url"]);

        $this->assertEquals("James Average Student", $data["DisplayName"]);

        $this->assertEquals(1, $connection->getOptions()[CURLOPT_HTTPGET]);
    }

    public function testPost()
    {
        $connection = $this->makeConnection();

        $resp = $connection->execPOST("person-javerage-full.json");
        $data = json_decode($resp->getData(), true);

        $this->assertEquals("http://localhost/person-javerage-full.json", $resp->getInfo()["url"]);

        $this->assertEquals("James Average Student", $data["DisplayName"]);

        $this->assertEquals(1, $connection->getOptions()[CURLOPT_POST]);
    }

    public function testXUwActAs()
    {


        $user = "u" . (string)rand();

        $_SERVER["REMOTE_USER"] = $user;

        $options = [CURLOPT_HTTPHEADER => ["X-UW-ACT-AS: $user"]];

        $connection = $this->makeConnection($options);

        $resp = $connection->execGET("person-javerage-full.json", ["first" => 1, "second" => 2]);
        $data = json_decode($resp->getData(), true);

        $this->assertEquals("James Average Student", $data["DisplayName"]);

        $this->assertContains("X-UW-ACT-AS: $user", $connection->getOptions()[CURLOPT_HTTPHEADER]);
    }
}
