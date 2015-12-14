<?php

use UWDOEM\Connection\Connection;

class MockConnection extends Connection {

    public $curl;

    public function getCurl() {
        return $this->curl;
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
}