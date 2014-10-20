<?php

/**
* PDO Caching Mechanism
* @return $cache_result object
*/
class cache
{

    function cache_query($sqlselect, $params)
    {
        $db = $c['db'];
        $db = $c['cache'];
        $cache_name = 'querycache-' . md5(serialize(array($sqlselect, $params)));
        $cache_result = $cache->get($cache_name);

        if (!$cache_result) {
            $statement = $db->prepare($sqlselect);
            $exec = $statement->execute($params);
            $cache_result = $statement->fetchAll();
            $cache->add($cache_name, $cache_result);
        }

        return $cache_result;
    }
}
