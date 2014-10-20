<?php

$app->get('/silabus', function () use ($app) {
    $silabus = new SAR\models\Silabus();
    $result = $silabus->selectAll();
    var_dump($result);
});
