<?php

namespace SAR\models;

use SAR\lib\Core;

/**
* Login
*/
class Login
{

    function __construct()
    {
        $this->core = Core::getInstance();
    }

    public function authenticate($username, $password)
    {
        if ($username = "asd" && $password = "def") {
            return true;
        } else {
            return false;
        }
    }
}
