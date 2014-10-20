<?php

/**
* Silabus
*
*/
class silabus
{

    function selectAll()
    {
        $result = cache_query('SELECT * FROM SILABUS');
        return $result;
    }
}
