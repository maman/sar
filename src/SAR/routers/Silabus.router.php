<?php

use SAR\models\Silabus;

$app->get('/silabus', function () use ($app) {
    $silabus = new Silabus();
    $result = $silabus->selectAll();
    var_dump($result);
});
