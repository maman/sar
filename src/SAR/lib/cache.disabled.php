<?php

namespace SAR\lib;

use SAR\lib\Core;
use CrazyCodr\Pdo\Oci8;

/**
* PDO Caching Mechanism
* via Memcached & php_memcached
*/
class Cache
{
    protected $core;

    function __construct() {
        $this->core = Core::getInstance();
    }

    public function cache_query($sqlselect, $params)
    {
        $cache_name = 'querycache-' . md5(serialize(array($sqlselect, $params)));
        $cache_result = $this->core->mcc->get($cache_name);

        if (!$cache_result) {
            $statement = $this->core->db->prepare($sqlselect);
            $exec = $statement->execute($params);
            $cache_result = $statement->fetchAll();
            $this->core->mcc->add($cache_name, $cache_result);
        }

        return $cache_result;
    }
}
