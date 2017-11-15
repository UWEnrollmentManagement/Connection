<?php

namespace UWDOEM\Connection\Test;

use UWDOEM\Connection\Connection;

class MockConnection extends Connection
{

    public function getCurl()
    {
        return $this->curl;
    }

    public function getOptions()
    {
        return $this->options;
    }

    protected function makeSlug($url)
    {
        $url = str_replace([$this->baseUrl], [""], $url);
        $url = str_replace(["?", "&", "/", ".", "="], ["-q-", "-and-", "-", "-", "-"], $url);

        if (strlen($url) > 63) {
            $url = md5($url);
        }

        return $url;
    }

    protected function doExec()
    {
        $url = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        $info = curl_getinfo($this->curl);
        $data = file_get_contents(getcwd() . "/test/responses/{$this->makeSlug($url)}");
        return new \UWDOEM\Connection\ConnectionReturn($data, $info);
    }
}
