<?php
namespace SAR\models;

use \Slim\Slim;

/**
* Silabus
*
*/
class Silabus
{
    protected $core;

    function __construct() {
        $this->core = Slim::getInstance();
    }

    function selectAll()
    {
        $result = array();
        $sql = "SELECT * FROM SILABUS";
        $statement = $this->core->db->prepare($sql);
        if ($statement->execute()) {
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $result = 0;
        }
        return $result;
    }
}
