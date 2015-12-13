<?php

require_once dirname(__FILE__) ."/../src/Connection.php";
require_once dirname(__FILE__) ."/../src/Parser.php";

class MockConnectionInstance {

    public $lastUrl;
    public $lastParams;

    protected function makeSlug($url) {
        $url = str_replace(["https://ws.admin.washington.edu/identity/v1/"], [""], $url);
        $url = str_replace(["https://ws.admin.washington.edu/student/v5/"], [""], $url);
        $url = str_replace(["?", "/", ".", "="], ["-", "-", "-", "-"], $url);

        if (strlen($url) > 63) {
            $url = md5($url);
        }

        return $url;
    }


    public function execGET($url, $params = []) {
        $this->lastUrl = $url;
        $this->lastParams = $params;

        return file_get_contents(getcwd() . "/test/responses/{$this->makeSlug($url)}.json");
    }

    public function execPOST($url, $params = []) {
        $this->lastUrl = $url;
        $this->lastParams = $params;
        return file_get_contents(getcwd() . "/test/StaffStudentPerson.json");
    }
}

$myMockConnectionInstance = new MockConnectionInstance();
