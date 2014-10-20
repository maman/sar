<?php

/**
* Silabus
*
*/

namespace SAR\models;

use SAR\lib\Core;
use CrazyCodr\Pdo\Oci8;

class Silabus
{
    protected $core;

    function __construct() {
        $this->core = Core::getInstance();
    }

    function selectAll()
    {
        $result = array();
        $sql = "SELECT * FROM SILABUS";
        $statement = $this->core->db->prepare($sql);
        if ($statement->execute()) {
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $result = 0;
        }
        return $result;
    }
}
